<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function add_invoice($data) {
        return $this->db->insert('invoice', $data);
    }

    public function get_all_invoices() {
        return $this->db->get('invoice')->result_array();
    }
}
