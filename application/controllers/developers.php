<?php

class developers extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        include (APPPATH . 'libraries/template_data.php');
    }

    function index()
    {
        $template_data = new TemplateData();
		$template_data->template_part = 'home/developers';
		$template_data->header_layout = 'no_login';
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