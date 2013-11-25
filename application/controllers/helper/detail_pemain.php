<?php

class detail_pemain extends Logged_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id_user_pemain = $this->input->post('id');
            $id_user_pemain = trim($id_user_pemain);
            $username = $username = $this->sesilogin_model->username_login();
            $result = $this->pemain_model->detail_pemain($username, $id_user_pemain);
            $json = new stdclass();
            $json->sukses = true;
            $json->pesan = $result;
            echo json_encode($json);
        }
    }

}
?>