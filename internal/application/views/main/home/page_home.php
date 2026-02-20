<!-- Content Header -->
<div class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
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
    <!-- Info Boxes Row 1 (Drivers) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-dark">
                <i class="fa-solid fa-user text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Drivers</p>
                <p class="text-lg font-bold text-gray-800" id="count-total-drivers"><?=$return['total_drivers']?></p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-green-600">
                <i class="fa-solid fa-circle-check text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Drivers Online</p>
                <p class="text-lg font-bold text-gray-800" id="count-drivers-online"><?=$return['drivers_online']?></p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-gray-400">
                <i class="fa-solid fa-circle-xmark text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Drivers Offline</p>
                <p class="text-lg font-bold text-gray-800" id="count-drivers-offline"><?=$return['drivers_offline']?></p>
            </div>
        </div>
    </div>

    <!-- Info Boxes Row 2 (Jobs) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4">
        <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-dark">
                <i class="fa-solid fa-briefcase text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Jobs</p>
                <p class="text-lg font-bold text-gray-800"><?=$return['total_job']?></p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-dark">
                <i class="fa-solid fa-hourglass-half text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Ongoing Job</p>
                <p class="text-lg font-bold text-gray-800"><?=$return['total_job_on_duty']?></p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-dark">
                <i class="fa-solid fa-check text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Finished Job</p>
                <p class="text-lg font-bold text-gray-800"><?=$return['total_finished_job']?></p>
            </div>
        </div>
    </div>

    <!-- Cards Row 1: Drivers Online / Drivers Offline -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <!-- Drivers Online Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800">Drivers Online</h3>
                <button type="button" onclick="toggleCard(this, 'card-body-online')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div id="card-body-online" class="transition-all duration-300 overflow-hidden">
                <div class="px-5 py-4">
                    <div class="overflow-x-auto">
                        <table id="table-drivers-online" class="w-full text-[13px]" data-paginated-table data-per-page="5">
                            <thead class="bg-gray-50">
                                <tr class="whitespace-nowrap">
                                    <th class="w-[10%] text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Fullname</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Phone Number</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Last Active</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($return['drivers_online_detail'] as $val) : ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="text-center px-4 py-3"><?= $no++ ?></td>
                                    <td class="px-4 py-3 driver-name"><?= $val['Fullname'] ?></td>
                                    <td class="px-4 py-3"><?= $val['PhoneNumber'] ?></td>
                                    <td class="px-4 py-3"><?= $val['LastActivity'] ? date('M d, Y h:i A', strtotime($val['LastActivity'])) : '-' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" data-pagination-controls></div>
                </div>
            </div>
        </div>

        <!-- Drivers Offline Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800">Drivers Offline</h3>
                <button type="button" onclick="toggleCard(this, 'card-body-offline')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div id="card-body-offline" class="transition-all duration-300 overflow-hidden">
                <div class="px-5 py-4">
                    <div class="overflow-x-auto">
                        <table id="table-drivers-offline" class="w-full text-[13px]" data-paginated-table data-per-page="5">
                            <thead class="bg-gray-50">
                                <tr class="whitespace-nowrap">
                                    <th class="w-[10%] text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Fullname</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Phone Number</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Last Active</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($return['drivers_offline_detail'] as $val) : ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="text-center px-4 py-3"><?= $no++ ?></td>
                                    <td class="px-4 py-3"><?= $val['Fullname'] ?></td>
                                    <td class="px-4 py-3"><?= $val['PhoneNumber'] ?></td>
                                    <td class="px-4 py-3"><?= $val['LastActivity'] ? date('M d, Y h:i A', strtotime($val['LastActivity'])) : 'Never' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" data-pagination-controls></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards Row 2: Ongoing Job / Finished Job -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <!-- Ongoing Job Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800">Ongoing Job</h3>
                <button type="button" onclick="toggleCard(this, 'card-body-ongoing')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div id="card-body-ongoing" class="transition-all duration-300 overflow-hidden">
                <div class="px-5 py-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-[13px]" data-paginated-table data-per-page="5">
                            <thead class="bg-gray-50">
                                <tr class="whitespace-nowrap">
                                    <th class="w-[10%] text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Job name</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Driver</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Customer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($return['detail_job_on_duty'] as $val) : ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="text-center px-4 py-3"><?= $no++ ?></td>
                                    <td class="px-4 py-3"><?= $val['JobName'] ?></td>
                                    <td class="px-4 py-3"><?= $val['NameDriver'] ?> - <?= $val['PhoneDriver'] ?></td>
                                    <td class="px-4 py-3"><?= $val['CustomerName'] ?> - <?= $val['CustomerPhone'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" data-pagination-controls></div>
                </div>
            </div>
        </div>

        <!-- Finished Job Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800">Finished Job</h3>
                <button type="button" onclick="toggleCard(this, 'card-body-finished')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div id="card-body-finished" class="transition-all duration-300 overflow-hidden">
                <div class="px-5 py-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-[13px]" data-paginated-table data-per-page="5">
                            <thead class="bg-gray-50">
                                <tr class="whitespace-nowrap">
                                    <th class="w-[10%] text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Job name</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Driver</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Customer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($return['finished_job_detail'] as $val) : ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="text-center px-4 py-3"><?= $no++ ?></td>
                                    <td class="px-4 py-3"><?= $val['JobName'] ?></td>
                                    <td class="px-4 py-3"><?= $val['NameDriver'] ?> - <?= $val['PhoneDriver'] ?></td>
                                    <td class="px-4 py-3"><?= $val['CustomerName'] ?> - <?= $val['CustomerPhone'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3" data-pagination-controls></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCard(btn, targetId) {
    var body = document.getElementById(targetId);
    var icon = btn.querySelector('i');
    var header = btn.parentElement;
    var card = header.parentElement;
    if (body.style.display === 'none') {
        body.style.display = '';
        icon.className = 'fas fa-minus';
        header.classList.add('border-b');
        card.classList.remove('self-start');
    } else {
        body.style.display = 'none';
        icon.className = 'fas fa-plus';
        header.classList.remove('border-b');
        card.classList.add('self-start');
    }
}
</script>

<script>
(function() {
    var POLL_INTERVAL = 30000;
    var BASE_URL = '<?= base_url("home/get_driver_status_ajax") ?>';

    function formatDate(dateStr) {
        if (!dateStr) return 'Never';
        var d = new Date(dateStr.replace(' ', 'T'));
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var month = months[d.getMonth()];
        var day = ('0' + d.getDate()).slice(-2);
        var year = d.getFullYear();
        var hours = d.getHours();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        var mins = ('0' + d.getMinutes()).slice(-2);
        return month + ' ' + day + ', ' + year + ' ' + ('0' + hours).slice(-2) + ':' + mins + ' ' + ampm;
    }

    function buildRows(drivers, emptyLabel) {
        if (drivers.length === 0) {
            return '<tr><td colspan="4" class="text-center px-4 py-3 text-gray-400">No drivers ' + emptyLabel + '</td></tr>';
        }
        var html = '';
        for (var i = 0; i < drivers.length; i++) {
            var d = drivers[i];
            var lastActive = d.LastActivity ? formatDate(d.LastActivity) : emptyLabel === 'online' ? '-' : 'Never';
            html += '<tr class="border-b border-gray-100 hover:bg-gray-50">' +
                '<td class="text-center px-4 py-3">' + (i + 1) + '</td>' +
                '<td class="px-4 py-3">' + (d.Fullname || '') + '</td>' +
                '<td class="px-4 py-3">' + (d.PhoneNumber || '') + '</td>' +
                '<td class="px-4 py-3">' + lastActive + '</td>' +
                '</tr>';
        }
        return html;
    }

    function refreshTable(tableId, drivers, emptyLabel) {
        var table = document.getElementById(tableId);
        if (!table) return;
        var tbody = table.querySelector('tbody');
        if (!tbody) return;
        tbody.innerHTML = buildRows(drivers, emptyLabel);

        var wrapper = table.closest('.overflow-x-auto');
        var controls = wrapper ? wrapper.parentElement.querySelector('[data-pagination-controls]') : null;
        if (controls) controls.innerHTML = '';

        if (typeof window.initPaginatedTable === 'function') {
            window.initPaginatedTable(table);
        }
    }

    function poll() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', BASE_URL, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4) return;
            if (xhr.status !== 200) return;
            var data;
            try {
                data = JSON.parse(xhr.responseText);
            } catch(e) { return; }
            if (!data) return;

            document.getElementById('count-total-drivers').textContent = data.total_drivers;
            document.getElementById('count-drivers-online').textContent = data.drivers_online;
            document.getElementById('count-drivers-offline').textContent = data.drivers_offline;

            refreshTable('table-drivers-online', data.drivers_online_detail || [], 'online');
            refreshTable('table-drivers-offline', data.drivers_offline_detail || [], 'offline');
        };
        xhr.send();
    }

    setInterval(poll, POLL_INTERVAL);
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
