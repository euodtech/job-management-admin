<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EFMS | Administrator</title>

    <link rel="icon" type="image/png" href="<?= base_url('assets/dist/img/company_logo/default-company-logo.png') ?>?v=<?= time() ?>" />
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/dist/img/company_logo/default-company-logo.png') ?>?v=<?= time() ?>" />

    <!-- Tailwind CSS v3 CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['Inter', 'sans-serif'] },
                colors: {
                    primary: { DEFAULT: '#070f26', dark: '#0a1431' },
                    accent: { DEFAULT: '#4e54c8', light: '#8f94fb' },
                    destructive: '#da1e26',
                },
            },
        },
    }
    </script>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style type="text/tailwindcss">
    /* Flash message backward-compat */
    .alert-success { @apply rounded-lg bg-green-50 border border-green-200 px-4 py-2 text-sm text-green-800 inline-block; }
    .alert-danger { @apply rounded-lg bg-red-50 border border-red-200 px-4 py-2 text-sm text-red-800 inline-block; }
    </style>

</head>

<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">
