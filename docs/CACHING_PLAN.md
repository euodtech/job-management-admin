# Plan: Traxroot API Caching Optimization (Split Endpoint + ETag + localStorage)

## Context

**E-FMS** is a fleet management system with a CodeIgniter 3 internal dashboard at `c:\xampp\htdocs\be-fms\internal\`. It integrates with the **Traxroot GPS tracking API** (`https://connect.traxroot.com/api/`) to display real-time vehicle positions on an OpenLayers map.

### Problem
The map page currently fetches **all 6 Traxroot API resources (~811 KB)** as a single merged response on every 20-second poll interval, even though only `ObjectsStatus` (live GPS positions) actually changes frequently. The other 5 resources (objects metadata, drivers, geozones, icons, profile) are semi-static and rarely change. There is **no client-side caching** — navigating away and returning forces a complete re-fetch. Search only works after the full initial load completes. The Vehicle DataTable page has **zero caching** and hits the Traxroot API directly on every request.

### Goal
- Split the monolithic `objectsMerge` endpoint into **static data** vs **live status** endpoints
- Cache semi-static data in **localStorage** for instant page render and immediate search
- Use **ETag/hashing** so the client knows when static data has changed
- Reduce 20-second poll payload from ~811 KB to just a few KB (status only)
- Add file caching to the Vehicle DataTable page
- Keep the existing `objectsMerge` endpoint for backward compatibility

### Data Profile (from actual cache files)
| Resource | Approx Size | Current TTL | Change Frequency |
|----------|-------------|-------------|------------------|
| objects.json | ~475 KB | 60s | Rarely (vehicle metadata) |
| profile.json | ~320 KB | 60s | Rarely |
| icons.json | ~16 KB | 300s | Almost never |
| geozones.json | ~434 B | 300s | Rarely |
| drivers.json | ~2 B | 60s | Rarely |
| **status (ObjectsStatus)** | varies | **never cached** | **Every few seconds** |

---

## Files to Modify (5 files)

| # | File | Action |
|---|------|--------|
| 1 | `internal/application/controllers/API/V1/ObjectsMerge.php` | Add `getStaticData()`, `getLiveStatus()`, `_computeStaticHash()` methods |
| 2 | `internal/application/config/routes.php` | Add 2 new routes |
| 3 | `internal/application/views/main/map/map.php` | Expose 2 new endpoint URLs |
| 4 | `internal/assets/map/map.js` | Refactor to phased loading with localStorage cache |
| 5 | `internal/application/controllers/Vehicle.php` | Add file caching for Objects data |

---

## Step 1: Add New Endpoints to ObjectsMerge.php

**File**: `c:\xampp\htdocs\be-fms\internal\application\controllers\API\V1\ObjectsMerge.php`

This file currently has one main method `getObjectMerge()` (line 17) that fetches all 6 resources, caches 5 of them as files, and always fetches ObjectsStatus fresh. It extends `ApiToken` (which provides `getApiToken()`, `requestCurl()`, `requestCurlMulti()`).

**Add these 3 new methods** before the existing `_getCacheDir()` method (before line 109):

### Method 1: `getStaticData()`
Returns objects + drivers + geozones + icons + profile (no status). Supports ETag via `If-None-Match` header for 304 responses. Uses increased TTLs (120s for objects/drivers, 600s for geozones/icons/profile).

```php
public function getStaticData()
{
    $token = $this->getApiToken();
    if (!$token) {
        return $this->_badRequest('Tidak ada API token, silakan login Auth dulu.');
    }

    $TTL_SHORT  = 120;   // Objects, Drivers: 2 minutes
    $TTL_MEDIUM = 600;   // Geozones, Icons, Profile: 10 minutes

    $cacheable = array(
        'objects'  => array('url' => 'https://connect.traxroot.com/api/Objects',       'ttl' => $TTL_SHORT),
        'drivers'  => array('url' => 'https://connect.traxroot.com/api/Drivers',       'ttl' => $TTL_SHORT),
        'geozones' => array('url' => 'https://connect.traxroot.com/api/Geozones',      'ttl' => $TTL_MEDIUM),
        'icons'    => array('url' => 'https://connect.traxroot.com/api/Objects/Icons',  'ttl' => $TTL_MEDIUM),
        'profile'  => array('url' => 'https://connect.traxroot.com/api/Profile',        'ttl' => $TTL_MEDIUM),
    );

    $bodies = array();
    $urlsToFetch = array();

    foreach ($cacheable as $key => $cfg) {
        $cacheFile = $this->_getCacheDir() . $key . '.json';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cfg['ttl']) {
            $bodies[$key] = file_get_contents($cacheFile);
        } else {
            $urlsToFetch[$key] = $cfg['url'];
        }
    }

    if (!empty($urlsToFetch)) {
        $responses = $this->requestCurlMulti($urlsToFetch);
        foreach ($responses as $key => $resp) {
            $bodies[$key] = $resp['body'];
            if ($resp['success'] && $resp['body']) {
                $cacheFile = $this->_getCacheDir() . $key . '.json';
                $tmpFile = $cacheFile . '.tmp';
                file_put_contents($tmpFile, $resp['body']);
                if (file_exists($cacheFile)) {
                    @unlink($cacheFile);
                }
                @rename($tmpFile, $cacheFile);
            }
        }
    }

    // Fall back to stale cache for any failed fetches
    foreach ($cacheable as $key => $cfg) {
        if (empty($bodies[$key])) {
            $cacheFile = $this->_getCacheDir() . $key . '.json';
            if (file_exists($cacheFile)) {
                $bodies[$key] = file_get_contents($cacheFile);
            }
        }
    }

    $objectsJson  = json_decode($bodies['objects'] ?? '', true)  ?? [];
    $driversJson  = json_decode($bodies['drivers'] ?? '', true)  ?? [];
    $geozonesJson = json_decode($bodies['geozones'] ?? '', true) ?? [];
    $iconsJson    = json_decode($bodies['icons'] ?? '', true)    ?? [];
    $profileJson  = json_decode($bodies['profile'] ?? '', true)  ?? [];

    if (is_string($profileJson)) {
        $profileJson = json_decode($profileJson, true) ?? [];
    }

    $merged = json_encode([
        'objects'  => $objectsJson,
        'drivers'  => $driversJson,
        'geozones' => $geozonesJson,
        'icons'    => $iconsJson,
        'profile'  => $profileJson
    ]);

    // ETag support: compute hash of merged content
    $etag = '"' . md5($merged) . '"';

    // Check If-None-Match from client
    $clientEtag = $this->input->get_request_header('If-None-Match');
    if ($clientEtag && trim($clientEtag) === $etag) {
        $this->output->set_status_header(304);
        return;
    }

    $this->output
        ->set_header('ETag: ' . $etag)
        ->set_header('Cache-Control: private, max-age=120')
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output($merged);
}
```

### Method 2: `getLiveStatus()`
Returns only ObjectsStatus (always fresh) plus a `staticHash` so the client knows if static data changed.

```php
public function getLiveStatus()
{
    $token = $this->getApiToken();
    if (!$token) {
        return $this->_badRequest('Tidak ada API token, silakan login Auth dulu.');
    }

    $statusRes = $this->requestCurl('https://connect.traxroot.com/api/ObjectsStatus', 'GET');
    $statusBody = $statusRes['body'];
    $statusJson = json_decode($statusBody, true);

    if (is_string($statusJson)) {
        $statusJson = json_decode($statusJson, true) ?? [];
    }

    $staticHash = $this->_computeStaticHash();

    $body = json_encode([
        'status'     => $statusJson,
        'staticHash' => $staticHash
    ]);

    $this->output
        ->set_header('Cache-Control: no-cache, no-store')
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output($body);
}
```

### Method 3: `_computeStaticHash()` (private helper)
Computes an MD5 hash from the 5 cache files' modification times and sizes. This lets the client detect when static data has changed without downloading it.

```php
private function _computeStaticHash()
{
    $dir = $this->_getCacheDir();
    $files = array('objects.json', 'drivers.json', 'geozones.json', 'icons.json', 'profile.json');
    $combined = '';
    foreach ($files as $f) {
        $path = $dir . $f;
        if (file_exists($path)) {
            $combined .= filemtime($path) . ':' . filesize($path) . ';';
        }
    }
    return md5($combined);
}
```

**Keep the existing `getObjectMerge()` method unchanged** for backward compatibility.

---

## Step 2: Add New Routes

**File**: `c:\xampp\htdocs\be-fms\internal\application\config\routes.php`

Add these 2 lines **after the existing `objectsMerge` route on line 194**:

```php
$route['v1/api/traxroot/staticData']  = 'API/V1/ObjectsMerge/getStaticData';
$route['v1/api/traxroot/liveStatus']  = 'API/V1/ObjectsMerge/getLiveStatus';
```

Insert them after line 194 (`$route['v1/api/traxroot/objectsMerge'] = 'API/V1/ObjectsMerge/getObjectMerge';`).

---

## Step 3: Expose New URLs in map.php

**File**: `c:\xampp\htdocs\be-fms\internal\application\views\main\map\map.php`

In the `<script>` block at line 46-57, add the two new URL globals. Change the block from:

```html
<script>
  window.objectsMergeUrl = "<?= base_url('v1/api/traxroot/objectsMerge') ?>";
```

To:

```html
<script>
  window.objectsMergeUrl = "<?= base_url('v1/api/traxroot/objectsMerge') ?>";
  window.staticDataUrl   = "<?= base_url('v1/api/traxroot/staticData') ?>";
  window.liveStatusUrl   = "<?= base_url('v1/api/traxroot/liveStatus') ?>";
```

---

## Step 4: Refactor map.js with Phased Loading + localStorage Cache

**File**: `c:\xampp\htdocs\be-fms\internal\assets\map\map.js`

This is the largest change. The current file is 745 lines. The refactoring replaces the initial data fetch (lines 87-274) and the update loop (lines 316-452, 457-460) with a phased loading architecture. The rest of the file (map init, cluster config, popup/tooltip handlers, search, zoom controls) remains mostly the same.

### Architecture Overview

```
Page Load:
  Phase 1 (instant):  Render geozones + populate search index from localStorage
  Phase 2 (network):  Fetch /liveStatus → render vehicles using cached static + fresh positions
  Phase 3 (background): If staticHash changed → fetch /staticData with If-None-Match

Polling (every 20s):
  Fetch /liveStatus only (~few KB) → update marker positions
  If staticHash changed → refresh static data in background
```

### Detailed Changes

**Replace line 87 (`let allObjects = [];`) through line 274 (end of initial fetch `.finally()`)** with the new phased loading code below. Also **replace lines 316-460** (the `updateVehicles()` function and the `setInterval`/`setTimeout` calls) with the new polling code.

Here is the complete new code that replaces those sections:

#### New code block 1: Replace lines 87-274 (initial fetch) with:

```javascript
  let allObjects = [];

  // === LOCALSTORAGE CACHE LAYER ===
  const StaticCache = {
    KEY: 'efms_traxroot_static',
    HASH_KEY: 'efms_traxroot_hash',
    ETAG_KEY: 'efms_traxroot_etag',

    get() {
      try {
        const raw = localStorage.getItem(this.KEY);
        return raw ? JSON.parse(raw) : null;
      } catch (e) { return null; }
    },

    set(data, etag, hash) {
      try {
        localStorage.setItem(this.KEY, JSON.stringify(data));
        if (etag) localStorage.setItem(this.ETAG_KEY, etag);
        if (hash) localStorage.setItem(this.HASH_KEY, hash);
      } catch (e) { /* localStorage full — degrade gracefully */ }
    },

    getEtag() { return localStorage.getItem(this.ETAG_KEY); },
    getHash() { return localStorage.getItem(this.HASH_KEY); },

    clear() {
      localStorage.removeItem(this.KEY);
      localStorage.removeItem(this.HASH_KEY);
      localStorage.removeItem(this.ETAG_KEY);
    }
  };

  // === REUSABLE RENDER FUNCTIONS ===

  function renderGeozones(geozones) {
    vectorSource.clear();
    if (!Array.isArray(geozones)) return;

    geozones.forEach(zone => {
      if (!zone.points) return;

      const coords = zone.points.trim().split(" ").map(parseFloat);
      let formattedCoords = [];
      for (let i = 0; i < coords.length; i += 2) {
        const lat = coords[i];
        const lon = coords[i + 1];
        formattedCoords.push(ol.proj.fromLonLat([lon, lat]));
      }

      let feature;
      if (zone.style?.type === "polygon") {
        feature = new ol.Feature(new ol.geom.Polygon([formattedCoords]));
      } else if (zone.style?.type === "polyline") {
        feature = new ol.Feature(new ol.geom.LineString(formattedCoords));
      }

      if (feature) {
        feature.setProperties({
          name: zone.name || "Unnamed Zone",
          comment: zone.comment || "",
        });

        feature.setStyle(
          new ol.style.Style({
            stroke: new ol.style.Stroke({
              color: zone.style.strokeColor || "blue",
              width: zone.style.strokeWidth || 2,
            }),
            fill: zone.style.type === "polygon"
              ? new ol.style.Fill({
                  color: zone.style.fillColor
                    ? zone.style.fillColor + "33"
                    : "rgba(0,0,255,0.2)",
                })
              : null,
          })
        );

        vectorSource.addFeature(feature);
      }
    });
  }

  function renderVehicles(staticData, statusData) {
    objectSource.clear();
    allObjects = [];
    Object.keys(vehicleMarkers).forEach(k => delete vehicleMarkers[k]);

    if (!statusData?.points || !Array.isArray(statusData.points)) return;

    const profiles = staticData?.profile?.objects || [];
    const icons = Array.isArray(staticData?.icons) ? staticData.icons : [];
    const baseUrl = "https://connect.traxroot.com";

    statusData.points.forEach(point => {
      if (!point.lat || !point.lng) return;

      const lonLat = [parseFloat(point.lng), parseFloat(point.lat)];
      const coord = ol.proj.fromLonLat(lonLat);

      let objProfile = profiles.find(p => String(p.id) === String(point.trackerid));
      let iconDef = objProfile?.iconid
        ? icons.find(ic => String(ic.id) === String(objProfile.iconid))
        : null;

      let status = "active";
      if (parseInt(point.sat) === 0) {
        status = "disabled";
      } else if (parseFloat(point.speed) === 0) {
        status = "inactive";
      }

      let iconPath = "";
      if (iconDef) {
        if (status === "inactive" && iconDef.urlCross) {
          iconPath = iconDef.urlCross;
        } else if (status === "disabled" && iconDef.urlDisabled) {
          iconPath = iconDef.urlDisabled;
        } else if (iconDef.url) {
          iconPath = iconDef.url;
        }
      }
      if (!iconPath) return;

      const iconUrl = iconPath.startsWith("http") ? iconPath : baseUrl + iconPath;

      const marker = new ol.Feature({
        geometry: new ol.geom.Point(coord),
        name: objProfile?.name || "Tracker " + point.trackerid,
        driver: objProfile?.driver || "Unknown Driver",
        comment: objProfile?.comment || "",
        device: objProfile?.devicetype || "",
        phone: objProfile?.phone || "",
        speed: point.speed,
        sat: point.sat,
        lon: point.lng,
        lat: point.lat,
        parkingDuration: point.parkingDuration || "--",
        gsmSignal: point.IN21 ?? "--",
        ignitionSensor: point.IN239 === 1 ? "on" : "off",
        coolantTemp: point.IN32 ?? "--",
        deviceBattery: point.BATT ?? "--",
      });

      marker.setStyle(
        new ol.style.Style({
          image: new ol.style.Icon({
            anchor: [
              parseFloat(iconDef?.anchorx) || 0.5,
              parseFloat(iconDef?.anchory) || 1
            ],
            anchorXUnits: 'fraction',
            anchorYUnits: 'fraction',
            src: iconUrl,
            scale: 1,
            rotation: (point.ang || 0) * Math.PI / 180,
            rotateWithView: false
          })
        })
      );

      objectSource.addFeature(marker);
      vehicleMarkers[point.trackerid] = marker;
      allObjects.push(marker);
    });

    // Zoom-level icon control
    const toggleIconVisibility = () => {
      const zoom = map.getView().getZoom();
      if (zoom > 15) {
        objectLayer.setVisible(true);
        clusterLayer.setVisible(false);
      } else {
        objectLayer.setVisible(false);
        clusterLayer.setVisible(true);
      }
    };

    toggleIconVisibility();
    map.getView().on('change:resolution', toggleIconVisibility);
  }

  function populateSearchIndex(staticData) {
    // Pre-populate allObjects with basic data for search (no positions yet)
    // This is only used when we have cache but no live status yet
    if (allObjects.length > 0) return; // already populated from renderVehicles

    const profiles = staticData?.profile?.objects || [];
    profiles.forEach(p => {
      const marker = new ol.Feature({
        name: p.name || "Unknown",
        driver: p.driver || "",
        comment: p.comment || "",
      });
      allObjects.push(marker);
    });
  }

  // === STATIC DATA REFRESH (with ETag) ===
  let currentStaticData = null;

  function refreshStaticData(currentStatus) {
    const headers = {};
    const etag = StaticCache.getEtag();
    if (etag) headers['If-None-Match'] = etag;

    return fetch(staticDataUrl, { headers })
      .then(res => {
        if (res.status === 304) return null; // Not Modified — cache is still valid
        const newEtag = res.headers.get('ETag');
        return res.json().then(data => ({ data, etag: newEtag }));
      })
      .then(result => {
        if (!result) return; // 304
        // Parse double-encoded fields if needed
        if (typeof result.data.profile === "string") result.data.profile = JSON.parse(result.data.profile);
        if (typeof result.data.geozones === "string") result.data.geozones = JSON.parse(result.data.geozones);
        if (typeof result.data.icons === "string") result.data.icons = JSON.parse(result.data.icons || "[]");

        currentStaticData = result.data;
        StaticCache.set(result.data, result.etag, null);
        renderGeozones(result.data.geozones);
        if (currentStatus) {
          renderVehicles(result.data, currentStatus);
        }
      })
      .catch(err => console.error("Static data refresh error:", err));
  }

  // === PHASED LOADING ===

  // Phase 1: Instant render from localStorage cache
  const cachedStatic = StaticCache.get();
  if (cachedStatic) {
    // Parse double-encoded fields if needed
    if (typeof cachedStatic.profile === "string") cachedStatic.profile = JSON.parse(cachedStatic.profile);
    if (typeof cachedStatic.geozones === "string") cachedStatic.geozones = JSON.parse(cachedStatic.geozones);
    if (typeof cachedStatic.icons === "string") cachedStatic.icons = JSON.parse(cachedStatic.icons || "[]");

    currentStaticData = cachedStatic;
    renderGeozones(cachedStatic.geozones);
    populateSearchIndex(cachedStatic);
    document.getElementById("loader-text").textContent = "Updating live positions...";
  }

  // Phase 2: Fetch live status
  fetch(liveStatusUrl)
    .then(res => res.json())
    .then(data => {
      if (typeof data.status === "string") data.status = JSON.parse(data.status);

      if (currentStaticData) {
        // We have cached static data — render vehicles immediately
        renderVehicles(currentStaticData, data.status);
      }

      // Phase 3: Check if static data needs refresh
      const cachedHash = StaticCache.getHash();
      if (!currentStaticData || !cachedHash || data.staticHash !== cachedHash) {
        // Static data is stale or missing — fetch it
        refreshStaticData(data.status).then(() => {
          // Save the new hash from the liveStatus response
          if (data.staticHash) {
            try { localStorage.setItem(StaticCache.HASH_KEY, data.staticHash); } catch(e) {}
          }
          // If we didn't have cached static data, we now do — render vehicles
          if (!cachedStatic && currentStaticData) {
            renderVehicles(currentStaticData, data.status);
          }
        });
      } else {
        // Static data is still fresh — save hash
        if (data.staticHash) {
          try { localStorage.setItem(StaticCache.HASH_KEY, data.staticHash); } catch(e) {}
        }
      }
    })
    .catch(err => {
      console.error("Error loading data:", err);
      // Fallback: try original objectsMerge endpoint
      if (!currentStaticData) {
        fetch(objectsMergeUrl)
          .then(res => res.json())
          .then(data => {
            if (typeof data.profile === "string") data.profile = JSON.parse(data.profile);
            if (typeof data.status === "string") data.status = JSON.parse(data.status);
            if (typeof data.geozones === "string") data.geozones = JSON.parse(data.geozones);
            if (typeof data.icons === "string") data.icons = JSON.parse(data.icons || "[]");

            currentStaticData = { objects: data.objects, drivers: data.drivers, geozones: data.geozones, icons: data.icons, profile: data.profile };
            StaticCache.set(currentStaticData, null, null);
            renderGeozones(data.geozones);
            renderVehicles(currentStaticData, data.status);
          })
          .catch(fallbackErr => {
            console.error("Fallback also failed:", fallbackErr);
            alert("Failed to load data from the server!");
          });
      }
    })
    .finally(() => {
      document.getElementById("map-loader").style.display = "none";
    });
```

#### New code block 2: Replace lines 282-460 (vehicleMarkers declaration through setTimeout) with:

**Keep the existing `vehicleMarkers`, `vehicleStatus`, `movementHistory` declarations and `animateMarker()` function (lines 282-311) as-is.**

**Replace only lines 316-452 (the `updateVehicles()` function) and lines 457-460 (the setInterval/setTimeout) with:**

```javascript
  // === UPDATE: STATUS-ONLY POLLING ===
  function updateVehicles() {
    fetch(liveStatusUrl)
      .then(res => res.json())
      .then(data => {
        if (typeof data.status === "string") data.status = JSON.parse(data.status);
        if (!data.status?.points || !currentStaticData) return;

        const profiles = currentStaticData?.profile?.objects || [];
        const icons = Array.isArray(currentStaticData?.icons) ? currentStaticData.icons : [];
        const baseUrl = "https://connect.traxroot.com";

        const sortedPoints = data.status.points
          .filter(p => p.lat && p.lng)
          .sort((a, b) => parseInt(a.time) - parseInt(b.time));

        sortedPoints.forEach(point => {
          const id = point.trackerid;
          if (!id || !point.lat || !point.lng) return;

          const newCoord = ol.proj.fromLonLat([parseFloat(point.lng), parseFloat(point.lat)]);

          let marker = vehicleMarkers[id];

          const prevSpeed = parseFloat(marker?.get("speed") || 0);
          const newSpeed = parseFloat(point.speed || 0);

          // Movement detection notification
          if (prevSpeed === 0 && newSpeed > 0 && marker) {
            const entryTime = new Date(parseInt(point.time));
            const timestamp = entryTime.toLocaleString("en-GB", {
              day: "2-digit", month: "2-digit", year: "numeric",
              hour: "2-digit", minute: "2-digit"
            });

            const entry = {
              name: marker.get("name"),
              time: timestamp,
              status: "Started moving"
            };

            movementHistory.push(entry);
            showMovementNotification(entry);
          }

          if (marker) {
            animateMarker(marker, newCoord);
            marker.set("speed", point.speed);
            marker.set("lat", point.lat);
            marker.set("lng", point.lng);

            let style = marker.getStyle();
            if (style && style.getImage()) style.getImage().setRotation((point.ang || 0) * Math.PI / 180);
            return;
          }

          // NEW MARKER (vehicle appeared that wasn't in initial load)
          const objProfile = profiles.find(p => String(p.id) === String(id));
          const iconDef = objProfile?.iconid ? icons.find(i => String(i.id) === String(objProfile.iconid)) : null;

          let status = "active";
          if (parseInt(point.sat) === 0) status = "disabled";
          else if (parseFloat(point.speed) === 0) status = "inactive";

          let iconPath = "";
          if (iconDef) {
            if (status === "inactive" && iconDef.urlCross) iconPath = iconDef.urlCross;
            else if (status === "disabled" && iconDef.urlDisabled) iconPath = iconDef.urlDisabled;
            else if (iconDef.url) iconPath = iconDef.url;
          }
          if (!iconPath) return;
          const iconUrl = iconPath.startsWith("http") ? iconPath : baseUrl + iconPath;

          function getAddressOSM(lat, lng) {
            if (!lat || !lng) return Promise.resolve("");
            const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
            return fetch(url)
              .then(res => res.json())
              .then(data => data.display_name || "")
              .catch(() => "");
          }

          marker = new ol.Feature({
            geometry: new ol.geom.Point(newCoord),
            name: objProfile?.name || "Tracker " + id,
            driver: objProfile?.driver || "-",
            comment: objProfile?.comment || "",
            device: objProfile?.devicetype || "",
            phone: objProfile?.phone || "",
            parkingDuration: point.parkingDuration || "--",
            speed: point.speed,
            sat: point.sat,
            lat: point.lat,
            lon: point.lng,
            gsmSignal: point.IN21 ?? "--",
            ignitionSensor: point.IN239 === 1 ? "on" : "off",
            coolantTemp: point.IN32 ?? "--",
            deviceBattery: point.BATT ?? "--",
            address: "Loading..."
          });

          getAddressOSM(point.lat, point.lng).then(addr => {
            marker.set("address", addr);
          });

          marker.setStyle(new ol.style.Style({
            image: new ol.style.Icon({
              anchor: [parseFloat(iconDef?.anchorx) || 0.5, parseFloat(iconDef?.anchory) || 1],
              anchorXUnits: 'fraction',
              anchorYUnits: 'fraction',
              src: iconUrl,
              scale: 1,
              rotation: (point.ang || 0) * Math.PI / 180,
              rotateWithView: false
            })
          }));

          objectSource.addFeature(marker);
          vehicleMarkers[id] = marker;
          allObjects.push(marker);
        });

        clusterSource.refresh();

        // Check if static data needs refresh
        const cachedHash = StaticCache.getHash();
        if (data.staticHash && data.staticHash !== cachedHash) {
          refreshStaticData(data.status);
          try { localStorage.setItem(StaticCache.HASH_KEY, data.staticHash); } catch(e) {}
        }
      })
      .catch(err => console.error("Update error:", err));
  }

  // Poll every 20 seconds (status only — lightweight)
  setInterval(updateVehicles, 20000);

  // First update after 2 seconds
  setTimeout(updateVehicles, 2000);
```

**Important**: Lines 462-745 (showMovementNotification, popup, tooltip, coordinate legend, zoom controls, search) remain **completely unchanged**.

---

## Step 5: Add File Caching to Vehicle.php

**File**: `c:\xampp\htdocs\be-fms\internal\application\controllers\Vehicle.php`

The `traxrootVehicle()` method (line 29) currently calls `$this->requestCurl()` twice — once for Objects and once for ObjectsStatus — with zero caching. Since this controller extends `ApiToken`, it has access to `requestCurl()` and `requestCurlMulti()`.

**Replace lines 38-41** (the two `requestCurl` calls):

```php
        // Ambil dari API eksternal (Traxroot)
        $objects = $this->requestCurl('https://connect.traxroot.com/api/Objects', 'GET');
        $status  = $this->requestCurl('https://connect.traxroot.com/api/ObjectsStatus', 'GET');
```

With this cached version:

```php
        // Read Objects from file cache (shared with ObjectsMerge) or fetch fresh
        $username = $this->session->userdata('traxroot_username') ?: 'default';
        $safeUser = preg_replace('/[^a-zA-Z0-9_-]/', '_', $username);
        $cacheDir = APPPATH . 'cache' . DIRECTORY_SEPARATOR . 'traxroot' . DIRECTORY_SEPARATOR . $safeUser . DIRECTORY_SEPARATOR;
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $objectsCacheFile = $cacheDir . 'objects.json';
        $TTL_OBJECTS = 120; // 2 minutes

        if (file_exists($objectsCacheFile) && (time() - filemtime($objectsCacheFile)) < $TTL_OBJECTS) {
            $objects = array('success' => true, 'body' => file_get_contents($objectsCacheFile));
        } else {
            $objects = $this->requestCurl('https://connect.traxroot.com/api/Objects', 'GET');
            // Write to cache if successful
            if ($objects['success'] && $objects['body']) {
                $tmpFile = $objectsCacheFile . '.tmp';
                file_put_contents($tmpFile, $objects['body']);
                if (file_exists($objectsCacheFile)) {
                    @unlink($objectsCacheFile);
                }
                @rename($tmpFile, $objectsCacheFile);
            }
        }

        // Status is always fetched fresh (real-time GPS positions)
        $status = $this->requestCurl('https://connect.traxroot.com/api/ObjectsStatus', 'GET');
```

---

## Verification / Testing

### Manual Testing Checklist

1. **Cold start (empty localStorage)**:
   - Clear localStorage in browser DevTools
   - Navigate to Map page
   - Should behave like current behavior: loader shows, data fetches, map renders
   - After load, check localStorage — should now contain `efms_traxroot_static` key

2. **Warm start (cached localStorage)**:
   - Navigate away from Map page, then return
   - Geozones and search should render **instantly** (before network request)
   - Loader should say "Updating live positions..." instead of "Loading map data..."
   - Vehicle markers appear once liveStatus response arrives

3. **Search immediately**:
   - On warm start, type in search box **before** the live status response arrives
   - Should return results from cached vehicle names

4. **20-second polling**:
   - Open browser DevTools Network tab
   - Every 20 seconds, should see a request to `/v1/api/traxroot/liveStatus` (small payload)
   - Should **NOT** see requests to `/v1/api/traxroot/objectsMerge` (the old large payload)

5. **Static data refresh**:
   - Modify a vehicle name in Traxroot
   - Wait for file cache TTL to expire (120s)
   - On next poll, `staticHash` will differ → triggers background fetch to `/v1/api/traxroot/staticData`
   - Vehicle name updates on map

6. **ETag 304 response**:
   - In Network tab, after static data is cached, subsequent requests to `/v1/api/traxroot/staticData` should return `304 Not Modified` (no body)

7. **Vehicle DataTable page**:
   - Navigate to Vehicle page
   - First load should populate cache (if not already cached)
   - Subsequent loads / pagination / search should read Objects from file cache instead of calling Traxroot API

8. **Fallback on error**:
   - If `liveStatus` endpoint fails, the system should fall back to the original `objectsMerge` endpoint
   - If localStorage is unavailable (incognito mode in some browsers), should still work without caching

9. **Backward compatibility**:
   - The original `/v1/api/traxroot/objectsMerge` endpoint should still work exactly as before
