<?php

class login extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $username = $this->input->post('username1');
        $password = $this->input->post('password1');

       	$json = new stdclass();
        if($this->sesilogin_model->login($username, $password))
        {
        	$json->sukses = true;
        	$json->pesan = "Sukses";
        	echo json_encode($json);
        }
        else
        {
        	$json->sukses = false;
        	$json->pesan = "Username/Password Tidak Valid";
        	echo json_encode($json);
        }
    }

}
?>