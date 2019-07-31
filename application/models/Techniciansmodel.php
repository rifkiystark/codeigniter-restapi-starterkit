<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Techniciansmodel extends CI_Model {

    function select_user($data){
        $this->db->where($data);
        return $this->db->get('technicians');
    }

    function select(){
        return $this->db->get('technicians');
    }
}
