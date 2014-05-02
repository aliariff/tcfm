<?php

class latihan extends Logged_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'user/latihan';
		$template_data->header_layout = 'login';
		$template_data->page_layout = 'pure';

		$username = $this->sesilogin_model->username_login();

		$template_data->data->user_info = $this->user_model->user_info($username);
		$template_data->data->daftar_latihan = $this->latihan_model->daftar_latihan();
		
		if (!$this->input->is_ajax_request())
		{
			$this->load->view('template/index', $template_data);
		}
		else
		{
			$this->load->view('template/main_' . $template_data->page_layout, $template_data);
		}
    }

    function lakukan_latihan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $username = $this->sesilogin_model->username_login();
            $id_latihan = $this->input->post('id_latihan');
            $result = $this->latihan_model->lakukan_latihan($username, $id_latihan);
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
    }

    function cek_latihan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $result = $this->latihan_model->cek_latihan();
            $json = new stdclass();
            if ($result->kode == 0)
            {
                $json->sukses = true;
                $json->pesan = $result->pesan;
            }
            else
            {
                $json->sukses = false;
                $json->pesan = $result->pesan;
            }
            echo json_encode($json);
        }
    }
}
?>