<!-- Content Header -->
<div class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-xl font-bold text-gray-800"><?= $title ?></h1>
        <nav class="flex">
            <ol class="flex items-center gap-1.5 text-sm">
                <?php foreach ($breadcrumbs as $i => $crumb): ?>
                    <?php if ($i > 0): ?>
                        <li class="text-gray-400">/</li>
                    <?php endif; ?>
                    <?php if ($crumb['url']): ?>
                        <li><a href="<?= $crumb['url'] ?>" class="text-primary hover:underline"><?= $crumb['label'] ?></a></li>
                    <?php else: ?>
                        <li class="text-gray-500"><?= $crumb['label'] ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>
</div>

<!-- Content -->
<div class="px-4 sm:px-6 lg:px-8 pb-6">
    <div id="map" style="width: 100%; height: 82%; position: relative; z-index: 0;">
        <div id="map-loader" class="map-loader">
          <div class="spinner"></div>
          <span id="loader-text">Loading map data...</span>
        </div>

        <div id="coord-legend" class="ol-legend">Lat: -, Lon: -</div>
        <div class="custom-zoom">
          <button id="zoom-in" aria-label="Zoom in">+</button>
          <button id="zoom-out" aria-label="Zoom out">−</button>
          <div id="zoom-level">Zoom: 12</div>
        </div>

        <div class="search-box">
          <input id="search-input" type="text" placeholder="Search for Vehicles..." autocomplete="off" />
          <div id="search-results"></div>
        </div>

        <div id="hover-tooltip" class="hover-tooltip"></div>
    </div>
</div>

<script>
  window.objectsMergeUrl = "<?= base_url('v1/api/traxroot/objectsMerge') ?>";

  // Timeout fallback
  setTimeout(function() {
    var loader = document.getElementById('map-loader');
    if (loader && loader.style.display !== 'none') {
      document.getElementById('loader-text').innerHTML =
        'Map loading timeout. <button onclick="location.reload()" style="padding:4px 12px;margin-left:8px;cursor:pointer;border-radius:4px;">Retry</button>';
    }
  }, 15000);
</script>

<!-- Preconnect to CDN -->
<link rel="preconnect" href="https://cdnjs.cloudflare.com">

<!-- Load OpenLayers with error handling -->
<script
  src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/10.6.1/dist/ol.min.js"
  integrity="sha512-NEUbbO7KI1OYn+IHcF70vm3ON0obczJz9PJFwxHkfPCsT14UqDD4roG7rF5WpwkXRTPvysFb6Wvw/Tjh5tfv8g=="
  crossorigin="anonymous"
  referrerpolicy="no-referrer"
  onerror="document.getElementById('loader-text').innerHTML='Failed to load map library. <button onclick=location.reload()>Retry</button>'">
</script>

<!-- Load map script with cache busting -->
<script src="<?= base_url('assets/map/map.js?v=' . filemtime(FCPATH . 'assets/map/map.js')) ?>"></script>
