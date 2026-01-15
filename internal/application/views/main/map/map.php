<style>

@keyframes spin {
  to { transform: rotate(360deg); }
}

.ol-legend, .custom-zoom {
  position: absolute;
  z-index: 1000;
  background: rgba(255,255,255,0.9);
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  font-family: monospace;
}
/* Desktop */
@media screen and (min-width: 1024px) {
  .ol-legend {
    bottom: 0.4rem;
    left: 0.4rem;
    padding: 6px 10px;
    font-size: 12px;
  }

  .search-box {
    position: absolute;
    z-index: 1000;
    top: 0;
    left: 0;
    width: 220px;
    padding: 6px;
  }
}

/* Tablet */
@media screen and (min-width: 768px) and (max-width: 1023px) {
  .ol-legend {
    bottom: 60px;
    left: 260px;
    padding: 6px 10px;
    font-size: 12px;
  }

  .search-box {
    position: absolute;
    z-index: 1000;
    top: 70px;
    left: 260px;
    width: 220px;
    padding: 6px;
  }
}

/* Mobile */
@media screen and (max-width: 767px) {
  .ol-legend {
    bottom: 60px;
    left: 7.5px;
    padding: 6px 10px;
    font-size: 12px;
  }

  .search-box {
    position: absolute;
    z-index: 1000;
    top: 70px;
    left: 7.5px;
    width: 220px;
    padding: 6px;
  }
}
.custom-zoom {
  height: 125px;
  width: 70px;
  top: 0.4rem;
  right: 0.4rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 10px;
}
.custom-zoom button {
  background: rgba(255,255,255,0.9);
  border: 1px solid #ccc;
  border-radius: 4px;
  width: 30px;
  height: 35px;
  font-weight: bold;
  font-size: 16px;
  color: #333;
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
#zoom-level {
  font-size: 11px;
  font-weight: bold;
  color: #333;
  margin-top: 2px;
}

.ol-viewport .custom-attribution { 
  position: absolute !important;
  bottom: 8px;
  right: 8px;
  left: auto;
  top: auto;
  background: rgba(255,255,255,0.8);
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-family: 'Arial', sans-serif;
  color: #333;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
  pointer-events: none; 
  display: flex !important;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  align-content: center;
  flex-wrap: nowrap;
  gap: 6px;
}

.custom-attribution ul {
  margin: 0;
  padding: 0;
  list-style: none;
  display: inline-block;
}

.custom-attribution ul li {
  display: inline; /* biar teks attribution di 1 baris */
}

/* Hapus style default button bawaan OL */
.custom-attribution button {
  border: none;
  background: transparent;
  padding: 0;
  margin: 0;
  font-size: 12px;
  cursor: pointer;
  color: #555;
}

/* Style kecil buat "i" biar manis */
.custom-attribution-expand {
  display: inline-block;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #007bff;
  color: #fff;
  font-weight: bold;
  line-height: 16px;
  text-align: center;
  font-size: 11px;
}

/* SEARCH BOX */
#search-input {
  width: 100%;
  height: 2.5rem;
  padding: 5px 8px;
  border: 1px solid #aaa;
  border-radius: 4px;
  font-size: 13px;
}
#search-results {
  margin-top: 5px;
  max-height: 200px;
  overflow-y: auto;
  background: white;
  border-radius: 4px;
  border: 1px solid #ccc;
  display: none;
}
#search-results div {
  padding: 5px 8px;
  cursor: pointer;
}
#search-results div:hover {
  background: #f0f0f0;
}

.ol-popup div::-webkit-scrollbar {
  width: 6px;
}
.ol-popup div::-webkit-scrollbar-thumb {
  background-color: rgba(0,0,0,0.3);
  border-radius: 3px;
}


.movement-notif {
  position: fixed;
  top: 10px;
  right: 10px;
  background: white;
  border-left: 4px solid green;
  padding: 10px;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  font-size: 14px;
  z-index: 9999999999;
  animation: notifFadeIn 0.3s ease-out;
  transition: opacity 0.5s ease, transform 0.5s ease;
}

@keyframes notifFadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}


</style>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
        <h4><?= $title ?></h4>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div id="map" style="width: 100%; height: 82%; position: relative;">
        <!-- Map content muncul di sini -->
        <div id="map-loader" style="
          position: absolute;
          inset: 0;
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 10px;
          background: rgba(255,255,255,0.7);
          backdrop-filter: blur(3px);
          z-index: 9999;
          font-size: 14px;
          font-weight: 600;
        ">
          <div class="spinner" style="
            width: 36px;
            height: 36px;
            border: 4px solid rgba(0,0,0,0.2);
            border-top-color: rgba(0,0,0,0.7);
            border-radius: 50%;
            animation: spin 1s linear infinite;
          "></div>
          <span>Loading map data...</span>
        </div>

        <!-- Legend dan Zoom Control -->
        <div id="coord-legend" class="ol-legend">Lat: -, Lon: -</div>
        <div class="custom-zoom">
          <button id="zoom-in">+</button>
          <button id="zoom-out">−</button>
          <div id="zoom-level">Zoom: 12</div>
        </div>

        <!-- Search Box -->
        <div class="search-box">
          <input id="search-input" type="text" placeholder="Search for Vehicles..." autocomplete="off" />
          <div id="search-results"></div>
        </div>

        <!-- Tooltip -->
        <div id="hover-tooltip" 
            style="position:absolute;
                    background:white;
                    padding:4px 6px;
                    border:1px solid #ccc;
                    border-radius:4px;
                    font-size:12px;
                    pointer-events:none;
                    display:none;">
        </div>


      </div>
    </div>
  </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/10.6.1/dist/ol.min.js" 
integrity="sha512-NEUbbO7KI1OYn+IHcF70vm3ON0obczJz9PJFwxHkfPCsT14UqDD4roG7rF5WpwkXRTPvysFb6Wvw/Tjh5tfv8g==" 
crossorigin="anonymous" 
referrerpolicy="no-referrer"
defer
></script>


<script>
  const objectsMergeUrl = "<?= base_url('v1/api/traxroot/objectsMerge') ?>";
</script>
<script src="<?php echo base_url('assets/map/map.js') ?>" defer></script>