<?php

class liga extends Logged_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'user/liga';
		$template_data->header_layout = 'login';
		$template_data->page_layout = 'content';

        $username = $this->sesilogin_model->username_login();
        $template_data->data->user_info = $this->user_model->user_info($username);
        $template_data->data->daftar_tim_ai = $this->timai_model->daftar_tim_ai($username);

		if (!$this->input->is_ajax_request())
		{
			$this->load->view('template/index', $template_data);
		}
		else
		{
			$this->load->view('template/main_' . $template_data->page_layout, $template_data);
		}
    }

    function tantang()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $att = $this->sesilogin_model->username_login();
            $def = $this->input->post('id');
            $result = $this->user_model->tanding_ai($att, $def);
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

    function lihat_pemain()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $tim_ai_id = $this->input->post('id');
            
            $template_data = new TemplateData();
            $template_data->template_part = 'user/tim_ai';
            $template_data->header_layout = 'login';
            $template_data->page_layout = 'pure';

            $template_data->data->formasi_ai = $this->timai_model->getFormasiAI($tim_ai_id);
            $template_data->data->daftar_lineup_ai = $this->timai_model->lineup_timai($tim_ai_id);
            
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

}
?>