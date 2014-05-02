<?php

class timai_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function daftar_tim_ai($username)
    {
        $sql = "CALL sp_getAllTimAi('$username');";
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

    function lineup_timai($id_timai)
    {
        $sql = "CALL sp_daftar_pemain_ai('$id_timai');";
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

    function getFormasiAI($id_timai)
    {
        $sql = "CALL sp_getFormasiAI('$id_timai');";
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