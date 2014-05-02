<?php

class formasi_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function daftar_formasi()
    {
        $sql = "CALL sp_daftar_formasi();";
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

    function update_formasi($username, $id_formasi)
    {
        $sql = "CALL sp_update_formasi('$username', '$id_formasi');";
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

    function tambah_formasi($nama_formasi)
    {
        $sql = "CALL sp_tambah_formasi('$nama_formasi');";
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