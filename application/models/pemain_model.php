<?php

class pemain_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function detail_pemain($id_user_pemain)
    {
        $sql = "CALL sp_detail_pemain('$id_user_pemain');";
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

    function detail_pemain_id($id_pemain)
    {
        $sql = "CALL sp_detail_pemain_id('$id_pemain');";
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

    function update_lineup($username, $id_user_pemain, $posisi)
    {
        $sql = "CALL sp_update_lineup('$username', '$id_user_pemain', '$posisi');";
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

    function update_lineup_limited($id_pemain, $posisi)
    {
        $sql = "CALL sp_update_lineup_limited('$id_pemain', '$posisi');";
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

    function tambah_pemain($nama_pemain, $tim_asal, $posisi, $nilai_att, $nilai_def, $nilai_speed, $nilai_stamina, $flag_limited,
        $flag_tersedia, $flag_paket, $foto_pemain)
    {
        $sql = "CALL sp_tambah_pemain('$nama_pemain', '$tim_asal', '$posisi', '$nilai_att', '$nilai_def', '$nilai_speed', 
            '$nilai_stamina', '$flag_limited', '$flag_tersedia', '$flag_paket', '$foto_pemain');";
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

    function daftar_pemain()
    {
        $sql = "CALL sp_daftar_pemain();";
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

    function release_pemain($username, $id_user_pemain)
    {
        $sql = "CALL sp_release_pemain('$username', '$id_user_pemain');";
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

    function beli_pemain($username, $paket, $flag)
    {
        $sql = "CALL sp_beliPemain('$username', '$paket', '$flag');";
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

    function get_lineup_limited()
    {
        $sql = "CALL sp_getLimited();";
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

    function get_all_limited()
    {
        $sql = "CALL sp_getAllLimited();";
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
    
}
?>