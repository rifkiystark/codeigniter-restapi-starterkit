<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ordermodel extends CI_Model {

    function select($data){
        return $this->db->get('orders');
    }

    function insert($data){
        return $this->db->insert('orders', $data);
    }
}
