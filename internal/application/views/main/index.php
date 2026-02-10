<div>
    <?php echo $header; ?>
    <?php
        if(!isset($_POST['exportExcel'])) {
            echo $topbar;
        }
    ?>
    <?php
        if(!isset($_POST['exportExcel'])) {
            echo $sidebar;
        }
    ?>
    <main class="<?= !isset($_POST['exportExcel']) ? 'lg:ml-64 pt-16 pb-16' : '' ?> min-h-screen transition-all duration-300" id="content-blur">
        <?php echo $content; ?>
    </main>
    <?php echo $ourjs; ?>
    <?php echo $footer; ?>
</div>
