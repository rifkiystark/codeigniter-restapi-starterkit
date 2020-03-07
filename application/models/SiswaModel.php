<?php
class SiswaModel extends CI_Model
{
    function __construct()
    {
        $this->table = 'Siswa';
    }


    function get()
    {
        return $this->db->get($this->table);
    }

    function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    function get_where($where)
    {
        $this->db->where($where);
        return $this->db->get($this->table);
    }
}
