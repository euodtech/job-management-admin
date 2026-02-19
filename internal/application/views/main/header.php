<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
        $companyLogo = $this->session->userdata('CompanyLogo');
        if ($companyLogo && file_exists(FCPATH . 'assets/dist/img/company_logo/' . $companyLogo)) {
            $faviconUrl = base_url('assets/dist/img/company_logo/' . $companyLogo);
            $faviconType = 'image/png';
        } else {
            $faviconUrl = base_url('assets/dist/img/company_logo/default-company-logo.png');
            $faviconType = 'image/png';
        }
        $faviconPath = FCPATH . 'assets/dist/img/company_logo/' . ($companyLogo ?: 'default-company-logo.png');
        $faviconVersion = file_exists($faviconPath) ? filemtime($faviconPath) : '1';
    ?>
    <link rel="icon" type="<?= $faviconType ?>" href="<?= $faviconUrl ?>?v=<?= $faviconVersion ?>" />
    <link rel="shortcut icon" type="<?= $faviconType ?>" href="<?= $faviconUrl ?>?v=<?= $faviconVersion ?>" />

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

    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <!-- jQuery (single load, pinned version — must be before any page scripts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Open Layer -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/openlayers/10.6.1/ol.min.css" integrity="sha512-oVyisN6T8O7H9DnBc1w/IipxzLhNvJERKa0Rx9fKEtaodE7UXQAypIHamYzQPAqVxp0pVl25e4spVQWIVfu6eA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/10.6.1/dist/ol.min.js" integrity="sha512-NEUbbO7KI1OYn+IHcF70vm3ON0obczJz9PJFwxHkfPCsT14UqDD4roG7rF5WpwkXRTPvysFb6Wvw/Tjh5tfv8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Font Awesome 7 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/map.min.css'); ?>">

    <style type="text/tailwindcss">
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

    /* === DataTables table rows — match dashboard hover & row height === */
    table.dataTable { width: 100% !important; font-size: 0.875rem !important; border-collapse: collapse !important; border-spacing: 0 !important; }
    table.dataTable thead th { background-color: #f9fafb !important; padding: 0.75rem 1rem !important; text-align: left !important; font-size: 0.75rem !important; font-weight: 600 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; color: #4b5563 !important; border-bottom: 1px solid #e5e7eb !important; border-top: none !important; }
    div.dataTables_scrollBody > table.dataTable > thead > tr > th,
    div.dataTables_scrollBody > table.dataTable > thead > tr > td { padding: 0 !important; height: 0 !important; line-height: 0 !important; font-size: 0 !important; border: none !important; border-bottom: none !important; overflow: hidden !important; }
    table.dataTable tbody td { padding: 0.75rem 1rem !important; border-bottom: 1px solid #f3f4f6 !important; vertical-align: middle !important; }
    table.dataTable tbody tr:hover { background-color: #f9fafb !important; }
    .dataTables_wrapper .dataTables_length { @apply flex items-center gap-2 text-sm text-gray-600; }
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
        color: #1f2937;
        outline: none;
        transition-property: color, background-color, border-color;
        transition-duration: 150ms;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
    .dataTables_wrapper .dataTables_filter input::placeholder {
        color: #9ca3af;
    }
    .dataTables_wrapper .dataTables_length select:focus,
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #070f26;
        box-shadow: 0 0 0 2px rgba(7, 15, 38, 0.2);
        outline: none;
    }
    .dataTables_wrapper .dataTables_filter { @apply flex items-center gap-2 text-sm text-gray-600; }
    .dataTables_wrapper .dataTables_info { @apply text-sm text-gray-600 py-3; }
    .dataTables_wrapper .dataTables_paginate { @apply py-3; }
    div.dataTables_wrapper div.dataTables_processing { display: none !important; }
    /* === DataTables pagination — match dashboard borderless style === */
    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.25rem !important;
        list-style: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    div.dataTables_wrapper div.dataTables_paginate .page-item {
        list-style: none !important;
    }
    div.dataTables_wrapper div.dataTables_paginate .page-item .page-link {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 2rem !important;
        height: 2rem !important;
        padding: 0 0.625rem !important;
        font-size: 0.875rem !important;
        font-weight: 500 !important;
        border-radius: 0.5rem !important;
        border: none !important;
        background-color: transparent !important;
        color: #1f2937 !important;
        text-decoration: none !important;
        cursor: pointer !important;
        user-select: none !important;
        transition: background-color 0.15s ease, color 0.15s ease !important;
        box-shadow: none !important;
    }
    div.dataTables_wrapper div.dataTables_paginate .page-item .page-link:hover {
        background-color: #f3f4f6 !important;
    }
    div.dataTables_wrapper div.dataTables_paginate .page-item.active .page-link,
    div.dataTables_wrapper div.dataTables_paginate .page-item.active .page-link:hover {
        background-color: #070f26 !important;
        color: #ffffff !important;
    }
    div.dataTables_wrapper div.dataTables_paginate .page-item.disabled .page-link {
        color: #1f2937 !important;
        background-color: transparent !important;
        opacity: 0.5 !important;
        cursor: not-allowed !important;
    }
    div.dataTables_wrapper div.dataTables_paginate .page-item.previous .page-link,
    div.dataTables_wrapper div.dataTables_paginate .page-item.next .page-link {
        font-size: 1.125rem !important;
    }
    .dt-buttons .dt-button { @apply inline-flex items-center gap-1.5 rounded-lg border !border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors; }

    /* DataTables button group layout */
    div div.dt-buttons {
        float: left !important;
        display: flex !important;
        justify-content: space-between !important;
        flex-direction: row;
        flex-wrap: nowrap;
        gap: 10px !important;
        align-items: flex-end;
    }
    .btn-group > .btn-group:not(:last-child) > .btn,
    .btn-group > .btn:not(:last-child):not(.dropdown-toggle) {
        border-radius: 0.5rem;
    }
    .btn-group > .btn-group:not(:first-child) > .btn,
    .btn-group > .btn:not(:first-child) {
        border-radius: 0.5rem;
    }

    /* Neutralize Bootstrap table classes for consistent Tailwind styling */
    table.table-bordered { border-collapse: collapse; border: none; }
    table.table-bordered th, table.table-bordered td { border: none; }
    table.table-striped tbody tr:nth-of-type(odd) { background-color: transparent; }
    table.table-striped tbody tr:hover { @apply bg-gray-50; }

    /* === Force DataTables scroll containers to fill available width === */
    .dataTables_wrapper { width: 100% !important; }
    .dataTables_scroll { width: 100% !important; }
    .dataTables_scrollHead,
    .dataTables_scrollBody,
    .dataTables_scrollFoot { width: 100% !important; overflow-x: auto; }
    .dataTables_scrollHead table.dataTable,
    .dataTables_scrollBody table.dataTable,
    .dataTables_scrollFoot table.dataTable { width: 100% !important; }

    /* === DataTables Bootstrap layout compat === */
    .dataTables_wrapper { @apply text-sm; }
    .dataTables_wrapper .row { @apply flex flex-wrap items-center justify-between gap-4; }
    .dataTables_wrapper .col-sm-12 { @apply w-full; }
    .dataTables_wrapper .col-sm-12.col-md-5 { @apply w-full md:w-5/12; }
    .dataTables_wrapper .col-sm-12.col-md-6 { @apply w-full md:w-1/2; }
    .dataTables_wrapper .col-sm-12.col-md-7 { @apply w-full; }

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

    /* === Preline hs-overlay-open: variant polyfill ===
       Tailwind CDN play script doesn't include Preline's plugin,
       so hs-overlay-open:* classes generate no CSS. These rules
       make them work when the parent .hs-overlay has the .open class. */
    .hs-overlay.open .hs-overlay-open\:opacity-100 { opacity: 1 !important; }
    .hs-overlay.open .hs-overlay-open\:mt-7 { margin-top: 1.75rem !important; }
    .hs-overlay.open .hs-overlay-open\:duration-500 { transition-duration: 500ms !important; }

    /* Overlay backdrop */
    .hs-overlay { visibility: hidden; }
    .hs-overlay.open { visibility: visible; }
    .hs-overlay.open::after {
        content: '';
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: -1;
    }
    </style>

    <script>
    (function() {
        var faviconUrl = "<?= $faviconUrl ?>";
        // Remove any existing favicon links (they may be outside <head> due to template structure)
        document.querySelectorAll("link[rel~='icon'], link[rel='shortcut icon']").forEach(function(el) {
            el.parentNode.removeChild(el);
        });
        // Create fresh favicon link inside document.head so the browser recognizes it
        var link = document.createElement('link');
        link.rel = 'icon';
        link.type = '<?= $faviconType ?>';
        link.href = faviconUrl + '?v=<?= $faviconVersion ?>';
        document.head.appendChild(link);
    })();
    </script>

</head>

<body class="bg-background font-sans text-gray-800">
    <div class="min-h-screen">
