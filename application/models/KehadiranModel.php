<?php
class KehadiranModel extends CI_Model
{
    function __construct()
    {
        $this->table = 'Kehadiran';
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

    function getKelas($id_guru)
    {
        $this->db->select('distinct(Master_Kelas.kelas), Kelas.id_kelas');
        $this->db->from(array('Jadwal', 'Mapel', 'Master_Mapel', 'Kelas', 'Master_Kelas', 'Guru'));
        $this->db->where('Mapel.id_guru = ', $id_guru);
        $this->db->where('Jadwal.id_kelas = Kelas.id_kelas');
        $this->db->where('Kelas.id_master_kelas = Master_Kelas.id_master_kelas');
        $this->db->where('Mapel.id_master_mapel = Master_Mapel.id_master_mapel');
        $this->db->where('Mapel.id_guru = Guru.id_guru');
        $this->db->where('Jadwal.id_mapel = Mapel.id_mapel');
        return $this->db->get();
    }
}
