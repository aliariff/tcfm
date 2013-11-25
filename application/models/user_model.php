<?php

class user_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function register($username, $password, $email)
    {
        $sql = "CALL sp_register('$username', '$password', 'Team', 'Stadion', '$email', null);";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
        {
            $result = $query->first_row();
            $query->next_result();
            $query->free_result();
            return $result;
        }
        return null;
    }

    function login($username, $password)
    {
        $sql = "CALL sp_login('$username', '$password');";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
        {
            $result = $query->first_row();
            $query->next_result();
            $query->free_result();
            return $result;
        }
        return null;
    }

    function user_info($username)
    {
        $sql = "CALL sp_getUserInfo('$username');";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
        {
            $result = $query->first_row();
            $query->next_result();
            $query->free_result();
            return $result;
        }
        return null;
    }

    function daftar_pemain_user($username)
    {
        $sql = "CALL sp_daftar_pemain_user('$username');";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            $query->next_result();
            $query->free_result();
            return $result;
        }
        return null;
    }
}
?>