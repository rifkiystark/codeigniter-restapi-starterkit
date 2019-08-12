<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tempordermodel extends CI_Model {

    function select($data){
        return $this->db->get('temporders');
    }

    function insert($data){
        return $this->db->insert('temporders', $data);
    }
}
