<?php

class sesilogin_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function apakah_batas_percobaan_login()
    {
        if ($this->session->userdata('percobaan_login'))
        {
            if ($this->session->userdata('percobaan_login') >= 10)
            {
                return true;
            }
        }
        return false;
    }

    function tambah_percobaan_login()
    {
        if ($this->session->userdata('percobaan_login'))
        {
            $percobaan_login = $this->session->userdata('percobaan_login') + 1;
            $this->session->set_userdata('percobaan_login', $percobaan_login);
        }
        else
        {
            $this->session->set_userdata('percobaan_login', 1);
        }
    }

    function lihat_jumlah_percobaan_login()
    {
        if ($this->session->userdata('percobaan_login'))
        {
            return $this->session->userdata('percobaan_login');
        }
        else
        {
            return 0;
        }
    }

    function login($username, $password)
    {
        $username = $this->db->escape_str($username);
        $password = $this->db->escape_str($password);
        //$password = sha1($password . strrev($password));
        $call = "CALL sp_login('$username', '$password')";
        $query = $this->db->query($call);
        
        if ($query->first_row()->kode == 0)
        {
            $this->session->sess_create();
            $this->session->set_userdata('username', $username);
            $this->session->set_userdata('secret_key', 'randomkey');
            $query->next_result();
            $query->free_result();
            return true;
        }
        else
        {
            $this->tambah_percobaan_login();
            $query->next_result();
            $query->free_result();
            return false;
        }
    }

    function login_admin($username, $password)
    {
        $username = $this->db->escape_str($username);
        $password = $this->db->escape_str($password);
        //$password = sha1($password . strrev($password));
        $call = "CALL sp_login_admin('$username', '$password')";
        $query = $this->db->query($call);
        
        if ($query->first_row()->kode == 0)
        {
            $this->session->sess_create();
            $this->session->set_userdata('username', $username);
            $this->session->set_userdata('secret_key', 'adminkey');
            $query->next_result();
            $query->free_result();
            return true;
        }
        else
        {
            $this->tambah_percobaan_login();
            $query->next_result();
            $query->free_result();
            return false;
        }
    }

    function logout()
    {
        return $this->session->sess_destroy();
    }

    function apakah_login()
    {
        if ($this->session->userdata('username') && $this->session->userdata('secret_key'))
        {
            if ($this->session->userdata('secret_key') == 'randomkey')
            {
                return true;
            }
        }
        return false;
    }

    function apakah_login_admin()
    {
        if ($this->session->userdata('username') && $this->session->userdata('secret_key'))
        {
            if ($this->session->userdata('secret_key') == 'adminkey')
            {
                return true;
            }
        }
        return false;
    }

    function username_login()
    {
        return $this->session->userdata('username');
    }

}
?>