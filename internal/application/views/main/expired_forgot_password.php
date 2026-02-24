<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Expired | EFMS</title>

    <link rel="icon" type="image/png" href="<?= base_url('assets/dist/img/company_logo/default-company-logo.png') ?>?v=<?= time() ?>" />

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
</head>

<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md mx-auto px-4">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Logo Header -->
            <div class="bg-white px-6 pt-8 pb-4 text-center">
                <img class="h-14 mx-auto object-contain" src="<?= base_url('assets/dist/logo_efms.jpg') ?>" alt="EFMS Logo">
            </div>

            <!-- Body -->
            <div class="px-8 pb-8 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-amber-600"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-800 mb-2">This Link Has Expired</h2>
                <p class="text-sm text-gray-500 mb-1">The reset link is expired or has already been used.</p>
                <p class="text-sm text-gray-500 mb-6">If you still need to reset your password, please request a new link from the mobile app.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 text-sm text-gray-500">
            <strong>Copyright &copy; <?= date('Y') ?></strong> &mdash; All rights reserved.
        </div>
    </div>

    <!-- Preline UI -->
    <script src="https://cdn.jsdelivr.net/npm/preline@2/dist/preline.min.js"></script>
</body>

</html>
