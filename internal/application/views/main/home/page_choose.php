<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    <!-- overlayScrollbars -->
    <link rel="stylesheet"
        href="<?php echo base_url('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css'); ?>">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/select2/css/select2.min.css'); ?>">
    <link rel="stylesheet"
        href="<?php echo base_url('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css'); ?>">
    <!-- DataTables -->
    <link rel="stylesheet"
        href="<?php echo base_url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); ?>">
    <link rel="stylesheet"
        href="<?php echo base_url('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css'); ?>">
    <!-- SweetAlert2 -->
    <link rel="stylesheet"
        href="<?php echo base_url('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css'); ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/adminlte.min.css'); ?>">
    <style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    input:checked+.slider {
        background: #f00 !important;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
    </style>
</head>

<body class="hold-transition lockscreen">
    <div class="container">
        <div class="login-logo">
            <a href="#"><b>Choose Outlet</b></a>
        </div>


        <div class="row">
             <div class="col-md-4">

                <form method="post" action="<?php echo base_url('ChooseOutlet/choose_redirect'); ?>">

                    <div class="card card-body card-primary flex-fill" style="height: auto">
                        <div class="alert alert-success" id="alert-<?=$key['OutletID']?>" role="alert"
                            style="display:none">
                            <div id=""></div>
                        </div>
                        <div class="card-body box-profile " align="center">

                            <div style=" border:solid 3px #ccc; border-radius: 30%; height: 150px; width: 150px; overflow: hidden;"
                                align="center">
                                <img class=""
                                    src="https://www.nicepng.com/png/detail/204-2046737_barber-shop-comments-barbershop-icon-png.png"
                                    alt="Outlet Logo" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <h5 class="profile-username text-center" style="font-size:16px;">ALL OUTLET</h5>
                     
                            <input type="hidden" class="form-control"
                                value="all" name="storeid">
                            <input type="hidden" class="form-control" value="ALL OUTLET"
                                name="storename">
                            <button type="submit" style="background:#deba44 !important"
                                class="btn btn-primary btn-block">Choose</button></a>
                            <br>
                            
                        </div>

                    </div>

                </form>
            </div>



            <?php foreach ($store->result_array() as $key) {
              # code...
            ?>

            <div class="col-md-4">

                <form method="post" action="<?php echo base_url('ChooseOutlet/choose_redirect'); ?>">

                    <div class="card card-body card-primary flex-fill" style="height: auto">
                        <div class="alert alert-success" id="alert-<?=$key['OutletID']?>" role="alert"
                            style="display:none">
                            <div id="message-<?=$key['OutletID']?>"></div>
                        </div>
                        <div class="card-body box-profile " align="center">

                            <div style=" border:solid 3px #ccc; border-radius: 30%; height: 150px; width: 150px; overflow: hidden;"
                                align="center">
                                <img class=""
                                    src="https://www.nicepng.com/png/detail/204-2046737_barber-shop-comments-barbershop-icon-png.png"
                                    alt="Outlet Logo" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <h5 class="profile-username text-center" style="font-size:16px;"><?php echo $key['MasterOutletName']?></h5>
                     
                            <input type="hidden" class="form-control"
                                value="<?php echo $key['MasterOutletID']; ?>" name="storeid">
                            <input type="hidden" class="form-control" value="<?php echo $key['MasterOutletName']; ?>"
                                name="storename">
                            <button type="submit" style="background:#deba44 !important"
                                class="btn btn-primary btn-block">Choose</button></a>
                            <br>
                            
                        </div>

                    </div>

                </form>
            </div>

            <?php }?>
        </div>

        <script src="<?php echo base_url('assets/admin/plugins/jquery/jquery.min.js'); ?>"></script>
        <!-- Bootstrap -->
        <script src="<?php echo base_url('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
        <script>
        function changeStatus(id) {

            if ($('#toko-' + id).prop("checked") == true) {
                // alert($('.outlet-' + id).val());
                var status = 0;
            } else {
                var status = 1;
            }
            $.ajax({
                url: "<?php echo base_url('outlet/statusoutlet'); ?>",
                method: "post",
                data: {
                    status: status,
                    outletid: id
                },
                dataType: "json",
                success: function(data) {
                    $("#message-" + id).text("Outlet status updated successfully");
                    $('#alert-' + id).css('display', 'block');
                    $("#alert-" + id).fadeTo(2000, 500).slideUp(500, function() {
                        $("#alert-" + id).slideUp(500);
                    });
                    //console.log(data);

                    //     ' <select class="form-control select2" onchange="getval(this);" id="mod" name="outletid">' +
                    //     data.obj + '</select>';
                }
            })
        }
        </script>