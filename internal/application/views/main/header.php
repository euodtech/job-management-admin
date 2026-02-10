<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
        $companyLogo = $this->session->userdata('CompanyLogo');
        $faviconUrl = ($companyLogo && $this->session->userdata('Role') != 1)
            ? base_url('assets/dist/img/company_logo/' . $companyLogo)
            : base_url('assets/dist/logo_efms.jpg');
    ?>
    <link rel="icon" href="<?= $faviconUrl ?>?v=<?= time() ?>" />
    <link rel="shortcut icon" href="<?= $faviconUrl ?>?v=<?= time() ?>" />

    <title><?php echo $title; ?></title>

    <!-- Tailwind CSS v3 CDN Play Script -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'Source Sans Pro', 'sans-serif'],
                },
                colors: {
                    primary: {
                        DEFAULT: '#070f26',
                        dark: '#0a1431',
                        light: '#1a2744',
                    },
                    accent: {
                        DEFAULT: '#4e54c8',
                        light: '#8f94fb',
                    },
                    destructive: '#da1e26',
                    surface: '#ffffff',
                    background: '#f4f6f9',
                    muted: '#6c757d',
                },
            },
        },
    }
    </script>

    <!-- Inter font (replaces Source Sans Pro) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Leaflet Control Geocoder CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <!-- summernote -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/summernote/summernote-bs4.min.css'); ?>">

    <!-- DataTables -->
    <link rel="stylesheet"
        href="<?php echo base_url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'); ?>">
    <link rel="stylesheet"
        href="<?php echo base_url('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css'); ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/select2/css/select2.min.css'); ?>">
    <link rel="stylesheet"
        href="<?php echo base_url('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css'); ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">

    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Open Layer -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/openlayers/10.6.1/ol.min.css" integrity="sha512-oVyisN6T8O7H9DnBc1w/IipxzLhNvJERKa0Rx9fKEtaodE7UXQAypIHamYzQPAqVxp0pVl25e4spVQWIVfu6eA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/10.6.1/dist/ol.min.js" integrity="sha512-NEUbbO7KI1OYn+IHcF70vm3ON0obczJz9PJFwxHkfPCsT14UqDD4roG7rF5WpwkXRTPvysFb6Wvw/Tjh5tfv8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Font Awesome 7 (full set) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/brands.min.css" integrity="sha512-WxpJXPm/Is1a/dzEdhdaoajpgizHQimaLGL/QqUIAjIihlQqlPQb1V9vkGs9+VzXD7rgI6O+UsSKl4u5K36Ydw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/fontawesome.min.css" integrity="sha512-M5Kq4YVQrjg5c2wsZSn27Dkfm/2ALfxmun0vUE3mPiJyK53hQBHYCVAtvMYEC7ZXmYLg8DVG4tF8gD27WmDbsg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/regular.min.css" integrity="sha512-x3gns+l9p4mIK7vYLOCUoFS2P1gavFvnO9Its8sr0AkUk46bgf9R51D8xeRUwCSk+W93YbXWi19BYzXDNBH5SA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/solid.min.css" integrity="sha512-EHa6vH03/Ty92WahM0/tet1Qicl76zihDCkBnFhN3kFGQkC+mc86d7V+6y2ypiLbk3h0beZAGdUpzfMcb06cMg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/map.min.css'); ?>">

    <style type="text/css">
    :root {
        --color-primary: #070f26;
        --color-primary-dark: #0a1431;
        --color-primary-light: #1a2744;
        --color-secondary: #6c757d;
        --color-accent: #4e54c8;
        --color-accent-light: #8f94fb;
        --color-background: #f4f6f9;
        --color-surface: #ffffff;
        --color-text: #212529;
        --color-muted: #6c757d;
        --color-destructive: #da1e26;
        --color-success: #28a745;
        --color-warning: #ffc107;
        --color-info: #17a2b8;
    }

    /* === KEEP: Status badges (used in DataTables JS renders) === */
    .ongoing_job {
        @apply inline-block whitespace-nowrap rounded-full bg-amber-100 px-2.5 py-0.5 text-sm font-semibold text-amber-800;
    }
    .finished_job {
        @apply inline-block whitespace-nowrap rounded-full bg-green-100 px-2.5 py-0.5 text-sm font-semibold text-green-800;
    }
    .awaiting_job {
        @apply inline-block whitespace-nowrap rounded-full bg-red-100 px-2.5 py-0.5 text-sm font-semibold text-red-800;
    }
    .reschedule_job {
        @apply inline-block whitespace-nowrap rounded-full bg-blue-100 px-2.5 py-0.5 text-sm font-semibold text-blue-800;
    }
    .completed_job {
        @apply inline-block whitespace-nowrap rounded-full bg-green-500 px-2.5 py-0.5 text-sm font-semibold text-white shadow-md shadow-green-500/40;
    }

    /* === KEEP: Loading blur overlay === */
    .blur { filter: blur(20px); }

    /* === KEEP: Bell shake animation (sidebar notification) === */
    .bell-shake { animation: shake 0.5s infinite; }
    @keyframes shake {
        0%   { transform: rotate(0deg); }
        25%  { transform: rotate(-15deg); }
        50%  { transform: rotate(15deg); }
        75%  { transform: rotate(-15deg); }
        100% { transform: rotate(0deg); }
    }

    /* === KEEP: Alert auto-dismiss animation === */
    .alert-auto-dismiss {
        animation: fadeInOut 6s forwards;
    }
    @keyframes fadeInOut {
        0%   { opacity: 0; transform: translateY(-30px); }
        20%  { opacity: 1; transform: translateY(0); }
        70%  { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-30px); }
    }

    /* === Flash message backward-compat (Option B) === */
    .alert-success { @apply rounded-lg bg-green-50 border border-green-200 px-4 py-2 text-sm text-green-800 inline-block; animation: fadeInOut 6s forwards; }
    .alert-danger { @apply rounded-lg bg-red-50 border border-red-200 px-4 py-2 text-sm text-red-800 inline-block; animation: fadeInOut 6s forwards; }
    .alert-warning { @apply rounded-lg bg-amber-50 border border-amber-200 px-4 py-2 text-sm text-amber-800 inline-block; animation: fadeInOut 6s forwards; }
    .alert-info { @apply rounded-lg bg-cyan-50 border border-cyan-200 px-4 py-2 text-sm text-cyan-800 inline-block; animation: fadeInOut 6s forwards; }

    /* === DataTables overrides for Tailwind === */
    table.dataTable { @apply w-full text-sm; }
    table.dataTable thead th { @apply bg-gray-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 border-b border-gray-200; }
    table.dataTable tbody td { @apply px-4 py-3 border-b border-gray-100; }
    table.dataTable tbody tr:hover { @apply bg-gray-50; }
    .dataTables_wrapper .dataTables_length select { @apply rounded-md border-gray-300 text-sm py-1 px-2; }
    .dataTables_wrapper .dataTables_filter input { @apply rounded-md border-gray-300 text-sm py-1.5 px-3 focus:ring-2 focus:ring-primary/20 focus:border-primary; }
    .dataTables_wrapper .dataTables_info { @apply text-sm text-gray-600 py-3; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { @apply px-3 py-1.5 text-sm rounded-md mx-0.5; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { @apply bg-primary text-white; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) { @apply bg-gray-100; }
    div.dataTables_wrapper div.dataTables_processing { @apply bg-white/80 rounded-lg; }
    .dt-buttons .dt-button { @apply inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors; }

    /* === DataTables Bootstrap layout compat === */
    .dataTables_wrapper { @apply text-sm; }
    .dataTables_wrapper .row { @apply flex flex-wrap items-center justify-between gap-4; }
    .dataTables_wrapper .col-sm-12 { @apply w-full; }
    .dataTables_wrapper .col-sm-12.col-md-6 { @apply w-full md:w-1/2; }

    /* === Select2 overrides for Tailwind === */
    .select2-container--bootstrap4 .select2-selection--single { @apply rounded-lg border-gray-300 h-10 flex items-center px-3 text-sm; }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow { @apply h-10 flex items-center; }
    .select2-container--bootstrap4 .select2-results__option--highlighted { @apply bg-primary text-white; }
    .select2-dropdown { @apply rounded-lg border border-gray-200 shadow-lg; }

    /* === Form validation error state === */
    .field-error .tw-input,
    .field-error input,
    .field-error select,
    .field-error textarea,
    .field-error .select2-selection { @apply !border-red-500; }
    .field-error .input-group-text-tw { @apply !border-red-500; }
    .inline-error { @apply text-red-500 text-xs mt-1 block; }

    /* === Gradient button utilities === */
    .btn-gradient-primary {
        @apply bg-gradient-to-r from-primary-dark to-black text-white;
        background-size: 200% 100%;
        background-position: left center;
        transition: background-position 1s ease;
    }
    .btn-gradient-primary:hover {
        background-position: right center;
    }
    .btn-gradient-accent {
        @apply bg-gradient-to-br from-accent to-accent-light text-white;
    }

    /* === DataTable button utility classes (for JS renders) === */
    .btn-tw-warning { @apply inline-flex items-center justify-center rounded-lg bg-amber-500 px-2 py-1 text-xs text-white hover:bg-amber-600 transition-colors; }
    .btn-tw-danger { @apply inline-flex items-center justify-center rounded-lg bg-destructive px-2 py-1 text-xs text-white hover:bg-red-700 transition-colors; }
    .btn-tw-success { @apply inline-flex items-center justify-center rounded-lg bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-700 transition-colors; }
    .btn-tw-info { @apply inline-flex items-center justify-center rounded-lg bg-cyan-600 px-2 py-1 text-xs text-white hover:bg-cyan-700 transition-colors; }
    .btn-tw-primary { @apply inline-flex items-center justify-center rounded-lg bg-primary px-2 py-1 text-xs text-white hover:bg-primary-dark transition-colors; }

    /* === SweetAlert custom loader === */
    .custom-swal-loader {
        border-top-color: #fff !important;
        border-right-color: black !important;
        border-bottom-color: #fff !important;
        border-left-color: black !important;
    }

    /* === Print hide === */
    @media print {
        .no-print { display: none !important; }
    }

    /* === Mobile DataTables adjustments === */
    @media (max-width: 991.98px) {
        .dataTables_info, .dataTables_paginate { font-size: 12px !important; margin-top: 0.75rem !important; }
        .dataTables_length, .dataTables_filter { font-size: 12px !important; }
    }
    </style>

    <script>
    (function() {
        var faviconUrl = "<?= $faviconUrl ?>";
        var link = document.querySelector("link[rel~='icon']");
        if (!link) {
            link = document.createElement('link');
            link.rel = 'icon';
            document.head.appendChild(link);
        }
        link.href = faviconUrl + '?v=' + Date.now();
    })();
    </script>

</head>

<body class="bg-background font-sans text-gray-800">
    <div class="min-h-screen">
