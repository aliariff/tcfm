<?php

class paket_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function daftar_paket()
    {
        $sql = "CALL sp_getPaket();";
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