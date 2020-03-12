<?php
defined('BASEPATH') or exit('No direct script access allowed');

class JadwalModel extends CI_Model
{
    function __construct()
    {
        $this->table = 'Jadwal';
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

    function getJadwal($id_guru, $hari = null)
    {
        $this->db->select('Master_Kelas.kelas, Master_Mapel.mapel, Jadwal.jam, Guru.nama');
        $this->db->from(array('Jadwal', 'Mapel', 'Master_Mapel', 'Kelas', 'Master_Kelas', 'Guru'));
        $this->db->where('Mapel.id_guru = ', $id_guru);
        if ($hari != null) {
            $this->db->where('Jadwal.hari = ', $hari);
        }
        $this->db->where('Jadwal.id_kelas = Kelas.id_kelas');
        $this->db->where('Kelas.id_master_kelas = Master_Kelas.id_master_kelas');
        $this->db->where('Mapel.id_master_mapel = Master_Mapel.id_master_mapel');
        $this->db->where('Mapel.id_guru = Guru.id_guru');
        $this->db->where('Jadwal.id_mapel = Mapel.id_mapel');
        $this->db->order_by('Jadwal.jam', 'ASC');
        return $this->db->get();
    }
}
