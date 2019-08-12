<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ordermodel extends CI_Model {

    function select_where($data){
        return $this->db->get_where('orders', $data);
    }

    function insert($data){
        return $this->db->insert('orders', $data);
    }
}
