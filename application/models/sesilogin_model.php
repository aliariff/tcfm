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

	function login($no_handphone, $password)
	{
            $no_handphone = $this->db->escape_str($no_handphone);
            $password = $this->db->escape_str($password);
            $this->db->where('nomor_handphone', $no_handphone);
            $this->db->where('sandi_anggota', sha1($password . strrev($password)));
            $query = $this->db->get('sandi_anggota');
            if ($query->num_rows() > 0)
            {
                $this->session->sess_create();
                $this->session->set_userdata('no_handphone', $no_handphone);
                $this->session->set_userdata('secret_key', 'randomkey');
                return true;
            }
            else
            {
                $this->tambah_percobaan_login();
                return false;
            }
	}

	function logout()
	{
            return $this->session->sess_destroy();
	}

	function apakah_login()
	{
            if ($this->session->userdata('no_handphone') && $this->session->userdata('secret_key'))
            {
                if ($this->session->userdata('secret_key') == 'randomkey')
                {
                    return true;
                }
            }
            return false;
	}

	function nama_pengguna_login()
	{
            $this->db->select('nama_lengkap');
            $this->db->where('nomor_handphone', $this->session->userdata('no_handphone'));
            $query = $this->db->get('anggota');
            if ($query->num_rows() > 0)
            {
                return $query->first_row()->nama_lengkap;
            }
            return null;
	}

        function nomor_handphone_login()
        {
            return $this->session->userdata('no_handphone');
        }
}

?>