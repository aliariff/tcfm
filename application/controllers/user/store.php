<?php

class store extends Logged_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $template_data = new TemplateData();
        $template_data->template_part = 'user/store';
        $template_data->header_layout = 'login';
        $template_data->page_layout = 'pure';

        $username = $this->sesilogin_model->username_login();
        $template_data->data->user_info = $this->user_model->user_info($username);
        $template_data->data->daftar_paket = $this->paket_model->daftar_paket();

        if (!$this->input->is_ajax_request())
        {
            $this->load->view('template/index', $template_data);
        }
        else
        {
            $this->load->view('template/main_' . $template_data->page_layout, $template_data);
        }
    }

    function beli_uang()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $template_data = new TemplateData();
            $template_data->template_part = 'user/galeri_foto';
            $template_data->header_layout = 'login';
            $template_data->page_layout = 'pure';

            $username = $this->sesilogin_model->username_login();
            $id_paket = $this->input->post('id_paket');
            $result = $this->pemain_model->beli_pemain($username, $id_paket, 1);
            
            $arr = array();

            if ($result->kode == 0)
            {
                $id_pemain = explode('.', $result->pesan);
                foreach ($id_pemain as $value)
                {
                    if ($value)
                    {
                        $temp = $this->pemain_model->detail_pemain_id($value);
                        array_push($arr, $temp);
                    }
                }
                $template_data->data->daftar_pemain = $arr;
                $template_data->data->sukses = true;
            }
            else
            {
                $template_data->data->daftar_pemain = $result->pesan;
                $template_data->data->sukses = false;
            }

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

    function beli_balen()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $template_data = new TemplateData();
            $template_data->template_part = 'user/galeri_foto';
            $template_data->header_layout = 'login';
            $template_data->page_layout = 'pure';

            $username = $this->sesilogin_model->username_login();
            $id_paket = $this->input->post('id_paket');
            $result = $this->pemain_model->beli_pemain($username, $id_paket, 0);
            
            $arr = array();

            if ($result->kode == 0)
            {
                $id_pemain = explode('.', $result->pesan);
                foreach ($id_pemain as $value)
                {
                    if ($value)
                    {
                        $temp = $this->pemain_model->detail_pemain_id($value);
                        array_push($arr, $temp);
                    }
                }
                $template_data->data->daftar_pemain = $arr;
                $template_data->data->sukses = true;
            }
            else
            {
                $template_data->data->daftar_pemain = $result->pesan;
                $template_data->data->sukses = false;
            }

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

    function topup_balen()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $username = $this->sesilogin_model->username_login();
            $kode = $this->input->post('kode');
            $result = $this->user_model->topup_balen($username, $kode);
            $json = new stdclass();
            if ($result->kode == 0)
            {
                $json->sukses = true;
                $json->pesan = $result->pesan;;
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