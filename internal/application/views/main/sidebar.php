<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-navy elevation-1">
    <!-- Brand Logo -->
    <a href="" class="brand-link d-flex justify-content-center align-items-center"
        style="background-color: white; gap: 10px;">
        <img src="<?= base_url('assets/dist/logo_efms.jpg') ?>" class="brand-text img-logo-text" style="width: 15%;">

        <span style="font-size: 15px; font-weight: 800;">fms | Administrator</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo base_url('assets/dist/1144760.png'); ?>" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo strtoupper($this->session->userdata('Fullname')); ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2 mb-4">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">
                <li class="nav-header pl-3">MASTER</li>
                <li class="nav-item">
                    <a href="<?= base_url('home') ?>"
                        class="nav-link <?= $this->uri->segment('1') == "home" ? "active" : "" ?>">
                        <i class="fa-solid fa-chart-line"></i>
                        <p>
                            Dashboard
                        </p>

                    </a>
                </li>

                <?php if($this->session->userdata('Role') != 1) : ?>
                <li class="nav-item">
                    <a href="<?= base_url('map') ?>"
                        class="nav-link <?= $this->uri->segment('1') == "map" ? "active" : "" ?>">
                        <i class="fa-solid fa-map-pin"></i>
                        <p>
                            Maps
                        </p>

                    </a>
                </li>
                <?php endif; ?>

                <?php if($this->session->userdata('Role') == 1) : ?>
                
                <li class="nav-item">
                    <a href="<?= base_url('company-list') ?>"
                        class="nav-link <?= $this->uri->segment('1') == "company-list" ? "active" : "" ?>">
                        <i class="fa-solid fa-building"></i>
                        <p>
                            Company
                        </p>

                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="<?= base_url('user-list') ?>"
                        class="nav-link <?= $this->uri->segment('1') == "user-list" ? "active" : "" ?>">
                        <i class="fa-solid fa-user"></i>
                        <p>
                            Rider
                        </p>

                    </a>
                </li>

                <?php if($this->session->userdata('Role') != 1) : ?>
                <li class="nav-item">
                    <a href="<?= base_url('vehicle') ?>"
                        class="nav-link <?= $this->uri->segment('1') == "vehicle" ? "active" : "" ?>">
                        <i class="fa-solid fa-car"></i>
                        <p>
                            Vehicle
                        </p>

                    </a>
                </li>
                <?php endif; ?>


                <!-- <?php if($this->session->userdata('Role') != 1) : ?>
                <li class="nav-item">
                    <a href="<?= base_url('job-list') ?>"
                        class="nav-link <?= $this->uri->segment('1') == "job-list" ? "active" : "" ?>">
                        <i class="fa-solid fa-briefcase"></i>
                        <p>
                            Job
                        </p>

                    </a>
                </li>
                <?php endif; ?> -->

                <li class="nav-item">
                    <a href="<?= base_url('customer-list') ?>"
                        class="nav-link <?= $this->uri->segment('1') == "customer-list" ? "active" : "" ?>">
                        <i class="fa-solid fa-users"></i>
                        <p>
                            Customer
                        </p>

                    </a>
                </li>
            </ul>

            <?php if($this->session->userdata('Role') != 1): ?>

            <?php if($this->session->userdata('CompanySubscribe') == 2) : ?>

                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                    <li class="nav-item has-treeview">
                        <?php $segment_uri = $this->uri->segment('1'); ?>
                        <a href="#"
                            class="nav-link <?= ($segment_uri == "line-interruption-job" OR  $segment_uri == "short-circuit-job" OR $segment_uri == "disconnection-job" OR $segment_uri == "reconnection-job") ? "active" : ''?>">
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fa-solid fa-briefcase"></i>
                                    <p class="mb-0">
                                        Job List 
                                    </p>
                                    <p class="text_notif_sidebar count_notif_sidebar mb-0"></p>
                                </div>
                                <div class="">
                                    <i class="right fas fa-angle-left"></i>
                                </div>
                            </div>
                            

                        </a>
                        
                        <ul class="nav nav-treeview">
                            <?php $current_url = current_url(); ?>
                            <li class="nav-item">
                                <a href="<?= base_url('line-interruption-job') ?>" class="nav-link <?= ($this->uri->segment(1) == 'line-interruption-job') ? 'active' : '' ?>">
                                    <i class="nav-icon <?= ($current_url == base_url('line-interruption-job')) ? 'fas fa-check-circle' : 'far fa-circle' ?>"></i>
                                    <p>Line Interruption</p>
                                    <p class="text_notif_sidebar count_line mb-0"></p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('short-circuit-job') ?>" class="nav-link <?= ($this->uri->segment(1) =='short-circuit-job') ? 'active' : '' ?>">
                                    <i class="nav-icon <?= ($current_url == base_url('short-circuit-job')) ? 'fas fa-check-circle' : 'far fa-circle' ?>"></i>
                                    <p>Short Circuit</p>
                                    <p class="text_notif_sidebar count_short mb-0"></p>
                                </a>
                            </li>
                            <li class="nav-item <?= ($this->uri->segment(1) == 'disconnection-job') ? 'active' : '' ?>"">
                                <a href="<?= base_url('disconnection-job') ?>" class="nav-link <?= ($this->uri->segment(1) == 'disconnection-job') ? 'active' : '' ?>">
                                    <i class="nav-icon <?= ($current_url == base_url('disconnection-job')) ? 'fas fa-check-circle' : 'far fa-circle' ?>"></i>
                                    <p>Disconnection</p>
                                    <p class="text_notif_sidebar count_disconnection mb-0"></p>
                                </a>
                            </li>
                            <li class="nav-item <?= ($this->uri->segment(1) == 'reconnection-job') ? 'active' : '' ?>"">
                                <a href="<?= base_url('reconnection-job') ?>" class="nav-link <?= ($this->uri->segment(1) == 'reconnection-job') ? 'active' : '' ?>">
                                    <i class="nav-icon <?= ($current_url == base_url('reconnection-job')) ? 'fas fa-check-circle' : 'far fa-circle' ?>"></i>
                                    <p>Reconnection</p>
                                    <p class="text_notif_sidebar count_reconnect mb-0"></p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="<?= base_url('reschedule-job') ?>"
                        class="nav-link <?= $this->uri->segment('1') == "reschedule-job" ? "active" : "" ?>">
                        <i class="fa-solid fa-calendar-check"></i>
                        <p>
                            Reschedule Job 
                        </p>
                        <p class="text_notif_sidebar count_reschedule mb-0"></p>

                    </a>
                </li>
            </ul>
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="<?= base_url('job-summary') ?>"
                        class="nav-link <?= $this->uri->segment('1') == "job-summary" ? "active" : "" ?>">
                        <i class="fa-solid fa-receipt"></i>
                        <p>
                            Job Summary
                        </p>

                    </a>
                </li>
            </ul>
                
            <?php endif; ?>
            
            <?php endif; ?>


            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item has-treeview">
                    <a href="<?= base_url('user-list') ?>" class="nav-link">
                        <i class="fa-solid fa-user"></i>
                        <p>
                            Report
                            <i class="right fas fa-angle-left"></i> <!-- icon kurang dari -->
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php $current_url = current_url(); ?>
                        <li class="nav-item">
                            <a href="<?= base_url('report-driver') ?>" class="nav-link <?= ($this->uri->segment(1) == 'report-driver') ? 'active' : '' ?>">
                                <i class="nav-icon <?= ($current_url == base_url('report-driver')) ? 'fas fa-check-circle' : 'far fa-circle' ?>"></i>
                                <p>Report Rider</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('report-job') ?>" class="nav-link <?= ($this->uri->segment(1) =='report-job') ? 'active' : '' ?>">
                                <i class="nav-icon <?= ($current_url == base_url('report-job')) ? 'fas fa-check-circle' : 'far fa-circle' ?>"></i>
                                <p>Report Job</p>
                            </a>
                        </li>
                        <li class="nav-item <?= ($this->uri->segment(1) == 'report-customer') ? 'active' : '' ?>"">
                            <a href="<?= base_url('report-customer') ?>" class="nav-link <?= ($this->uri->segment(1) == 'report-customer') ? 'active' : '' ?>">
                                <i class="nav-icon <?= ($current_url == base_url('report-customer')) ? 'fas fa-check-circle' : 'far fa-circle' ?>"></i>
                                <p>Report Customer</p>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>

        </nav>

        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>