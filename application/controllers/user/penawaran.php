<?php

class penawaran extends Logged_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'user/penawaran';
		$template_data->header_layout = 'login';
		$template_data->page_layout = 'content';

		$username = $this->sesilogin_model->username_login();

		$template_data->data->daftar_penawaran_masuk = $this->penawaran_model->daftar_penawaran_masuk($username);
        $template_data->data->daftar_penawaran_keluar = $this->penawaran_model->daftar_penawaran_keluar($username);
		
        $notif_masuk = 0;
        $notif_keluar = 0;
        
        foreach((array)$template_data->data->daftar_penawaran_masuk as $value)
        {
          if ($value->jawaban==99) $notif_masuk++;
        }

        foreach((array)$template_data->data->daftar_penawaran_keluar as $value)
        {
          if ($value->jawaban!=99 && $value->terbaca==0) $notif_keluar++;
        }

        $template_data->data->notif_masuk = $notif_masuk;
        $template_data->data->notif_keluar = $notif_keluar;

		if (!$this->input->is_ajax_request())
		{
			$this->load->view('template/index', $template_data);
		}
		else
		{
			$this->load->view('template/main_' . $template_data->page_layout, $template_data);
		}
    }

    function terima()
    {
        $id_penawaran = $this->input->post('id');
        $username = $this->sesilogin_model->username_login();
        $result = $this->penawaran_model->terima_tawaran($username, $id_penawaran);
        $json = new stdclass();
        $json = $result;
        echo json_encode($json);
    }

    function tolak()
    {
        $id_penawaran = $this->input->post('id');
        $username = $this->sesilogin_model->username_login();
        $result = $this->penawaran_model->tolak_tawaran($username, $id_penawaran);
        $json = new stdclass();
        $json = $result;
        echo json_encode($json);
    }

    function baca()
    {
        $username = $this->sesilogin_model->username_login();
        $this->penawaran_model->baca_tawaran($username);
    }

    function batalkan()
    {
        $id_penawaran = $this->input->post('id');
        $username = $this->sesilogin_model->username_login();
        $result = $this->penawaran_model->batalkan_tawaran($username, $id_penawaran);
        $json = new stdclass();
        $json = $result;
        echo json_encode($json);
    }
}
?>