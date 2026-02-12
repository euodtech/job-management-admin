
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
    <div class="bg-white rounded-xl shadow-sm border border-cyan-200">
        <div class="px-5 py-4 border-b border-cyan-200">
            <h5 class="text-base font-semibold text-gray-800">Vehicle</h5>
        </div>
        <div class="px-5 py-4">
          <div id="vehicleLoading" class="text-center py-5">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-cyan-200 border-t-cyan-600 mx-auto"></div>
            <p class="mt-3 text-gray-500">Loading vehicles...</p>
          </div>
          <table id="vehicleTable" class="table table-striped table-bordered dt-responsive display responsive wrap" cellspacing="0" width="100%" style="display:none;">
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
            </tbody>
          </table>
        </div>
    </div>
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
        $('#vehicleLoading').hide();
        $('#vehicleTable').show();
        return json.data;
      },
      error: function(xhr, error, thrown) {
        $('#vehicleLoading').html(
          '<div class="text-center py-5">' +
            '<i class="fas fa-exclamation-triangle text-amber-500" style="font-size: 2.5rem;"></i>' +
            '<p class="mt-3 text-gray-500">Failed to load vehicle data. Please try again later.</p>' +
          '</div>'
        );
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

          if (type === "display") {
              return `<a href="${url}" target="_blank" rel="noopener noreferrer" class="btn-tw-primary" style="color:white;text-decoration:none;">
                        ${lat}, ${lng}
                      </a>`;
          }

          return `${lat}, ${lng}`;
        }
      }
    ],
    responsive: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50, 100],
    lengthChange: true,
    ordering: true,
    searching: true,
    language: {
      search: "Search:",
    },
    order: [[1, "ASC"]]
  });

  $('#vehicleLoading').show();
  $('#vehicleTable').hide();
});

</script>
