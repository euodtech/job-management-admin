// === SOURCE LAYER ===
  const vectorSource = new ol.source.Vector(); // geozones
  const objectSource = new ol.source.Vector(); // vehicles

  const vectorLayer = new ol.layer.Vector({ source: vectorSource });
  const objectLayer = new ol.layer.Vector({ source: objectSource });

  const rasterLayer = new ol.layer.Tile({ 
    source: new ol.source.OSM({
      attributions: [
        'Map data © <a href="https://traxroot.com" target="_blank">Traxroot</a> / ' +
        '<a href="https://www.openstreetmap.org/" target="_blank">OpenStreetMap</a>'
      ],
      url: 'https://{a-c}.tile.openstreetmap.org/{z}/{x}/{y}.png'
    }) 
  });

  // === INIT MAP ===
  const map = new ol.Map({
    target: "map",
    layers: [rasterLayer, vectorLayer, objectLayer],
    view: new ol.View({
      center: ol.proj.fromLonLat([121.051184, 14.623512]),
      zoom: 12,
    }),
    controls: ol.control.defaults.defaults({
      zoom: false,         // Hilangkan tombol zoom bawaan
      attribution: false,  // Hilangkan "© OpenStreetMap contributors"
      rotate: false        // Hilangkan tombol rotasi
    }).extend([
      new ol.control.Attribution({
        collapsible: false,
        collapsed: false,
        className: 'custom-attribution',
        target: null,
      }),
      new ol.control.FullScreen(),
      new ol.control.ScaleLine(), 
    ])
  });
  

  // === CLUSTER SOURCE ===
  const clusterSource = new ol.source.Cluster({
    distance: 60, // Jarak cluster
    source: objectSource,
  });

  // === CLUSTER STYLE ===
  const clusterStyle = (feature) => {
    const size = feature.get('features').length;
    const zoom = map.getView().getZoom();
    const scale = Math.max(0.6, Math.min(1.2, (zoom - 5) / 10));

    // Batasi radius maksimal agar tidak terlalu besar
    const radius = Math.min(10 + Math.log(size) * 4 * scale, 25); 

    return new ol.style.Style({
      image: new ol.style.Circle({
        radius,
        fill: new ol.style.Fill({ color: 'rgba(0,153,255,0.6)' }),
        stroke: new ol.style.Stroke({ color: '#0066cc', width: 2 }),
      }),
      text: new ol.style.Text({
        text: size.toString(),
        fill: new ol.style.Fill({ color: '#fff' }),
        font: 'bold 12px Arial',
      }),
    });
  };


  // === CLUSTER LAYER ===
  const clusterLayer = new ol.layer.Vector({
    source: clusterSource,
    style: (feature, resolution) => clusterStyle(feature, resolution),
  });

  if (!map.getLayers().getArray().includes(clusterLayer)) {
    map.addLayer(clusterLayer);
  }
  if (!map.getLayers().getArray().includes(objectLayer)) {
    map.addLayer(objectLayer);
  }
  

  let allObjects = [];

  // === FETCH DATA MERGE ===
  fetch(objectsMergeUrl)
  .then(res => res.json())
  .then(data => {
    // ==== PASTIKAN PARSING JIKA DOUBLE ENCODE ====
    if (typeof data.profile === "string") data.profile = JSON.parse(data.profile);
    if (typeof data.status === "string") data.status = JSON.parse(data.status);
    if (typeof data.geozones === "string") data.geozones = JSON.parse(data.geozones);
    if (typeof data.icons === "string") data.icons = JSON.parse(data.icons);
    
    // --- GEZONES ---
    vectorSource.clear();
    if (Array.isArray(data.geozones)) {
      data.geozones.forEach(zone => {
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

    // --- OBJECTS (DRIVERS) ---
    objectSource.clear();

    if (data.status?.points && Array.isArray(data.status.points)) {
      let profiles = data.profile?.objects || [];
      let icons = Array.isArray(data.icons) ? data.icons : [];
      const baseUrl = "https://connect.traxroot.com";

      data.status.points.forEach(point => {
        if (!point.lat || !point.lng) return;

        const lonLat = [parseFloat(point.lng), parseFloat(point.lat)];
        const coord = ol.proj.fromLonLat(lonLat);

        let objProfile = profiles.find(p => String(p.id) === String(point.trackerid));
        let iconDef = objProfile?.iconid
          ? icons.find(ic => String(ic.id) === String(objProfile.iconid))
          : null;
        // console.log(objProfile);
        // console.log(point);

        let status = "active";

        // Jika tidak ada satelit → dianggap disabled
        if (parseInt(point.sat) === 0) {
          status = "disabled";
        }
        // Jika satelit ada tapi kecepatan = 0 → dianggap inactive
        else if (parseFloat(point.speed) === 0) {
          status = "inactive";
        }

        let iconPath = "";

        if (iconDef) {
          if (status === "inactive" && iconDef.urlCross) {
            iconPath = iconDef.urlCross;
          } else if (status === "disabled" && iconDef.urlDisabled) {
            iconPath = iconDef.urlDisabled;
          } else if (iconDef.url) {
            iconPath = iconDef.url; // default aktif
          }
        }

        // kalau tetep kosong (misal data icons-nya belum lengkap)
        if (!iconPath) return; // skip daripada error

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
        }));

        objectSource.addFeature(marker);
        vehicleMarkers[point.trackerid] = marker;
        allObjects.push(marker);
      });


      // === ZOOM-LEVEL ICON CONTROL ===
      const toggleIconVisibility = () => {
        const zoom = map.getView().getZoom();
        // Icon hanya muncul saat zoom > 15
        // clusterLayer.setVisible(zoom <= 18); // Cluster visible when zoom is smaller or equal to 15
        // objectLayer.setVisible(zoom > 18);   // Icon visible when zoom is greater than 15
        if (zoom > 15) {
          objectLayer.setVisible(true); // Menampilkan ikon individu
          clusterLayer.setVisible(false); // Menyembunyikan cluster
        } 
        // Jika zoom lebih kecil atau sama dengan 15, tampilkan cluster
        else {
          objectLayer.setVisible(false); // Menyembunyikan ikon individu
          clusterLayer.setVisible(true); // Menampilkan cluster
        }
      };

      // Jalankan saat awal
      toggleIconVisibility();

      // Jalankan setiap kali zoom berubah
      map.getView().on('change:resolution', toggleIconVisibility);

      // === FIT VIEW ===
      // const extent = vectorSource.getExtent();
      // if (extent && extent[0] !== Infinity) {
      //   map.getView().fit(extent, { padding: [50, 50, 50, 50] });
      // }
    }
  })
  .catch(err => {
    console.error("Error loading data:", err);
    alert("Failed to load data from the server!");
  })
  .finally(() => {
    // SEMBUNYIKAN LOADER SETELAH SEMUA PROSES SELESAI
    document.getElementById("map-loader").style.display = "none";
  });


  // ===============================================
  // === REALTIME MOVEMENT ENGINE (FULL VERSION) ===
  // ===============================================

  // Simpan marker berdasarkan trackerid
  const vehicleMarkers = {};

  // Simpan status sebelumnya (moving / stopped)
  const vehicleStatus = {}; 

  // Simpan histori per kendaraan
  const movementHistory = [];

  // Fungsi animasi smooth movement (tanpa merombak OL)
  function animateMarker(feature, newCoord) {
    const geom = feature.getGeometry();
    const start = geom.getCoordinates();
    const end = newCoord;

    const duration = 800;
    const startTime = Date.now();

    function step() {
      const elapsed = Date.now() - startTime;
      const t = Math.min(elapsed / duration, 1);

      const x = start[0] + (end[0] - start[0]) * t;
      const y = start[1] + (end[1] - start[1]) * t;
      geom.setCoordinates([x, y]);

      if (t < 1) requestAnimationFrame(step);
    }

    requestAnimationFrame(step);
  }

  // =====================================================
  // === UPDATE DATA SETIAP 5 DETIK TANPA MEROMBAK KODE ===
  // =====================================================
  function updateVehicles() {
    fetch(objectsMergeUrl)
      .then(res => res.json())
      .then(data => {
        if (typeof data.profile === "string") data.profile = JSON.parse(data.profile);
        if (typeof data.status === "string") data.status = JSON.parse(data.status);
        if (typeof data.icons === "string") data.icons = JSON.parse(data.icons || "[]");

        if (!data.status?.points) return;

        const profiles = data.profile?.objects || [];
        const icons = Array.isArray(data.icons) ? data.icons : [];
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

          // Jika sebelumnya speed = 0 dan sekarang > 0 → kendaraan mulai bergerak
          if (prevSpeed === 0 && newSpeed > 0) {
              const entryTime = new Date(parseInt(point.time));
              const timestamp = entryTime.toLocaleString("en-GB", { 
                day: "2-digit", month: "2-digit", year: "numeric",
                hour: "2-digit", minute: "2-digit"
              });

              const name = marker.get("name");

              const entry = {
                  name: name,
                  time: timestamp,
                  status: "Started moving"
              };

              movementHistory.push(entry); // simpan di paling atas
              showMovementNotification(entry); // munculkan popup notifikasi
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

          // NEW MARKER
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

          // Fungsi reverse geocoding OSM
          function getAddressOSM(lat, lng) {
            if (!lat || !lng) return Promise.resolve("");
            const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
            return fetch(url)
              .then(res => res.json())
              .then(data => data.display_name || "")
              .catch(err => {
                console.error("Error reverse geocoding:", err);
                return "";
              });
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

          // Setelah marker dibuat, panggil reverse geocoding
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
      })
      .catch(err => console.error("Update error:", err));
  }

  // =====================================================
  // === JALANKAN UPDATE SETIAP  DETIK (REALTIME)      ===
  // =====================================================
  setInterval(updateVehicles, 20000);

  // Panggil sekali diawal
  setTimeout(updateVehicles, 2000);

  // === SHOW MOVEMENT NOTIFICATION ===
  function showMovementNotification(entry) {
    const notif = document.createElement("div");
    notif.className = "movement-notif";
    notif.innerHTML = `
        <b>${entry.name}</b> — ${entry.time}<br>
        <span style="color:green;">${entry.status}</span>
    `;

    document.body.appendChild(notif);

    setTimeout(() => {
        notif.style.opacity = "0";
        notif.style.transform = "translateY(-10px)";
        setTimeout(() => notif.remove(), 500);
    }, 3000);
  }

  // === POPUP ===
  const container = document.createElement("div");
  container.className = "ol-popup";
  container.style.cssText = "background:white; padding:5px; border:1px solid #ccc; border-radius:4px; display: flex; flex-direction: column; flex-wrap: wrap; align-items: flex-start; align-content: flex-start; justify-content: center; gap: 5px;";
  const overlay = new ol.Overlay({
    element: container,
    positioning: "bottom-center", 
    offset: [0, -60],
    stopEvent: true,
  });
  map.addOverlay(overlay);


  // === TOOLTIP HOVER ===
  const hoverTooltip = document.getElementById("hover-tooltip");
  const hoverOverlay = new ol.Overlay({
    element: hoverTooltip,
    offset: [10, 10],
    positioning: "top-left"
  });
  map.addOverlay(hoverOverlay);

  // === POINTER MOVE (HOVER) ===
  map.on("pointermove", function (evt) {
    const feature = map.forEachFeatureAtPixel(evt.pixel, function (feature) {
      return feature;
    });
    if (feature) {
      const features = feature.get('features');
      let name = "";
      if (features && features.length > 1) {
        name = `${features.length} Vehicles`;
      }
      else if (features && features.length === 1) {
        name = features[0].get('name') || 'Unknown';
      }
      else {
        name = feature.get('name') || 'Unknown';
      }
      hoverTooltip.innerHTML = name;
      hoverOverlay.setPosition(evt.coordinate);
      hoverTooltip.style.display = "block";
    } else {
      hoverTooltip.style.display = "none";
    }
  });

  // === POPUP CLICK ===
  



  // Cegah klik & scroll di dalam popup menutup overlay
  container.addEventListener("wheel", e => e.stopPropagation());
  container.addEventListener("mousedown", e => e.stopPropagation());
  container.addEventListener("click", e => e.stopPropagation());
  container.addEventListener("touchstart", e => e.stopPropagation());

  map.on("singleclick", function (evt) {
    // Sembunyikan popup dulu setiap kali klik map
    overlay.setPosition(undefined);

    // Cek apakah klik mengenai feature
    const feature = map.forEachFeatureAtPixel(evt.pixel, function (feature) {
      return feature;
    });

    // Kalau tidak ada feature yang diklik → langsung return
    if (!feature) return;

    const features = feature.get('features');

    if (features && features.length > 1) {
      // === ini cluster ===
      let list = `
        <div style="display: flex;
          justify-content: flex-start;
          align-items: center;
          flex-direction: row;
          flex-wrap: nowrap;
          align-content: center;
          font-size: 1.25rem;
          font-style: normal;
          font-weight: bolder;">
          <b>📍 ${features.length} Vehicles</b>
        </div>
        <div style="
          max-height: 200px;
          min-height: 200px;
          overflow-y: auto;
          padding-right: 5px;
          scrollbar-width: thin;
          max-width: 275px;
          min-width: 275px;
        ">
        <ul style='margin:0;padding-left:18px;'>
        `;
        features.forEach(f => {
          const name = f.get('name') || 'Unknown';
          const driver = f.get('driver') || '-';
          const speed = f.get('speed') || '0';
          const comment = f.get('comment') || '';
          const address  = f.get('address') || 'Loading...';
          list += `
            <li style="margin-bottom:4px;">
              <b>${name}</b><br>
              Driver: ${driver}<br>
              Speed: ${speed} km/h<br>
              Address: ${address}<br>
              <i>${comment}</i>
            </li>
        `;
      });

      list += "</ul></div>";

      container.innerHTML = list;
      container.style.maxWidth = "300px";
      container.style.minWidth = "270px";
      // container.style.maxHeight = "300px"; 
      // container.style.minHeight = "270px"; 
      container.style.overflow = "hidden";
      overlay.setPosition(evt.coordinate);
    } 
    else if (features && features.length === 1) {
      // === ini single point di dalam cluster ===
      const f = features[0];
      const props = f.getProperties();

      // let content = `<b>${props.name || 'Unknown'}</b>`;
      // if (props.driver) content += `<p style="margin:0">Driver: ${props.driver}</p>`;
      // if (props.comment) content += `<p style="margin:0">${props.comment}</p>`;
      // if (props.speed !== undefined) content += `<p style="margin:0">Speed: ${props.speed} km/h</p>`;

      let content = `
        <b>${props.name || 'Unknown'}</b> (${props.trackerid})<br>
        Comment: ${props.comment || '-'}<br>
        Device: ${props.device || '-'}<br>
        Phone: ${props.phone || '-'}<br>
        Address: ${props.address || 'Loading...'}<br>
        Time: ${new Date(props.time || Date.now()).toLocaleString('en-GB')}<br>
        Coordinates: ${props.lat.toFixed(6)}, ${props.lon.toFixed(6)}<br>
        Speed: ${props.speed} km/h<br>
        Satellites: ${props.sat}<br>
        Parking: ${props.parkingDuration}<br>
        GSM Signal: ${props.gsmSignal}<br>
        Ignition: ${props.ignitionSensor}<br>
        Coolant Temp: ${props.coolantTemp}<br>
        Device Battery: ${props.deviceBattery}<br>
      `;

      container.innerHTML = content;
      // container.style.maxHeight = "125px";
      // container.style.minHeight = "125px"; 
      overlay.setPosition(evt.coordinate);
    } 
    else {
      // === non-cluster feature biasa ===
      const props = feature.getProperties();
      let content = `<b>${props.name || 'Unknown'}</b>`;
      if (props.driver) content += `<p style="margin:0">Driver: ${props.driver}</p>`;
      if (props.comment) content += `<p style="margin:0">${props.comment}</p>`;
      if (props.speed !== undefined) content += `<p style="margin:0">Speed: ${props.speed} km/h</p>`;

      container.innerHTML = content;
      // container.style.maxHeight = "125px";
      // container.style.minHeight = "125px"; 
      overlay.setPosition(evt.coordinate);
    }
  });


  // === COORDINATE LEGEND ===
  const coordLegend = document.getElementById('coord-legend');
  map.on('pointermove', evt => {
    const coord = ol.proj.toLonLat(evt.coordinate);
    coordLegend.textContent = `Lat: ${coord[1].toFixed(5)}, Lon: ${coord[0].toFixed(5)}`;
  });

  // === CUSTOM ZOOM CONTROL + ZOOM LEVEL ===
  const zoomLevel = document.getElementById('zoom-level');
  const updateZoomLevel = () => {
    zoomLevel.textContent = `Zoom: ${map.getView().getZoom().toFixed(1)}`;
  };
  map.getView().on('change:resolution', updateZoomLevel);
  updateZoomLevel();

  document.getElementById('zoom-in').onclick = () => {
    map.getView().setZoom(map.getView().getZoom() + 1);
  };
  document.getElementById('zoom-out').onclick = () => {
    map.getView().setZoom(map.getView().getZoom() - 1);
  };


  // === SEARCH FEATURE ===
  const searchInput = document.getElementById("search-input");
  const searchResults = document.getElementById("search-results");

  searchInput.addEventListener("input", () => {
    const q = searchInput.value.trim().toLowerCase();
    searchResults.innerHTML = "";
    if (!q) return (searchResults.style.display = "none");
    
    const results = allObjects.filter(o => {
      const name = (o.get("name") || "").toLowerCase();
      const driver = (o.get("driver") || "").toLowerCase();
      const comment = (o.get("comment") || "").toLowerCase();
      return name.includes(q) || driver.includes(q) || comment.includes(q);
    });

    if (results.length === 0) {
      searchResults.innerHTML = "<div>Not found</div>";
    } else {
      results.slice(0, 20).forEach(o => {
        const div = document.createElement("div");
        div.innerHTML = `
          <strong>${o.get("name")}</strong><br>
          <div style="font-size:11px;color:#555;">
            Driver: ${o.get("driver") || "-"}<br>
            ${o.get("comment") || ""}
          </div>
        `;
        div.addEventListener("click", () => {
          const coords = o.getGeometry().getCoordinates();
          // arahkan ke titik
          map.getView().animate({ center: coords, zoom: 18, duration: 800 });

          // tampilkan popup di titik
          const props = o.getProperties();
          let content = `<b>${props.name}</b>`;
          if (props.driver) content += `Driver: ${props.driver}`;
          if (props.comment) content += `<br/>${props.comment}`;
          if (props.speed !== undefined) content += `<br/>Speed: ${props.speed} km/h`;

          container.innerHTML = content;
          overlay.setPosition(coords);

          // tutup hasil pencarian
          searchResults.style.display = "none";
          searchInput.value = "";
        });

        searchResults.appendChild(div);
      });
    }

    searchResults.style.display = "block";
  
  });

  // === TUTUP SEARCH BOX SAAT USER KLIK SEMBARANG DI MAP ===
  map.on("click", function (evt) {
    const searchBox = document.querySelector(".search-box");
    const target = evt.originalEvent.target;
    const searchResults = document.getElementById("search-results");
    const searchInput = document.getElementById("search-input");

    // Tutup hanya kalau klik di luar elemen search box
    if (!searchBox.contains(target)) {
      searchResults.style.display = "none";
      // searchInput.value = "";
    }
  });

