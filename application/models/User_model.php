<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_user($username) {
        return $this->db->get_where('user', ['username' => $username])->row_array();
    }

    public function get_user_by_token($token) {
        return $this->db->get_where('user', ['remember_token' => $token])->row_array();
    }

    public function update_remember_token($id, $token) {
        $this->db->where('id', $id);
        return $this->db->update('user', ['remember_token' => $token]);
    }

    public function clear_remember_token($username) {
        $this->db->where('username', $username);
        return $this->db->update('user', ['remember_token' => NULL]);
    }
}
