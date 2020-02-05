<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tempordermodel extends CI_Model {

    function select(){
        return $this->db->get('temporders');
    }

    function select_where($where){
        return $this->db->get_where('temporders', $where);
    }

    function insert($data){
        return $this->db->insert('temporders', $data);
    }
}
