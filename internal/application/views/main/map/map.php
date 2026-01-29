<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
        <h4><?= $title ?></h4>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div id="map" style="width: 100%; height: 82%; position: relative;">
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
  </section>
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
  }, 15000); // 15 second timeout
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