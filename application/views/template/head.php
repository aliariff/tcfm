<link rel="stylesheet" href="<?php echo base_url('assets/css/bdl.css'); ?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.css'); ?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.css'); ?>" />

<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery-ui.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>

<?php 
	foreach ($scripts as $script) {
		$this->load->view('scripts/' . $script);
	}
?>