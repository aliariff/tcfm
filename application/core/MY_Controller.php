<?php

class MY_Controller extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		include (APPPATH . 'libraries/template_data.php');
		if ($this->sesilogin_model->apakah_login())
		{
			redirect(base_url('user/dasbor'));
		}
	}
}
