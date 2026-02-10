<!-- Sidebar Overlay for Mobile -->
<?php
    $companyLogo = $this->session->userdata('CompanyLogo');
    $brandLogo = ($companyLogo && $this->session->userdata('Role') != 1)
        ? base_url('assets/dist/img/company_logo/' . $companyLogo)
        : base_url('assets/dist/logo_efms.jpg');
    $segment = $this->uri->segment('1');
    $current_url = current_url();
    $jobSegments = ['line-interruption-job','short-circuit-job','disconnection-job','reconnection-job'];
    $reportSegments = ['report-driver','report-job','report-customer'];
?>
<div id="sidebar"
     class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0
            -translate-x-full fixed top-0 left-0 z-[60] w-64 h-full
            bg-white border-r border-gray-200
            transition-transform duration-300
            lg:translate-x-0 lg:z-40"
     role="dialog" tabindex="-1">

    <!-- Brand Logo -->
    <div class="flex items-center justify-center gap-2 h-16 border-b border-gray-200 bg-white px-4">
        <img src="<?= $brandLogo ?>" class="h-8 object-contain" alt="Logo">
        <span class="text-sm font-extrabold text-gray-800 truncate">fms | Administrator</span>
    </div>

    <!-- User Panel -->
    <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
        <img src="<?= base_url('assets/dist/1144760.png') ?>" class="w-9 h-9 rounded-full ring-2 ring-gray-200" alt="User">
        <span class="text-sm font-semibold text-gray-700 truncate"><?= strtoupper($this->session->userdata('Fullname')) ?></span>
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
                    Maps
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
                        <span class="text-xs font-bold count_notif_sidebar"></span>
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
                                <span class="text-xs font-bold count_line"></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('short-circuit-job') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($segment == 'short-circuit-job') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($segment == 'short-circuit-job') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Short Circuit
                                <span class="text-xs font-bold count_short"></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('disconnection-job') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($segment == 'disconnection-job') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($segment == 'disconnection-job') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Disconnection
                                <span class="text-xs font-bold count_disconnection"></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('reconnection-job') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($segment == 'reconnection-job') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($segment == 'reconnection-job') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Reconnection
                                <span class="text-xs font-bold count_reconnect"></span>
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
                    <span class="text-xs font-bold count_reschedule"></span>
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
                                Report Rider
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('report-job') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($segment == 'report-job') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($segment == 'report-job') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Report Job
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('report-customer') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($segment == 'report-customer') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($segment == 'report-customer') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Report Customer
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </nav>
</div>
