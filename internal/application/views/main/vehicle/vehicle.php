
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h4><?= $title ?></h4>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h5 class="card-title">Vehicle</h5>
                </div>
                <div class="card-body">
                  <table id="vehicleTable" class="table table-striped table-striped table-bordered dt-responsive display responsive wrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Comment</th>
                            <th>Speed</th>
                            <th>Satellite</th>
                            <th>Latitude - Longitude</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables akan otomatis load data -->
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
  </section>
</div>



<script>
$(document).ready(function() {
  $('#vehicleTable').DataTable({
    scrollX: true,
    processing: true,
    serverSide: true, 
    deferRender: true,
    ajax: {
      url: "<?= base_url('Vehicle/traxrootVehicle') ?>",
      type: "GET",
      dataSrc: function(json) {
        console.log("Full JSON:", json);
        console.log("Vehicle Data:", json.data);
        $('#vehicleTable').closest('.card-body').show();
        return json.data;
      }
    },
    columns: [
      { 
        data: null,      
        render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1; 
        },
        title: "No"
      },
      { data: 'name', title: 'Name' },
      { data: 'comment', title: 'Comment' },
      { data: 'speed', title: 'Speed' },
      { data: 'sat', title: 'Satellite' },
      {
        data: null,
        title: "Coordinate",
        render: function (data, type, row) {
          let lat = row.latitude;
          let lng = row.longitude;
          let url = `https://www.google.com/maps?q=${lat},${lng}`;

          // Saat display -> tampilkan HTML link
          if (type === "display") {
              return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary" style="color:white;text-decoration:none;">
                        ${lat}, ${lng}
                      </a>`;
          }

          // Saat sort/search -> gunakan text biasa
          return `${lat}, ${lng}`;
        }
      }
    ],
    responsive: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50, 100],
    lengthChange: true,
    dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rtip',
    ordering: true,
    searching: true,
    language: {
      search: "🔍 Search:",
    },
    order: [[1, "ASC"]]
  });

  $('#vehicleTable').closest('.card-body').hide();
});

</script>