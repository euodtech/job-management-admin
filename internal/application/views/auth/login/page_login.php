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
            <form action="<?php echo base_url('auth/login'); ?>" method="post">
                <div class="input-group mb-3">
                    <input type="email" autofocus class="form-control" name="email" placeholder="Email"
                        autocomplete="off" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fa fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fa fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="loginnich btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>