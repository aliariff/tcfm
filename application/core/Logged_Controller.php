<?php

class Logged_Controller extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		include (APPPATH . 'libraries/template_data.php');
		$this->load->model('user_model');
		$this->load->model('pemain_model');
		if (!$this->sesilogin_model->apakah_login())
		{
			redirect(base_url('home'));
		}
	}
}
