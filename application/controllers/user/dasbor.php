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
		$template_data->page_layout = 'content';

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

    function ubah_profil()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'user/ubah_profil';
		$template_data->header_layout = 'login';
		$template_data->page_layout = 'pure';
		$this->load->library('form_validation');

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$username = $username = $this->sesilogin_model->username_login();
			$this->form_validation->set_rules('nama_tim', 'Nama Tim', 'required');
			$this->form_validation->set_rules('nama_stadion', 'Nama Stadion', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required');
			
			$this->form_validation->set_message('required', '%s belum diisi');
			$this->form_validation->set_error_delimiters('', '<br/>');

			$template_data->data->nama_tim = $this->input->post('nama_tim');
			$template_data->data->nama_stadion = $this->input->post('nama_stadion');
			$template_data->data->email = $this->input->post('email');
			$template_data->data->foto_user = $this->input->post('foto_user');

			$json = new stdclass();
			if ($this->form_validation->run() == TRUE)
            {
            	$result = $this->user_model->ubah_profil
            	(	
            		$username,
            		$template_data->data->nama_tim, 
            		$template_data->data->nama_stadion, 
            		$template_data->data->email,
            		$template_data->data->foto_user
            	);
       			
            	if ($result->kode == 0)
                {
                	$json->sukses = true;
					$json->pesan = "<b>Sukses</b> Profil Berhasil Diubah";
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
			$username = $this->sesilogin_model->username_login();
			$template_data->data->user_info = $this->user_model->user_info($username);

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

    function ubah_password()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'user/ubah_password';
		$template_data->header_layout = 'login';
		$template_data->page_layout = 'pure';
		$this->load->library('form_validation');

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$username = $username = $this->sesilogin_model->username_login();
			$this->form_validation->set_rules('password_lama', 'Password Lama', 'required');
			$this->form_validation->set_rules('password_baru', 'Password Baru', 'required|matches[repassword_baru]');
			$this->form_validation->set_rules('repassword_baru', 'Ulangi Password Baru', 'required');
			
			$this->form_validation->set_message('required', '%s belum diisi');
			$this->form_validation->set_message('matches', 'Password Tidak Sama');
			$this->form_validation->set_error_delimiters('', '<br/>');

			$template_data->data->password_lama = $this->input->post('password_lama');
			$template_data->data->password_baru = $this->input->post('password_baru');
			$template_data->data->repassword_baru = $this->input->post('repassword_baru');

			$json = new stdclass();
			if ($this->form_validation->run() == TRUE)
            {
            	$result = $this->user_model->ubah_password
            	(
            		$username, $template_data->data->password_lama, $template_data->data->password_baru, $template_data->data->repassword_baru);
       			
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