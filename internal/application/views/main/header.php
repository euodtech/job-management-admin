<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon"
        href="http://quetraverse.pro/primafit/assets/img/icon_primafit_Red.svg" />

    <title><?php echo $title; ?></title>
    <!-- Google Font: Source Sans Pro -->
    <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            />

            <!-- Leaflet Control Geocoder CSS -->
    <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"
    />
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="<?php echo base_url('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css'); ?>">

    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/adminlte.min.css'); ?>">

    <!-- summernote -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/summernote/summernote-bs4.min.css'); ?>">

    <!-- DataTables -->
    <link rel="stylesheet"
        href="<?php echo base_url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); ?>">
    <link rel="stylesheet"
        href="<?php echo base_url('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css'); ?>">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/select2/css/select2.min.css'); ?>">
    <link rel="stylesheet"
        href="<?php echo base_url('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css'); ?>">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">

    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">


    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Open Layer -->
    <!-- <link rel="stylesheet" href="https://unpkg.com/ol@6.15.1/ol.css">
    <script src="https://unpkg.com/ol@6.15.1/dist/ol.js"></script> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ol3/6.15.1/ol.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ol3/6.15.1/ol.js"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/openlayers/10.6.1/ol.min.css" integrity="sha512-oVyisN6T8O7H9DnBc1w/IipxzLhNvJERKa0Rx9fKEtaodE7UXQAypIHamYzQPAqVxp0pVl25e4spVQWIVfu6eA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/10.6.1/dist/ol.min.js" integrity="sha512-NEUbbO7KI1OYn+IHcF70vm3ON0obczJz9PJFwxHkfPCsT14UqDD4roG7rF5WpwkXRTPvysFb6Wvw/Tjh5tfv8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/brands.min.css" integrity="sha512-WxpJXPm/Is1a/dzEdhdaoajpgizHQimaLGL/QqUIAjIihlQqlPQb1V9vkGs9+VzXD7rgI6O+UsSKl4u5K36Ydw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/fontawesome.min.css" integrity="sha512-M5Kq4YVQrjg5c2wsZSn27Dkfm/2ALfxmun0vUE3mPiJyK53hQBHYCVAtvMYEC7ZXmYLg8DVG4tF8gD27WmDbsg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/regular.min.css" integrity="sha512-x3gns+l9p4mIK7vYLOCUoFS2P1gavFvnO9Its8sr0AkUk46bgf9R51D8xeRUwCSk+W93YbXWi19BYzXDNBH5SA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/solid.min.css" integrity="sha512-EHa6vH03/Ty92WahM0/tet1Qicl76zihDCkBnFhN3kFGQkC+mc86d7V+6y2ypiLbk3h0beZAGdUpzfMcb06cMg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    
    <style type="text/css">

    .bell-shake {
        animation: shake 0.5s infinite;
    }

    @media (max-width: 991.98px) {
        .dataTables_info, .dataTables_paginate  {
            font-size: 12px !important;
            margin-top: 0.75rem !important;
        }

        .dataTables_length, .dataTables_filter {
            font-size: 12px !important;
        }
    }

    /* .dataTables_paginate, .dataTables_filter  {
        float: right !important;
        text-align: right !important;
    }

    .dataTables_info, .dataTables_length  {
        float: left !important;
        text-align: left !important;
    } */

    @keyframes shake {
        0% { transform: rotate(0deg); }
        25% { transform: rotate(-15deg); }
        50% { transform: rotate(15deg); }
        75% { transform: rotate(-15deg); }
        100% { transform: rotate(0deg); }
    }




    .text_notif_sidebar {
        font-weight: 900 !important;
    }

    .ongoing_job {
        background-color: #fff3cd;       /* kuning lembut */
        color: #856404 !important;                  /* teks coklat gelap */
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-block;
        white-space: nowrap !important;
    }

    .finished_job {
        background-color: #d4edda;       /* hijau lembut */
        color: #155724;                  /* teks hijau tua */
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-block;
        white-space: nowrap !important;
    }

    .awaiting_job {
        background-color: #f8d7da;       /* merah muda lembut */
        color: #721c24;                  /* teks merah gelap */
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-block;
        white-space: nowrap !important;
    }

    .reschedule_job {
        background-color: #cfe2ff;       /* biru lembut */
        color: #084298 !important;       /* teks biru gelap */
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-block;
        white-space: nowrap !important;
    }

    .blur {
        filter: blur(20px)
    }

    .btn-primary {
        background-color: #070f26 !important; 
        color: white !important;
        border: unset;
    }

    .custom-swal-loader {
        border-top-color: #fff !important;
        border-right-color: black !important;
        border-bottom-color: #fff !important;
        border-left-color: black !important;
    }

    .sidebar-light-navy .nav-sidebar>.nav-item>.nav-link.active {
        background-color: #070f26 !important;
    }

   .bg-gradient-blue-purple {
    background: linear-gradient(135deg, #4e54c8, #8f94fb);
    /* background: linear-gradient(135deg, #11998e, #38ef7d); */
    color: white;
/* background: linear-gradient(135deg, #f7971e, #ffd200); */

   }

   .btn-primary-gradient {
    background: linear-gradient(90deg, #0a1431ff, #000000);
    background-size: 200% 100%; /* gradient jadi 2x lebih lebar */
    background-position: left center;
    color: white;
    transition: background-position 1s ease; /* animasi geser */
    }

    .btn-primary-gradient:hover {
    background-position: right center; /* geser ke kanan */
    color: white;
    }


    .alert {
        padding: 1px 20px !important;
        margin-bottom: unset !important;
        position: relative;
        display: inline-block;

        /* animasi total 6s: 
       0–20%  => fadeInDown,
       70–100% => fadeOutUp */
        animation: fadeInOut 6s forwards;
    }

    /* Keyframes gabungan */
    @keyframes fadeInOut {
        0% {
            opacity: 0;
            transform: translateY(-30px);
        }

        20% {
            opacity: 1;
            transform: translateY(0);
        }

        70% {
            opacity: 1;
            transform: translateY(0);
        }

        100% {
            opacity: 0;
            transform: translateY(-30px);
        }
    }
    </style>

</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">