<?php

class simulasi extends Logged_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'user/simulasi';
		$template_data->header_layout = 'login';
		$template_data->page_layout = 'content';

		if (!$this->input->is_ajax_request())
		{
			$this->load->view('template/index', $template_data);
		}
		else
		{
			$this->load->view('template/main_' . $template_data->page_layout, $template_data);
		}
    }

}
?>