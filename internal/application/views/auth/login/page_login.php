<style type="text/tailwindcss">
.field-error input { @apply !border-red-500; }
.field-error .input-icon-box { @apply !border-red-500; }
.inline-error { @apply text-red-500 text-xs mt-1 block; }
</style>

<div class="w-full max-w-md mx-auto px-4">
    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Logo Header -->
        <div class="bg-white px-6 pt-8 pb-4 text-center">
            <img class="h-14 mx-auto object-contain" src="<?= base_url('assets/dist/logo_efms.jpg') ?>" alt="EFMS Logo">
        </div>

        <!-- Form Body -->
        <div class="px-8 pb-8">
            <p class="text-center text-gray-500 text-sm mb-6">Administrator Sign In</p>

            <?php echo $this->session->flashdata('message'); ?>

            <form id="loginForm" action="<?php echo base_url('auth/login'); ?>" method="post" novalidate>
                <!-- Email -->
                <div class="mb-4" id="email-wrapper">
                    <div class="flex" id="email-group">
                        <input type="email" autofocus
                               class="flex-1 rounded-l-lg border border-gray-300 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                               name="email" id="login-email" placeholder="Email" autocomplete="off">
                        <span class="input-icon-box inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3">
                            <i class="fa fa-envelope text-gray-400 text-sm"></i>
                        </span>
                    </div>
                    <span class="inline-error" id="email-error"></span>
                </div>

                <!-- Password -->
                <div class="mb-6" id="password-wrapper">
                    <div class="flex" id="password-group">
                        <input type="password"
                               class="flex-1 rounded-l-lg border border-gray-300 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                               name="password" id="login-password" placeholder="Password">
                        <span class="input-icon-box inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3">
                            <i class="fa fa-lock text-gray-400 text-sm"></i>
                        </span>
                    </div>
                    <span class="inline-error" id="password-error"></span>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full rounded-lg bg-gradient-to-r from-[#251abe] to-[#9f22f0] px-4 py-2.5 text-sm font-semibold text-white hover:scale-[1.02] transition-transform">
                    Sign In
                </button>
            </form>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var form = document.getElementById('loginForm');
                var emailInput = document.getElementById('login-email');
                var passwordInput = document.getElementById('login-password');
                var emailError = document.getElementById('email-error');
                var passwordError = document.getElementById('password-error');
                var emailGroup = document.getElementById('email-group');
                var passwordGroup = document.getElementById('password-group');
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                function clearFieldError(group, errorEl) {
                    group.parentElement.classList.remove('field-error');
                    errorEl.textContent = '';
                }

                function setFieldError(group, errorEl, msg) {
                    group.parentElement.classList.add('field-error');
                    errorEl.textContent = msg;
                }

                emailInput.addEventListener('input', function() {
                    clearFieldError(emailGroup, emailError);
                });
                passwordInput.addEventListener('input', function() {
                    clearFieldError(passwordGroup, passwordError);
                });

                form.addEventListener('submit', function(e) {
                    var valid = true;
                    var emailVal = emailInput.value.trim();
                    var passVal = passwordInput.value;

                    // Reset
                    clearFieldError(emailGroup, emailError);
                    clearFieldError(passwordGroup, passwordError);

                    if (!emailVal) {
                        setFieldError(emailGroup, emailError, 'Email is required.');
                        valid = false;
                    } else if (!emailRegex.test(emailVal)) {
                        setFieldError(emailGroup, emailError, 'Please enter a valid email address.');
                        valid = false;
                    }

                    if (!passVal) {
                        setFieldError(passwordGroup, passwordError, 'Password is required.');
                        valid = false;
                    }

                    if (!valid) {
                        e.preventDefault();
                    }
                });
            });
            </script>
        </div>
    </div>
</div>
