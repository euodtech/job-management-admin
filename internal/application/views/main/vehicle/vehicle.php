
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
            return meta.row + 1; 
        },
        title: "No"
      },
      { data: 'name', title: 'Name' },
      { data: 'comment', title: 'Comment' },
      { data: 'speed', title: 'Speed' },
      { data: 'sat', title: 'Satellite' },
      // { data: 'address', title: 'Address' },
      // { data: 'address', render: function(data, type, row) {
      //     if (!row.address || row.address === "") {
      //         getAddressOSM(row.latitude, row.longitude, (addr) => {
      //             row.address = addr;
      //             // update cell di DataTables
      //             const table = $('#vehicleTable').DataTable();
      //             const rowIndex = table.row(row).index();
      //             table.cell(rowIndex, 5).data(addr).draw(false);
      //         });
      //         return "Loading...";
      //     } else {
      //         return row.address;
      //     }
      //   }
      // },
      // { data: 'latitude', title: 'Latitude' },
      // { data: 'longitude', title: 'Longitude' },
      {
        data: null,
        title: "Coordinates",
        render: function (data, type, row) {
          let lat = row.latitude;
          let lng = row.longitude;
          let url = `https://www.google.com/maps?q=${lat},${lng}`;

          // Saat display -> tampilkan HTML link
          if (type === "display") {
              return `<a href="${url}" target="_blank" class="btn btn-sm btn-primary" style="color:blue;text-decoration:underline;">
                        ${lat}, ${lng}
                      </a>`;
          }

          // Saat sort/search -> gunakan text biasa
          return `${lat}, ${lng}`;
        }
      }
    ],
    responsive: true,
    pageLength: 6,
    rowReorder: {
        selector: 'td:nth-child(2)'
    },
    lengthMenu: [5, 10, 25, 50, 100],
    dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
    buttons: [
      {
        extend: 'pageLength',
        className: 'btn btn-secondary me-2'
      }
    ],
    ordering: true,
    searching: true,
    language: {
      search: "🔍 Search:",
    },
    order: [[1, "ASC"]],
    // initComplete: function(settings, json) {
    //   $('#vehicleTable').closest('.card-body').show();
    // }
  });

  $('#vehicleTable').closest('.card-body').hide();
});

</script>