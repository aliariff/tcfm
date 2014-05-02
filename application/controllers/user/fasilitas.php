<?php

class fasilitas extends Logged_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'user/fasilitas';
		$template_data->header_layout = 'login';
		$template_data->page_layout = 'pure';

		$username = $this->sesilogin_model->username_login();

		$template_data->data->user_info = $this->user_model->user_info($username);
		$template_data->data->daftar_stadion = $this->stadion_model->daftar_stadion();
		
		if (!$this->input->is_ajax_request())
		{
			$this->load->view('template/index', $template_data);
		}
		else
		{
			$this->load->view('template/main_' . $template_data->page_layout, $template_data);
		}
    }

    function upgrade_stadion()
    {
    	$username = $this->sesilogin_model->username_login();
    	$result = $this->stadion_model->upgrade_stadion($username);
    	$json = new stdclass();
    	if ($result->kode == 0)
        {
        	$json->sukses = true;
			$json->pesan = "<b>Sukses</b> $result->pesan";
    	}
        else
        {
        	$json->sukses = false;
			$json->pesan = "<b>Galat</b> $result->pesan";
    	}
    	echo json_encode($json);
    }

    function downgrade_stadion()
    {
    	$username = $this->sesilogin_model->username_login();
    	$result = $this->stadion_model->downgrade_stadion($username);
    	$json = new stdclass();
    	if ($result->kode == 0)
        {
        	$json->sukses = true;
			$json->pesan = "<b>Sukses</b> $result->pesan";
    	}
        else
        {
        	$json->sukses = false;
			$json->pesan = "<b>Galat</b> $result->pesan";
    	}
    	echo json_encode($json);
    }

    function cek_stadion()
    {
        $username = $this->sesilogin_model->username_login();
        $result = $this->stadion_model->cek_stadion($username);
        $json = new stdclass();
        if ($result->kode == 0)
        {
            $json->sukses = true;
            $json->pesan = "$result->pesan";
        }
        else
        {
            $json->sukses = false;
            $json->pesan = "$result->pesan";
        }
        echo json_encode($json);
    }
}
?>