<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function add_payment($data) {
        return $this->db->insert('payments', $data);
    }
}
