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
    <?php echo $content; ?>
    <?php echo $ourjs; ?>
    <?php echo $footer; ?>
</div>