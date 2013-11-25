<?php

class dasbor extends Logged_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'user/dasbor';
		$template_data->header_layout = 'login';
		$template_data->page_layout = 'pure';

		$username = $this->sesilogin_model->username_login();

		$template_data->data->user_info = $this->user_model->user_info($username);
		$template_data->data->daftar_pemain_user = $this->user_model->daftar_pemain_user($username);
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