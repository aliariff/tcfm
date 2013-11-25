<?php

class pemain_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function detail_pemain($username, $id_user_pemain)
    {
        $sql = "CALL sp_detail_pemain('$username', '$id_user_pemain');";
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
}
?>