<?php

class stadion_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function daftar_stadion()
    {
        $sql = "CALL sp_daftar_stadion();";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
        {
            $result = $query->result();
        }
        else
        {
            $result = null;
        }
        $query->next_result();
        $query->free_result();
        return $result;
    }

    function upgrade_stadion($username)
    {
        $sql = "CALL sp_upgrade_stadion('$username');";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
        {
            $result = $query->first_row();
        }
        else
        {
            $result = null;
        }
        $query->next_result();
        $query->free_result();
        return $result;
    }

    function downgrade_stadion($username)
    {
        $sql = "CALL sp_downgrade_stadion('$username');";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
        {
            $result = $query->first_row();
        }
        else
        {
            $result = null;
        }
        $query->next_result();
        $query->free_result();
        return $result;
    }

    function cek_stadion($username)
    {
        $sql = "CALL sp_cek_biaya_perawatan_stadion('$username');";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0)
        {
            $result = $query->first_row();
        }
        else
        {
            $result = null;
        }
        $query->next_result();
        $query->free_result();
        return $result;
    }
    
}
?>