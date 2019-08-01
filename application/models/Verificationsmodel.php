<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verificationsmodel extends CI_Model {

    function insert($data){
        
        return $this->db->insert('verifications', $data);
    }

    function select_where($data){
        $this->db->where($data);
        return $this->db->get('verifications');
    }

    function delete_where($where){
        $this->db->where($where);
        return $this->db->delete('verifications');
    }
}
