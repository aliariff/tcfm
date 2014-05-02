<?php

class profil extends Logged_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
    	redirect(base_url('user/dasbor'));
    }

    function view($username = null)
    {
    	$now = $this->sesilogin_model->username_login();
    	if ($username == null)
    	{
	    	redirect(base_url('user/dasbor'));
		}
		else if ($username == $now)
		{
			redirect(base_url('user/timsaya'));
		}

		$template_data = new TemplateData();
		$template_data->template_part = 'user/profil';
		$template_data->header_layout = 'login';
		$template_data->page_layout = 'pure';

		$template_data->data->user_info = $this->user_model->user_info($username);
		$template_data->data->daftar_pemain_user = $this->user_model->daftar_pemain_user($username);
		$template_data->data->daftar_lineup_user = $this->user_model->daftar_lineup_user($username);

		if (!$this->input->is_ajax_request())
		{
			$this->load->view('template/index', $template_data);
		}
		else
		{
			$this->load->view('template/main_' . $template_data->page_layout, $template_data);
		}
    }

    function tawar_pemain()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'user/tawar_pemain';
		$template_data->header_layout = 'login';
		$template_data->page_layout = 'pure';
		$this->load->library('form_validation');

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$username = $this->sesilogin_model->username_login();
			$this->form_validation->set_rules('harga', 'Harga Penawaran', 'required');
			$this->form_validation->set_message('required', '%s belum diisi');
			$this->form_validation->set_error_delimiters('', '<br/>');

			$id_pemain = $this->input->post('id_pemain');
			$harga = str_replace('.', '', $this->input->post('harga'));
			$pesan = $this->input->post('pesan');
			$pemain = $this->pemain_model->detail_pemain($id_pemain);

			$json = new stdclass();
			if ($this->form_validation->run() == TRUE)
            {
            	$result = $this->penawaran_model->tawar_pemain($harga, $pemain->user_username, $username, $id_pemain, $pesan);
       			
            	if ($result->kode == 0)
                {
                	$json->sukses = true;
					$json->pesan = "<b>Sukses</b> $result->pesan";
					echo json_encode($json);
            	}
                else
                {

                	$json->sukses = false;
					$json->pesan = "<b>Galat</b> $result->pesan";
					echo json_encode($json);
            	}
            }
            else//gagal validasi
            {
                if (!$this->input->is_ajax_request())// rekues biasa
				{
					$template_data->data->galat = $this->form_validation->error_array();

					$this->load->view('template/index', $template_data);
				}
				else//rekues ajax
				{
					$json->sukses = false;
					$json->pesan = "<b>Galat</b><br/>" . validation_errors();
					echo json_encode($json);
				}
            }
		}
		else
		{
			$id_pemain = $this->input->get('id');
			$template_data->data->pemain = $this->pemain_model->detail_pemain($id_pemain);

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