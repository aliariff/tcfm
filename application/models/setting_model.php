<?php

class setting_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function getAllSetting()
    {
        $sql = "CALL sp_getAllSetting();";
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
    
    function ubah_setting(
        $server_waktu_mulai,
        $server_durasi_tu,
        $ap_durasi_pertambahan_tu,
        $ap_pertambahan,
        $ap_maksimal,
        $ap_nilai_awal,
        $exp_nilai_awal,
        $kekompakan_nilai_awal,
        $kekompakan_maksimal,
        $proteksi_durasi_tu,
        $balen_nilai_awal,
        $uang_nilai_awal
    )
    {
        $sql = "CALL sp_ubahSetting(
            '$server_waktu_mulai',
            '$server_durasi_tu',
            '$ap_durasi_pertambahan_tu',
            '$ap_pertambahan',
            '$ap_maksimal',
            '$ap_nilai_awal',
            '$exp_nilai_awal',
            '$kekompakan_nilai_awal',
            '$kekompakan_maksimal',
            '$proteksi_durasi_tu',
            '$balen_nilai_awal',
            '$uang_nilai_awal'
        );";
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