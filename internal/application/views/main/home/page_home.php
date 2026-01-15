<style>
    .table_dashboard1 {
        font-size: 13px !important;
    }

</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url("home") ?>">Dashboard</a></li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4 ">
                    <div class="info-box">
                        <span class="info-box-icon elevation-1" style="background-color: #0a1431ff;"><i class="fa-solid fa-user text-white"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">
                                Total Drivers <br>
                                <strong><?=$return['total_drivers']?></strong>
                            </span>
                            
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box mb-3">
                        <span class="info-box-icon elevation-1" style="background-color: #0a1431ff;"><i class="fa-solid fa-users-gear text-white"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">
                                Drivers On Duty <br>
                                <strong><?=$return['drivers_on_duty']?></strong>
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box mb-3">
                        <span class="info-box-icon elevation-1" style="background-color: #0a1431ff;"><i class="fa-solid fa-users-line text-white"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">
                                Drivers Off Duty <br>
                                <strong><?=$return['drivers_off_duty']?></strong>
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4 ">
                    <div class="info-box">
                        <span class="info-box-icon elevation-1" style="background-color: #0a1431ff;"><i class="fa-solid fa-briefcase text-white"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">
                                Total Jobs <br>
                                <strong><?=$return['total_job']?></strong>
                            </span>
                            
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box mb-3">
                        <span class="info-box-icon elevation-1" style="background-color: #0a1431ff;"><i class="fa-solid fa-hourglass-half text-white"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">
                                Ongoing Job <br>
                                <strong><?=$return['total_job_on_duty']?></strong>
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box mb-3">
                        <span class="info-box-icon elevation-1" style="background-color: #0a1431ff;"><i class="fa-solid fa-check text-white"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">
                                Finished Job <br>
                                <strong><?=$return['total_finished_job']?></strong>
                            </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

                

            </div>

            <!-- PIE CHART SERVICE -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Driver On Duty</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>

                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table_dashboard1">
                                    <thead class="">
                                        <tr style="white-space: nowrap;">
                                            <th style="width: 10%; text-align: center;">No</th>
                                            <th>Fullname</th>
                                            <th>Phone Number</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                    $no = 1;
                                    foreach($return['drivers_on_duty_detail'] as $val) : ?>
                                        <tr>
                                            <td style="text-align: center;"><?= $no++ ?></td>
                                            <td><?= $val['Fullname'] ?></td>
                                            <td><?= $val['PhoneNumber'] ?></td>
                                            <td><?= $val['Email'] ?></td>
                                            
                                        </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- /.row -->
                        </div>
                    </div>
                </div>

                <!-- PIE CHART PRODUCT -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Driver Off Duty</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>

                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered table-striped table_dashboard1" >
                                    <thead class="">
                                        <tr style="white-space: nowrap;">
                                            <th style="width: 10%; text-align: center;">No</th>
                                            <th>Fullname</th>
                                            <th>Phone Number</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                    $no = 1;
                                    foreach($return['drivers_off_duty_detail'] as $val) : ?>
                                        <tr>
                                            <td style="text-align: center;"><?= $no++ ?></td>
                                            <td><?= $val['Fullname'] ?></td>
                                            <td><?= $val['PhoneNumber'] ?></td>
                                            <td><?= $val['Email'] ?></td>
                                            
                                        </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                            <!-- /.row -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ongoing Job</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>

                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered table-striped table_dashboard1" >
                                    <thead class="">
                                        <tr style="white-space: nowrap;">
                                            <th style="width: 10%; text-align: center;">No</th>
                                            <th>Job name</th>
                                            <th>Driver</th>
                                            <th>Customer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                    $no = 1;
                                    foreach($return['detail_job_on_duty'] as $val) : ?>
                                        <tr>
                                            <td style="text-align: center;"><?= $no++ ?></td>
                                            <td><?= $val['JobName'] ?></td>
                                            <td><?= $val['NameDriver'] ?> - <?= $val['PhoneDriver'] ?></td>
                                            <td><?= $val['CustomerName'] ?> - <?= $val['CustomerPhone'] ?></td>
                                            
                                        </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                            <!-- /.row -->
                        </div>
                    </div>
                </div>

                <!-- PIE CHART PRODUCT -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Finished Job</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>

                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered table-striped table_dashboard1" >
                                    <thead class="">
                                        <tr style="white-space: nowrap;">
                                            <th style="width: 10%; text-align: center;">No</th>
                                            <th>Job name</th>
                                            <th>Driver</th>
                                            <th>Customer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                    $no = 1;
                                    foreach($return['finished_job_detail'] as $val) : ?>
                                        <tr>
                                            <td style="text-align: center;"><?= $no++ ?></td>
                                            <td><?= $val['JobName'] ?></td>
                                            <td><?= $val['NameDriver'] ?> - <?= $val['PhoneDriver'] ?></td>
                                            <td><?= $val['CustomerName'] ?> - <?= $val['CustomerPhone'] ?></td>
                                            
                                        </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                            <!-- /.row -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PIE CHART -->
            <!-- /.row -->
        </div>
        <!--/. container-fluid -->
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"
    integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>