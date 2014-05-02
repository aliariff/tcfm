<?php

class login extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        include (APPPATH . 'libraries/template_data.php');
    }

    function index()
    {
        $template_data = new TemplateData();
        $template_data->template_part = 'home/index';
        $template_data->header_layout = 'no_login';
        $template_data->page_layout = 'pure';

        $username = $this->input->post('username1');
        $password = $this->input->post('password1');

       	$json = new stdclass();
        if($this->sesilogin_model->login_admin($username, $password))
        {
            //redirect(base_url('admin'));
            
            $json->sukses = "admin";
            $json->pesan = "Sukses";
            echo json_encode($json);
        }
        else if($this->sesilogin_model->login($username, $password))
        {
            //redirect(base_url('user/dasbor'));
        	$json->sukses = true;
        	$json->pesan = "Sukses";
        	echo json_encode($json);
        }
        else
        {
            /*$data = new stdClass();
            $data->message = 'Username/Password Tidak Valid';
            $data->message_type = 'warning';

            $template_data->data = $data;

            $this->load->view('template/index', $template_data);*/
        	$json->sukses = false;
        	$json->pesan = "Username/Password Tidak Valid";
        	echo json_encode($json);
        }
    }

}
?>