<style type="text/css">
/* .login-page {
    background-color: #070f26;
} */

.loginnich {
    background-color: #da1e26;
    border-color: #da1e26;
}

.btn-primary {
    background: linear-gradient(135deg, #251abe, #9f22f0) !important;
    border-color: unset !important;
    transition: transform 0.2s ease-in-out;
    /* biar animasi halus */
}

.btn-primary:hover {
    transform: scale(1.02);
    /* agak gede 5% pas hover */
}

.card {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
}


.card-body {
    border-radius: 45px;
    /* Adjust the value as needed */
}

.field-error .form-control {
    border-color: #dc3545 !important;
}
.field-error .input-group-text {
    border-color: #dc3545 !important;
}
.inline-error {
    color: #dc3545;
    font-size: 0.8rem;
    margin-top: 4px;
    display: block;
}
</style>
<div class="login-box">
    <div class="login-logo">
        <a href="<?php echo base_url('auth'); ?>">
        </a>

    </div>
    <!-- /.login-logo -->
    <div class="card card-image">
        <div class="card-header text-center" style="background-color:white;">
            <img class="img-logo-text" src="<?= base_url('assets/dist/logo_efms.jpg') ?>" style="width:50%;">
        </div>
        <div class="card-body login-card-body">
            <p class="login-box-msg">Administrator Sign In</p>
            <?php echo $this->session->flashdata('message'); ?>
            <form id="loginForm" action="<?php echo base_url('auth/login'); ?>" method="post" novalidate>
                <div class="mb-3">
                    <div class="input-group" id="email-group">
                        <input type="email" autofocus class="form-control" name="email" id="login-email" placeholder="Email"
                            autocomplete="off">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fa fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <span class="inline-error" id="email-error"></span>
                </div>
                <div class="mb-3">
                    <div class="input-group" id="password-group">
                        <input type="password" class="form-control" name="password" id="login-password" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fa fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <span class="inline-error" id="password-error"></span>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="loginnich btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
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
        <!-- /.login-card-body -->
    </div>
</div>