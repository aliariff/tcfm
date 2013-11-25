<?php

	$this->load->view('template/header_' . $header_layout, array('scripts' => $scripts));
	
	$this->load->view('template/main_' . $page_layout, 
		array(
			'template_part' => $template_part,
			'sidebar' => $sidebar,
			'page_title' => $page_title
		)
	);
	
	$this->load->view('template/footer');
?>
