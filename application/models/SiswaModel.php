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

    function getSiswa($idKelas)
    {
        $this->db->select('Siswa.id_siswa, Master_Siswa.nis, Master_Siswa.nama');
        $this->db->where('Siswa.id_kelas', $idKelas);
        $this->db->where('Siswa.id_master_siswa = Master_Siswa.id_master_siswa');
        $this->db->from(array('Siswa', 'Master_Siswa'));

        return $this->db->get();
    }
}
