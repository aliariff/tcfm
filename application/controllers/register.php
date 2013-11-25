<?php

class register extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    function index()
    {
        $template_data = new TemplateData();
		$template_data->template_part = 'register/index';
		$template_data->header_layout = 'no_login';
		$template_data->page_layout = 'content';
		$this->load->library('form_validation');

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$this->form_validation->set_rules('username', 'Username', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required');
			$this->form_validation->set_rules('password', 'Password', 'required|matches[repassword]');
			$this->form_validation->set_rules('repassword', 'Ulangi Password', 'required');
			$this->form_validation->set_rules('setuju', 'Persetujuan', 'required');

			$this->form_validation->set_message('matches', '%s tidak cocok');
			$this->form_validation->set_message('required', '%s belum diisi');
			$this->form_validation->set_error_delimiters('', '<br/>');

			$template_data->data->username = $this->input->post('username');
			$template_data->data->password = $this->input->post('password');
			$template_data->data->email = $this->input->post('email');

			$json = new stdclass();
			if ($this->form_validation->run() == TRUE)
            {
            	$result = $this->user_model->register($template_data->data->username, $template_data->data->password, $template_data->data->email);
       			
            	if ($result->kode == 0)
                {
                	$json->sukses = true;
					$json->pesan = "<b>Sukses</b> Pendaftaran Berhasil, Silahkan Login";
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

    function syaratketentuan()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'register/syaratketentuan';
		$template_data->header_layout = 'no_login';

		if (!$this->input->is_ajax_request())
		{
			$template_data->page_layout = 'content';
			$this->load->view('template/index', $template_data);
		}
		else
		{
			$template_data->page_layout = 'pure';
			$this->load->view('template/main_' . $template_data->page_layout, $template_data);
		}	
    }

    function kebijakanprivasi()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'register/kebijakanprivasi';
		$template_data->header_layout = 'no_login';

		if (!$this->input->is_ajax_request())
		{
			$template_data->page_layout = 'content';
			$this->load->view('template/index', $template_data);
		}
		else
		{
			$template_data->page_layout = 'pure';
			$this->load->view('template/main_' . $template_data->page_layout, $template_data);
		}		
    }
}
?>