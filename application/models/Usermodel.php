<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usermodel extends CI_Model {

    function register($data){
        $data['role'] = "customer";
        
        return $this->db->insert('users', $data);
    }

    function select_user($data){
        $this->db->where($data);
        return $this->db->get('users');
    }

    function registertech($data){
        $data['role'] = "technician";

        return $this->db->insert('users', $data);
    }
}
