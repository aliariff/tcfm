<?php

class logout extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->sesilogin_model->logout();
        redirect(base_url('home'));
    }
}
?>