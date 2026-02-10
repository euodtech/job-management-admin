<style>
    .table_dashboard1 { font-size: 13px !important; }
</style>

<!-- Content Header -->
<div class="px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
        <nav class="flex">
            <ol class="flex items-center gap-1.5 text-sm">
                <li><a href="<?= base_url('home') ?>" class="text-primary hover:underline">Dashboard</a></li>
            </ol>
        </nav>
    </div>
</div>

<!-- Content -->
<div class="px-4 sm:px-6 lg:px-8 pb-6">
    <!-- Info Boxes Row 1 (Drivers) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-dark">
                <i class="fa-solid fa-user text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Drivers</p>
                <p class="text-lg font-bold text-gray-800"><?=$return['total_drivers']?></p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-dark">
                <i class="fa-solid fa-users-gear text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Drivers On Duty</p>
                <p class="text-lg font-bold text-gray-800"><?=$return['drivers_on_duty']?></p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-dark">
                <i class="fa-solid fa-users-line text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Drivers Off Duty</p>
                <p class="text-lg font-bold text-gray-800"><?=$return['drivers_off_duty']?></p>
            </div>
        </div>
    </div>

    <!-- Info Boxes Row 2 (Jobs) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4">
        <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-dark">
                <i class="fa-solid fa-briefcase text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Jobs</p>
                <p class="text-lg font-bold text-gray-800"><?=$return['total_job']?></p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-dark">
                <i class="fa-solid fa-hourglass-half text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Ongoing Job</p>
                <p class="text-lg font-bold text-gray-800"><?=$return['total_job_on_duty']?></p>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-dark">
                <i class="fa-solid fa-check text-white text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Finished Job</p>
                <p class="text-lg font-bold text-gray-800"><?=$return['total_finished_job']?></p>
            </div>
        </div>
    </div>

    <!-- Cards Row 1: Driver On Duty / Driver Off Duty -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <!-- Driver On Duty Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800">Driver On Duty</h3>
                <button type="button" data-hs-collapse="#card-body-on-duty" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div id="card-body-on-duty" class="hs-collapse open transition-all duration-300">
                <div class="px-5 py-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm table_dashboard1">
                            <thead class="bg-gray-50">
                                <tr class="whitespace-nowrap">
                                    <th class="w-[10%] text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Fullname</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Phone Number</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($return['drivers_on_duty_detail'] as $val) : ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="text-center px-4 py-3"><?= $no++ ?></td>
                                    <td class="px-4 py-3"><?= $val['Fullname'] ?></td>
                                    <td class="px-4 py-3"><?= $val['PhoneNumber'] ?></td>
                                    <td class="px-4 py-3"><?= $val['Email'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Driver Off Duty Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800">Driver Off Duty</h3>
                <button type="button" data-hs-collapse="#card-body-off-duty" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div id="card-body-off-duty" class="hs-collapse open transition-all duration-300">
                <div class="px-5 py-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm table_dashboard1">
                            <thead class="bg-gray-50">
                                <tr class="whitespace-nowrap">
                                    <th class="w-[10%] text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Fullname</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Phone Number</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600 text-left">Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($return['drivers_off_duty_detail'] as $val) : ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="text-center px-4 py-3"><?= $no++ ?></td>
                                    <td class="px-4 py-3"><?= $val['Fullname'] ?></td>
                                    <td class="px-4 py-3"><?= $val['PhoneNumber'] ?></td>
                                    <td class="px-4 py-3"><?= $val['Email'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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
                <button type="button" data-hs-collapse="#card-body-ongoing" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div id="card-body-ongoing" class="hs-collapse open transition-all duration-300">
                <div class="px-5 py-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm table_dashboard1">
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
                </div>
            </div>
        </div>

        <!-- Finished Job Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800">Finished Job</h3>
                <button type="button" data-hs-collapse="#card-body-finished" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div id="card-body-finished" class="hs-collapse open transition-all duration-300">
                <div class="px-5 py-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm table_dashboard1">
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
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"
    integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
