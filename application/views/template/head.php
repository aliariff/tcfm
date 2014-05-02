<link rel="stylesheet" href="<?php echo base_url('assets/css/bdl.css'); ?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.css'); ?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.css'); ?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap-notify.css'); ?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/jquery.fileupload.css'); ?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/blueimp-gallery.min.css'); ?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap-image-gallery.min.css'); ?>" />
<link rel="icon" href="<?php echo base_url('assets/ico/favicon.png'); ?>" type="image/png" />

<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery-ui.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap-notify.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.fileupload.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.blueimp-gallery.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap-image-gallery.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.fixedheadertable.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/tick.js'); ?>"></script>

<?php 
	foreach ($scripts as $script) {
		$this->load->view('scripts/' . $script);
	}
?>