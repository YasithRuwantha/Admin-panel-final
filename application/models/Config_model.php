<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function get_payment_methods() {
        $query = $this->db->get_where('config', ['config_type' => 'payment_method', 'is_active' => 1]);
        return $query->result_array();
    }
}
