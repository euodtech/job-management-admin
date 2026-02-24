<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | EFMS</title>

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

    <style type="text/tailwindcss">
    .alert-danger { @apply rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800; }
    .field-error input { @apply !border-red-500; }
    </style>
</head>

<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md mx-auto px-4">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Logo Header -->
            <div class="bg-white px-6 pt-8 pb-4 text-center">
                <img class="h-14 mx-auto object-contain" src="<?= base_url('assets/dist/logo_efms.jpg') ?>" alt="EFMS Logo">
            </div>

            <!-- Form Body -->
            <div class="px-8 pb-8">
                <h2 class="text-center text-lg font-semibold text-gray-800 mb-1">Reset Your Password</h2>
                <p class="text-center text-gray-500 text-sm mb-6">Enter your new password below</p>

                <!-- Server-side error display -->
                <?php if (!empty($error)): ?>
                <div class="alert-danger mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <!-- Client-side error display -->
                <div id="client-error" class="alert-danger mb-4 hidden" role="alert"></div>

                <form id="resetForm" action="<?= base_url('Auth/submit_new_password') ?>" method="post" novalidate>
                    <?php if (config_item('csrf_protection')): ?>
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <?php endif; ?>
                    <input type="hidden" name="reset_token" value="<?= htmlspecialchars($token) ?>">

                    <!-- New Password -->
                    <div class="mb-4">
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
                        <div class="relative">
                            <input type="password" name="new_password" id="new_password"
                                   placeholder="Enter new password" required autocomplete="new-password"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 pr-10 text-sm placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors">
                            <button type="button" id="toggle-new" tabindex="-1"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <i class="fa-regular fa-eye" id="icon-new"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Requirements -->
                    <div class="mb-4 px-1">
                        <p class="text-xs font-medium text-gray-500 mb-1.5">Password requirements:</p>
                        <ul class="space-y-1">
                            <li id="req-length" class="flex items-center gap-2 text-xs text-gray-400">
                                <i class="fa-solid fa-circle text-[6px]"></i>
                                <span>At least 8 characters</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <input type="password" name="confirm_password" id="confirm_password"
                                   placeholder="Confirm new password" required autocomplete="new-password"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 pr-10 text-sm placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors">
                            <button type="button" id="toggle-confirm" tabindex="-1"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <i class="fa-regular fa-eye" id="icon-confirm"></i>
                            </button>
                        </div>
                        <p id="match-message" class="text-xs mt-1.5 hidden"></p>
                    </div>

                    <!-- Submit -->
                    <button type="submit" id="btn-submit" disabled
                            class="w-full rounded-lg bg-gradient-to-r from-[#251abe] to-[#9f22f0] px-4 py-2.5 text-sm font-semibold text-white hover:scale-[1.02] transition-transform disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                        Reset Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 text-sm text-gray-500">
            <strong>Copyright &copy; <?= date('Y') ?></strong> &mdash; All rights reserved.
        </div>
    </div>

    <!-- Preline UI -->
    <script src="https://cdn.jsdelivr.net/npm/preline@2/dist/preline.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var newPw      = document.getElementById('new_password');
        var confirmPw  = document.getElementById('confirm_password');
        var matchMsg   = document.getElementById('match-message');
        var btnSubmit  = document.getElementById('btn-submit');
        var reqLength  = document.getElementById('req-length');
        var clientErr  = document.getElementById('client-error');

        // Show/hide password toggle
        function setupToggle(btnId, inputId, iconId) {
            var btn   = document.getElementById(btnId);
            var input = document.getElementById(inputId);
            var icon  = document.getElementById(iconId);
            btn.addEventListener('click', function() {
                var isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                icon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
            });
        }
        setupToggle('toggle-new', 'new_password', 'icon-new');
        setupToggle('toggle-confirm', 'confirm_password', 'icon-confirm');

        function validate() {
            var pw  = newPw.value;
            var cpw = confirmPw.value;
            var lengthOk = pw.length >= 8;
            var matchOk  = pw.length > 0 && cpw.length > 0 && pw === cpw;

            // Update requirements checklist
            var icon = reqLength.querySelector('i');
            if (pw.length === 0) {
                reqLength.className = 'flex items-center gap-2 text-xs text-gray-400';
                icon.className = 'fa-solid fa-circle text-[6px]';
            } else if (lengthOk) {
                reqLength.className = 'flex items-center gap-2 text-xs text-green-600';
                icon.className = 'fa-solid fa-check text-[10px]';
            } else {
                reqLength.className = 'flex items-center gap-2 text-xs text-red-500';
                icon.className = 'fa-solid fa-xmark text-[10px]';
            }

            // Update match message
            if (cpw.length === 0) {
                matchMsg.classList.add('hidden');
                matchMsg.textContent = '';
            } else if (matchOk) {
                matchMsg.classList.remove('hidden');
                matchMsg.className = 'text-xs mt-1.5 text-green-600';
                matchMsg.textContent = 'Passwords match';
            } else {
                matchMsg.classList.remove('hidden');
                matchMsg.className = 'text-xs mt-1.5 text-red-500';
                matchMsg.textContent = 'Passwords do not match';
            }

            // Enable/disable submit
            btnSubmit.disabled = !(lengthOk && matchOk);

            // Hide client error when user is correcting
            clientErr.classList.add('hidden');
        }

        newPw.addEventListener('input', validate);
        confirmPw.addEventListener('input', validate);

        // Final guard on form submit
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            if (newPw.value.length < 8 || newPw.value !== confirmPw.value) {
                e.preventDefault();
                clientErr.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-1"></i> Please fix the errors above before submitting.';
                clientErr.classList.remove('hidden');
            }
        });
    });
    </script>
</body>

</html>
