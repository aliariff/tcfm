<?php

class rank extends Logged_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('pagination');
    }

    function index()
    {
    	$template_data = new TemplateData();
		$template_data->template_part = 'user/rank';
		$template_data->header_layout = 'login';
		$template_data->page_layout = 'content';

        $username = $this->sesilogin_model->username_login();

        $n_data = $this->user_model->count_all_user();
        $n_data = (array)$n_data;
        $n_data = $n_data['fn_count_all_user()'];
        $per_page = 20;

        $template_data->data->all_user = $this->user_model->get_all_user_offset(0, $per_page);
        $template_data->data->start_rank = 1;
        $template_data->data->all_user = (array) $template_data->data->all_user;
        foreach ($template_data->data->all_user as &$value)
        {
            if ($username == $value->username)
            {
                $value->status_proteksi = 2;
            }
            else
            {
                $temp = $this->user_model->dapatkan_status_proteksi($username, $value->username);
                $temp = (array)$temp;
                $value->status_proteksi = $temp["fn_statusProteksi('$username', '$value->username')"];
            }
        }

        $pagination = array(
            'query_string_segment' => 'page',
            'base_url' => base_url() . "user/rank/page",
            'total_rows' => $n_data,
            'per_page' => $per_page,
            'use_page_numbers' => TRUE,
            'uri_segment' => 4,
            'num_links' => 3,
            'full_tag_open' => '<ul class="pagination pagination-centered">',
            'full_tag_close' => '</ul>',
            'first_link' => FALSE,
            'last_link' => FALSE,
            'next_link' => '&raquo;',
            'next_tag_open' => '<li>',
            'next_tag_close' => '</li>',
            'prev_link' => '&laquo;',
            'prev_tag_open' => '<li>',
            'prev_tag_close' => '</li>',
            'cur_tag_open' => '<li class="disabled"><a href="#">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li>',
            'num_tag_close' => '</li>',
        );

        $this->pagination->initialize($pagination);

        $template_data->data->pagination_links = $this->pagination->create_links();

		if (!$this->input->is_ajax_request())
		{
			$this->load->view('template/index', $template_data);
		}
		else
		{
			$this->load->view('template/main_' . $template_data->page_layout, $template_data);
		}
    }

    function page($page = null)
    {
        if ($page == null)
        {
            redirect(base_url('user/rank'));
        }
        else
        {
            $template_data = new TemplateData();
            $template_data->template_part = 'user/rank';
            $template_data->header_layout = 'login';
            $template_data->page_layout = 'content';
            
            $username = $this->sesilogin_model->username_login();

            $page--;
            if ($page < 0) $page = 0;

            $n_data = $this->user_model->count_all_user();
            $n_data = (array)$n_data;
            $n_data = $n_data['fn_count_all_user()'];
            $per_page = 20;
            $offset = $page * $per_page;

            $template_data->data->all_user = $this->user_model->get_all_user_offset($offset, $per_page);
            $template_data->data->all_user = (array) $template_data->data->all_user;
            foreach ($template_data->data->all_user as &$value)
            {
                if ($username == $value->username)
                {
                    $value->status_proteksi = 2;
                }
                else
                {
                    $temp = $this->user_model->dapatkan_status_proteksi($username, $value->username);
                    $temp = (array)$temp;
                    $value->status_proteksi = $temp["fn_statusProteksi('$username', '$value->username')"];
                }
            }

            $template_data->data->start_rank = $offset + 1;
            
            $pagination = array(
                'base_url' => base_url() . "user/rank/page",
                'total_rows' => $n_data,
                'per_page' => $per_page,
                'use_page_numbers' => TRUE,
                'uri_segment' => 4,
                'num_links' => 3,
                'full_tag_open' => '<ul class="pagination pagination-centered">',
                'full_tag_close' => '</ul>',
                'first_link' => FALSE,
                'last_link' => FALSE,
                'next_link' => '&raquo;',
                'next_tag_open' => '<li>',
                'next_tag_close' => '</li>',
                'prev_link' => '&laquo;',
                'prev_tag_open' => '<li>',
                'prev_tag_close' => '</li>',
                'cur_tag_open' => '<li class="disabled"><a href="#">',
                'cur_tag_close' => '</a></li>',
                'num_tag_open' => '<li>',
                'num_tag_close' => '</li>',
            );

            $this->pagination->initialize($pagination);

            $template_data->data->pagination_links = $this->pagination->create_links();

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

    function tantang()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $att = $this->sesilogin_model->username_login();
            $def = $this->input->post('username');
            $result = $this->user_model->tanding_pemain($att, $def);
            $json = new stdclass();
            if ($result->kode == 0)
            {
                $json->sukses = true;
                $json->pesan = $result->pesan;
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