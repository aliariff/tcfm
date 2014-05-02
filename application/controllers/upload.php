<?php

class upload extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        include (APPPATH . 'libraries/UploadHandler.php');
    }

    function index()
    {
        $cek = $this->input->post('param');
        error_reporting(E_ALL | E_STRICT);
        if ($cek == "pemain")
        {
            $upload_handler = new UploadHandler(null, true, null, 180, null);
        }
        else if ($cek == "latihan")
        {
            $upload_handler = new UploadHandler(null, true, null, 180, "latihan/");
        }
        else if ($cek == "user")
        {
            $upload_handler = new UploadHandler(null, true, null, 180, "user/");
        }
    }
}

?>