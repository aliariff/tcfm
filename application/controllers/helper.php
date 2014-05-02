<?php

class helper extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('pemain_model');
        $this->load->model('formasi_model');
    }

    function index()
    {

    }

    function detail_pemain()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id_user_pemain = $this->input->post('id');
            $id_user_pemain = trim($id_user_pemain);
            $result = $this->pemain_model->detail_pemain($id_user_pemain);
            $json = new stdclass();
            $json->sukses = true;
            $json->pesan = $result;
            echo json_encode($json);
        }
    }

    function update_lineup()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id_user_pemain = $this->input->post('id_user_pemain');
            $posisi = $this->input->post('posisi');
            $id_user_pemain = trim($id_user_pemain);
            $posisi = trim($posisi);
            $username = $this->sesilogin_model->username_login();
            $result = $this->pemain_model->update_lineup($username, $id_user_pemain, $posisi);
            $json = new stdclass();
            $json->pesan = $result;
            echo json_encode($json);
        }
    }

    function update_lineup_limited()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id_pemain = $this->input->post('id_pemain');
            $posisi = $this->input->post('posisi');
            $id_pemain = trim($id_pemain);
            $posisi = trim($posisi);
            $result = $this->pemain_model->update_lineup_limited($id_pemain, $posisi);
            $json = new stdclass();
            $json->pesan = $result;
            echo json_encode($json);
        }
    }

    function best_team_lineup()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $username = $this->sesilogin_model->username_login();
            $result = $this->user_model->best_team_lineup($username);
            $json = new stdclass();
            $json->pesan = $result;
            echo json_encode($json);
        }
    }

    function update_formasi()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id_formasi = $this->input->post('id_formasi');
            $username = $this->sesilogin_model->username_login();
            $result = $this->formasi_model->update_formasi($username, $id_formasi);
            $json = new stdclass();
            $json->pesan = $result;
            echo json_encode($json);
        }
    }
}
?>