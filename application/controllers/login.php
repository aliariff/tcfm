<?php

class login extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->load->model('sesilogin_model');
        $status = $this->sesilogin_model->apakah_login();

        $template_data = new TemplateData();
        $template_data->page_layout = "no_sidebar";
        $template_data->template_part = 'login/index';
        $template_data->scripts = array('login');
        $this->load->library('form_validation');

        if (!$status)
        {
            $this->form_validation->set_rules('nomor_handphone', 'Nomor Handphone', 'required');
            $this->form_validation->set_rules('kata_sandi', 'Kata Sandi', 'required');
            $this->form_validation->set_rules('recaptcha_response_field', 'Konfirmasi Captcha', 'trim|required|callback_captcha_check');
            $this->form_validation->set_message('required', '%s belum diisi');
            $this->form_validation->set_message('captcha_check', '%s Salah');

            if ($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                if (!$this->input->is_ajax_request())
                {
                    $no_handphone = $this->input->post('nomor_handphone');
                    $password = $this->input->post('kata_sandi');

                    if ($this->form_validation->run() == TRUE)
                    {
                        $status = $this->sesilogin_model->login($no_handphone, $password);
                        if ($status)
                        {
                            redirect(base_url('anggota/dasbor'));
                        }
                        else
                        {
                            $data = new stdClass();
                            $data->message = 'Nomor Handphone atau Kata Sandi yang Anda masukkan tidak benar!';
                            $data->message_type = 'warning';

                            $template_data->data = $data;

                            $this->load->view('template/index', $template_data);
                        }
                    }
                    else
                    {
                            $this->form_validation->set_error_delimiters('', '<br />');

                            $data = new stdClass();
                            $data->message = validation_errors();
                            $data->message_type = 'danger';

                            $template_data->data = $data;

                            $this->load->view('template/index', $template_data);
                    }
                }
                else // rekues ajax
                {
                    $this->form_validation->set_error_delimiters('', '');

                    $no_handphone = $this->input->post('nomor_handphone');
                    $password = $this->input->post('kata_sandi');

                    $json = new stdClass();
                    if ($this->form_validation->run() == TRUE)
                    {
                        $status = $this->sesilogin_model->login($no_handphone, $password);

                        if ($status)
                        {
                            $json->sukses = true;
                            $json->redirect = base_url('anggota/dasbor');
                        }
                        else
                        {
                            $json->sukses = false;
                            $json->message = 'Nomor Handphone atau Kata Sandi yang Anda masukkan tidak benar!';
                            $json->message_type = 'danger';
                        }
                    }
                    else
                    {
                        $json->message = validation_errors();
                        $json->message_type = 'danger';
                    }
                    echo json_encode($json);
                    exit;
                }
            }
            else // get biasa
            {
                $this->load->view('template/index', $template_data);
            }
        }
        else // sudah login
        {
            redirect(base_url('anggota/dasbor'));
        }
    }

    function captcha_check($response)
    {
        if (!$this->config->item('captcha_setting'))
        {
            return true;
        }
        require_once(APPPATH . 'libraries/recaptchalib.php');
        $privatekey = "6LfYFukSAAAAADyBc64dLbuqTgksxxiY6DmcCrmQ";
        $resp = recaptcha_check_answer
        (
            $privatekey,
            $_SERVER["REMOTE_ADDR"],
            $_POST["recaptcha_challenge_field"],
            $_POST["recaptcha_response_field"]
        );
        if ($resp->is_valid)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

?>