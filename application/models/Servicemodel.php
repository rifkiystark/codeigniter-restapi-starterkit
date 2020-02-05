<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Servicemodel extends CI_Model {

    function select_where($data){
        return $this->db->get_where('services', $data);
    }

    function insert($data){
        return $this->db->insert('services', $data);
    }
}
