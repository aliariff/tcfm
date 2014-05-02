<?php

class penawaran_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function tawar_pemain($harga, $pemilik, $penawar, $id_pemain, $pesan = null)
    {
        $sql = "CALL sp_tawar_pemain('$harga', '$pemilik', '$penawar', '$id_pemain', '$pesan');";
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

    function daftar_penawaran_masuk($username)
    {
        $sql = "CALL sp_daftar_penawaran_masuk('$username');";
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

    function daftar_penawaran_keluar($username)
    {
        $sql = "CALL sp_daftar_penawaran_keluar('$username');";
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

    function terima_tawaran($username, $id_penawaran)
    {
        $sql = "CALL sp_terima_tawaran('$username', '$id_penawaran');";
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

    function tolak_tawaran($username, $id_penawaran)
    {
        $sql = "CALL sp_tolak_tawaran('$username', '$id_penawaran');";
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

    function baca_tawaran($username)
    {
        $sql = "CALL sp_baca_tawaran('$username');";
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

    function batalkan_tawaran($username, $id_penawaran)
    {
        $sql = "CALL sp_batalkan_tawaran('$username', '$id_penawaran');";
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