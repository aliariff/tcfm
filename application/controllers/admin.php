<?php

class admin extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
    	redirect(base_url('admin/tambah_pemain'));
    }

    function tambah_pemain()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'admin/tambah_pemain';
		$template_data->header_layout = 'login_admin';
		$template_data->page_layout = 'content';
		$this->load->library('form_validation');

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$this->form_validation->set_rules('nama_pemain', 'Nama Pemain', 'required');
			$this->form_validation->set_rules('tim_asal', 'Tim Asal', 'required');
			$this->form_validation->set_rules('posisi', 'Posisi', 'required');
			$this->form_validation->set_rules('nilai_att', 'Nilai Attack', 'required');
			$this->form_validation->set_rules('nilai_def', 'Nilai Defense', 'required');
			$this->form_validation->set_rules('nilai_speed', 'Nilai Speed', 'required');
			$this->form_validation->set_rules('nilai_stamina', 'Nilai Stamina', 'required');

			$this->form_validation->set_message('required', '%s belum diisi');
			$this->form_validation->set_error_delimiters('', '<br/>');

			$template_data->data->nama_pemain = $this->input->post('nama_pemain');
			$template_data->data->tim_asal = $this->input->post('tim_asal');
			$template_data->data->posisi = $this->input->post('posisi');
			$template_data->data->nilai_att = $this->input->post('nilai_att');
			$template_data->data->nilai_def = $this->input->post('nilai_def');
			$template_data->data->nilai_speed = $this->input->post('nilai_speed');
			$template_data->data->nilai_stamina = $this->input->post('nilai_stamina');
			$template_data->data->flag_limited = $this->input->post('flag_limited');
			$template_data->data->flag_tersedia = $this->input->post('flag_tersedia');
			$template_data->data->flag_paket = $this->input->post('flag_paket');
			$template_data->data->foto_pemain = $this->input->post('foto_pemain');
			
			$json = new stdclass();
			if ($this->form_validation->run() == TRUE)
            {
            	if ($template_data->data->foto_pemain==null)
            	{
            		$template_data->data->foto_pemain = 'no-pic.png';
            	}
            	if ($template_data->data->flag_limited=="on")
            	{
            		$template_data->data->flag_limited = 1;
            	}
            	if ($template_data->data->flag_tersedia=="on")
            	{
            		$template_data->data->flag_tersedia = 1;
            	}
            	if ($template_data->data->flag_paket=="on")
            	{
            		$template_data->data->flag_paket = 1;
            	}
            	$result = $this->pemain_model->tambah_pemain(
            			$template_data->data->nama_pemain,
            			$template_data->data->tim_asal,
            			$template_data->data->posisi,
            			$template_data->data->nilai_att,
            			$template_data->data->nilai_def,
            			$template_data->data->nilai_speed,
            			$template_data->data->nilai_stamina,
            			$template_data->data->flag_limited,
            			$template_data->data->flag_tersedia,
            			$template_data->data->flag_paket,
            			$template_data->data->foto_pemain
            		);
       			
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
			$template_data->data->daftar_pemain = $this->pemain_model->daftar_pemain();

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

    function tambah_latihan()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'admin/tambah_latihan';
		$template_data->header_layout = 'login_admin';
		$template_data->page_layout = 'content';
		$this->load->library('form_validation');

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$this->form_validation->set_rules('nama_latihan', 'Nama Latihan', 'required');
			$this->form_validation->set_rules('durasi_latihan_tu', 'Duras Latihan (TU)', 'required');
			$this->form_validation->set_rules('penambahan_kekompakan', 'Penambahan Kekompakan', 'required');

			$this->form_validation->set_message('required', '%s belum diisi');
			$this->form_validation->set_error_delimiters('', '<br/>');

			$template_data->data->nama_latihan = $this->input->post('nama_latihan');
			$template_data->data->durasi_latihan_tu = $this->input->post('durasi_latihan_tu');
			$template_data->data->penambahan_kekompakan = $this->input->post('penambahan_kekompakan');
			$template_data->data->gambar_latihan = $this->input->post('gambar_latihan');
			
			$json = new stdclass();
			if ($this->form_validation->run() == TRUE)
            {
            	$result = $this->latihan_model->tambah_latihan(
            			$template_data->data->nama_latihan,
            			$template_data->data->durasi_latihan_tu,
            			$template_data->data->penambahan_kekompakan,
            			$template_data->data->gambar_latihan
            		);
       			
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
    }

    function tambah_formasi()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'admin/tambah_formasi';
		$template_data->header_layout = 'login_admin';
		$template_data->page_layout = 'content';
		$this->load->library('form_validation');

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$this->form_validation->set_rules('nama_formasi', 'Nama Formasi', 'required');

			$this->form_validation->set_message('required', '%s belum diisi');
			$this->form_validation->set_error_delimiters('', '<br/>');

			$template_data->data->nama_formasi = $this->input->post('nama_formasi');
			
			$json = new stdclass();
			if ($this->form_validation->run() == TRUE)
            {
            	$result = $this->formasi_model->tambah_formasi($template_data->data->nama_formasi);
       			
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
    }

	function setting()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'admin/setting';
		$template_data->header_layout = 'login_admin';
		$template_data->page_layout = 'content';
		$this->load->library('form_validation');

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$this->form_validation->set_rules('server_waktu_mulai', 'Server Waktu', 'required');
			$this->form_validation->set_rules('server_durasi_tu', 'Durasi Server', 'required');
			$this->form_validation->set_rules('ap_durasi_pertambahan_tu', 'Durasi Pertambahan AP', 'required');
			$this->form_validation->set_rules('ap_pertambahan', 'AP Pertambahan', 'required');
			$this->form_validation->set_rules('ap_maksimal', 'AP Maksimal', 'required');
			$this->form_validation->set_rules('ap_nilai_awal', 'AP Nilai Awal', 'required');
			$this->form_validation->set_rules('exp_nilai_awal', 'EXP Nilai Awal', 'required');
			$this->form_validation->set_rules('kekompakan_nilai_awal', 'Kekompakan Nilai Awal', 'required');
			$this->form_validation->set_rules('kekompakan_maksimal', 'Kekompakan Maksimal', 'required');
			$this->form_validation->set_rules('proteksi_durasi_tu', 'Durasi Proteksi', 'required');
			$this->form_validation->set_rules('balen_nilai_awal', 'Balen Nilai Awal', 'required');
			$this->form_validation->set_rules('uang_nilai_awal', 'Uang Nilai Awal', 'required');

			$this->form_validation->set_message('required', '%s belum diisi');
			$this->form_validation->set_error_delimiters('', '<br/>');

			$template_data->data->server_waktu_mulai 		= $this->input->post('server_waktu_mulai');
			$template_data->data->server_durasi_tu 			= $this->input->post('server_durasi_tu');
			$template_data->data->ap_durasi_pertambahan_tu 	= $this->input->post('ap_durasi_pertambahan_tu');
			$template_data->data->ap_pertambahan			= $this->input->post('ap_pertambahan');
			$template_data->data->ap_maksimal 				= $this->input->post('ap_maksimal');
			$template_data->data->ap_nilai_awal 			= $this->input->post('ap_nilai_awal');
			$template_data->data->exp_nilai_awal 			= $this->input->post('exp_nilai_awal');
			$template_data->data->kekompakan_nilai_awal 	= $this->input->post('kekompakan_nilai_awal');
			$template_data->data->kekompakan_maksimal 		= $this->input->post('kekompakan_maksimal');
			$template_data->data->proteksi_durasi_tu 		= $this->input->post('proteksi_durasi_tu');
			$template_data->data->balen_nilai_awal 			= $this->input->post('balen_nilai_awal');
			$template_data->data->uang_nilai_awal 			= $this->input->post('uang_nilai_awal');
			
			$json = new stdclass();
			if ($this->form_validation->run() == TRUE)
            {
            	$result = $this->setting_model->ubah_setting(
					$template_data->data->server_waktu_mulai,
					$template_data->data->server_durasi_tu,
					$template_data->data->ap_durasi_pertambahan_tu,
					$template_data->data->ap_pertambahan,
					$template_data->data->ap_maksimal,
					$template_data->data->ap_nilai_awal,
					$template_data->data->exp_nilai_awal,
					$template_data->data->kekompakan_nilai_awal,
					$template_data->data->kekompakan_maksimal,
					$template_data->data->proteksi_durasi_tu,
					$template_data->data->balen_nilai_awal,
					$template_data->data->uang_nilai_awal
            	);
       			
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
			$template_data->data->pengaturan_sistem = $this->setting_model->getAllSetting();

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

    function ubah_limited()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'admin/ubah_limited';
		$template_data->header_layout = 'login_admin';
		$template_data->page_layout = 'pure';

		$template_data->data->daftar_pemain_limited = $this->pemain_model->get_all_limited();
		$template_data->data->daftar_lineup_limited = $this->pemain_model->get_lineup_limited();

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
?>