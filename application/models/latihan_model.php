<?php

class latihan_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function daftar_latihan()
    {
        $sql = "CALL sp_daftar_latihan();";
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

    function lakukan_latihan($username, $id_latihan)
    {
        $sql = "CALL sp_latihan('$username', '$id_latihan');";
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

    function cek_latihan()
    {
        $username = $this->sesilogin_model->username_login();
        $sql = "CALL sp_cek_latihan('$username');";
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

    function tambah_latihan($nama_latihan, $durasi_latihan_tu, $penambahan_kekompakan, $gambar_latihan)
    {
        $sql = "CALL sp_tambah_latihan('$nama_latihan', '$durasi_latihan_tu', '$penambahan_kekompakan', '$gambar_latihan');";
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