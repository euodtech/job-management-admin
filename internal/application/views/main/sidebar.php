<!-- Sidebar Overlay for Mobile -->
<?php
    $companyLogo = $this->session->userdata('CompanyLogo');
    if ($companyLogo && file_exists(FCPATH . 'assets/dist/img/company_logo/' . $companyLogo)) {
        $brandLogo = base_url('assets/dist/img/company_logo/' . $companyLogo);
    } else {
        $brandLogo = base_url('assets/dist/img/company_logo/default-company-logo.png');
    }
    $segment = $this->uri->segment('1');
    $current_url = current_url();
    $jobSegments = ['line-interruption-job','short-circuit-job','disconnection-job','reconnection-job'];
    $reportSegments = ['report-driver','report-job','report-customer'];
?>
<div id="sidebar"
     class="-translate-x-full fixed top-0 left-0 z-[60] w-64 h-full
            bg-white border-r border-gray-200
            transition-transform duration-300
            lg:translate-x-0 lg:z-40">

    <!-- Brand Logo -->
    <div class="flex items-center justify-center gap-2 h-16 border-b border-gray-200 bg-white px-4">
        <img src="<?= $brandLogo ?>" class="h-8 object-contain" alt="Logo">
        <span class="text-sm font-extrabold text-gray-800 truncate">FMS | Administrator</span>
    </div>

    <!-- User Panel -->
    <div class="flex items-center gap-3 pl-6 pr-4 py-3 border-b border-gray-100">
        <div class="relative shrink-0">
            <img src="<?= base_url('assets/dist/1144760.png') ?>" class="w-9 h-9 rounded-full ring-2 ring-gray-200" alt="User">
            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-800 truncate leading-tight"><?= ucwords(strtolower($this->session->userdata('Fullname'))) ?></p>
            <p class="text-[11px] text-gray-400 truncate leading-tight mt-0.5"><?= $this->session->userdata('Role') == 1 ? 'Super Admin' : 'Administrator' ?></p>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="flex-1 overflow-y-auto p-4">
        <!-- Section: MASTER -->
        <p class="px-3 pt-2 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Master</p>
        <ul class="space-y-1">
            <!-- Dashboard -->
            <li>
                <a href="<?= base_url('home') ?>"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium
                          <?= $segment == 'home' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?>
                          transition-colors">
                    <i class="fa-solid fa-chart-line w-5 text-center"></i>
                    Dashboard
                </a>
            </li>

            <!-- Maps (non-superadmin only) -->
            <?php if($this->session->userdata('Role') != 1) : ?>
            <li>
                <a href="<?= base_url('map') ?>"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium
                          <?= $segment == 'map' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?>
                          transition-colors">
                    <i class="fa-solid fa-map-pin w-5 text-center"></i>
                    Map
                </a>
            </li>
            <?php endif; ?>

            <!-- Company (superadmin only) -->
            <?php if($this->session->userdata('Role') == 1) : ?>
            <li>
                <a href="<?= base_url('company-list') ?>"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium
                          <?= $segment == 'company-list' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?>
                          transition-colors">
                    <i class="fa-solid fa-building w-5 text-center"></i>
                    Company
                </a>
            </li>
            <?php endif; ?>

            <!-- Rider -->
            <li>
                <a href="<?= base_url('user-list') ?>"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium
                          <?= $segment == 'user-list' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?>
                          transition-colors">
                    <i class="fa-solid fa-user w-5 text-center"></i>
                    Rider
                </a>
            </li>

            <!-- Vehicle (non-superadmin only) -->
            <?php if($this->session->userdata('Role') != 1) : ?>
            <li>
                <a href="<?= base_url('vehicle') ?>"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium
                          <?= $segment == 'vehicle' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?>
                          transition-colors">
                    <i class="fa-solid fa-car w-5 text-center"></i>
                    Vehicle
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <!-- Job List Accordion (non-superadmin + Pro subscription) -->
        <?php if($this->session->userdata('Role') != 1): ?>
        <?php if($this->session->userdata('CompanySubscribe') == 2) : ?>

        <div class="hs-accordion-group mt-2" data-hs-accordion-always-open>
            <!-- Job List Accordion -->
            <div class="hs-accordion <?= in_array($segment, $jobSegments) ? 'active' : '' ?>"
                 id="sidebar-accordion-job">
                <button type="button"
                        class="hs-accordion-toggle flex items-center justify-between w-full rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors
                               hs-accordion-active:bg-primary hs-accordion-active:text-white">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-briefcase w-5 text-center"></i>
                        Job List
                        <span class="count_notif_sidebar hidden min-w-[20px] px-1.5 py-0.5 text-[10px] font-bold leading-none text-center rounded-full bg-red-500 text-white hs-accordion-active:bg-white hs-accordion-active:text-red-500"></span>
                    </span>
                    <svg class="hs-accordion-active:rotate-180 w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="sidebar-accordion-job-child"
                     class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300"
                     role="region" aria-labelledby="sidebar-accordion-job">
                    <ul class="pl-7 space-y-1 mt-1">
                        <li>
                            <a href="<?= base_url('line-interruption-job') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($segment == 'line-interruption-job') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($segment == 'line-interruption-job') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Line Interruption
                                <span class="count_line hidden ml-auto min-w-[18px] px-1.5 py-0.5 text-[10px] font-bold leading-none text-center rounded-full bg-red-100 text-red-600"></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('short-circuit-job') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($segment == 'short-circuit-job') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($segment == 'short-circuit-job') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Short Circuit
                                <span class="count_short hidden ml-auto min-w-[18px] px-1.5 py-0.5 text-[10px] font-bold leading-none text-center rounded-full bg-red-100 text-red-600"></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('disconnection-job') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($segment == 'disconnection-job') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($segment == 'disconnection-job') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Disconnection
                                <span class="count_disconnection hidden ml-auto min-w-[18px] px-1.5 py-0.5 text-[10px] font-bold leading-none text-center rounded-full bg-red-100 text-red-600"></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('reconnection-job') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($segment == 'reconnection-job') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($segment == 'reconnection-job') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Reconnection
                                <span class="count_reconnect hidden ml-auto min-w-[18px] px-1.5 py-0.5 text-[10px] font-bold leading-none text-center rounded-full bg-red-100 text-red-600"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Reschedule Job -->
        <ul class="space-y-1 mt-2">
            <li>
                <a href="<?= base_url('reschedule-job') ?>"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium
                          <?= $segment == 'reschedule-job' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?>
                          transition-colors">
                    <i class="fa-solid fa-calendar-check w-5 text-center"></i>
                    Reschedule Job
                    <span class="count_reschedule ml-auto"></span>
                </a>
            </li>
        </ul>

        <!-- Job Summary -->
        <ul class="space-y-1 mt-1">
            <li>
                <a href="<?= base_url('job-summary') ?>"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium
                          <?= $segment == 'job-summary' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?>
                          transition-colors">
                    <i class="fa-solid fa-receipt w-5 text-center"></i>
                    Job Summary
                </a>
            </li>
        </ul>

        <?php endif; ?>
        <?php endif; ?>

        <!-- Report Accordion -->
        <div class="hs-accordion-group mt-2" data-hs-accordion-always-open>
            <div class="hs-accordion <?= in_array($segment, $reportSegments) ? 'active' : '' ?>"
                 id="sidebar-accordion-report">
                <button type="button"
                        class="hs-accordion-toggle flex items-center justify-between w-full rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors
                               hs-accordion-active:bg-primary hs-accordion-active:text-white">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-user w-5 text-center"></i>
                        Report
                    </span>
                    <svg class="hs-accordion-active:rotate-180 w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="sidebar-accordion-report-child"
                     class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300"
                     role="region" aria-labelledby="sidebar-accordion-report">
                    <ul class="pl-7 space-y-1 mt-1">
                        <li>
                            <a href="<?= base_url('report-driver') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($segment == 'report-driver') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($segment == 'report-driver') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Rider Report
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('report-job') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($segment == 'report-job') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($segment == 'report-job') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Job Report
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('report-customer') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($segment == 'report-customer') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($segment == 'report-customer') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Customer Report
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </nav>
</div>
<!-- Sidebar Backdrop for Mobile -->
<div id="sidebar-backdrop" class="hidden fixed inset-0 z-[55] bg-black/50 lg:hidden" onclick="toggleSidebar()"></div>
