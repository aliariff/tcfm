<?php

class timsaya extends Logged_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'user/tim_saya';
		$template_data->header_layout = 'login';
		$template_data->page_layout = 'pure';

		$username = $this->sesilogin_model->username_login();

		$template_data->data->user_info = $this->user_model->user_info($username);
		$template_data->data->daftar_pemain_user = $this->user_model->daftar_pemain_user($username);
		$template_data->data->daftar_lineup_user = $this->user_model->daftar_lineup_user($username);
		$template_data->data->daftar_formasi = $this->formasi_model->daftar_formasi();

		if (!$this->input->is_ajax_request())
		{
			$this->load->view('template/index', $template_data);
		}
		else
		{
			$this->load->view('template/main_' . $template_data->page_layout, $template_data);
		}
    }

    function release()
    {
    	if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $username = $this->sesilogin_model->username_login();
            $id = $this->input->post('id');
            $result = $this->pemain_model->release_pemain($username, $id);
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

    function clear()
    {
    	$username = $this->sesilogin_model->username_login();
        $result = $this->user_model->clear_line_up($username);
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
?>