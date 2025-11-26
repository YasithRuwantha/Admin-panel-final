<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model {
        public function get_payments_by_invoice($invoice_id) {
            return $this->db->get_where('payments', ['invoice_id' => $invoice_id])->result_array();
        }
    public function __construct() {
        parent::__construct();
    }

    public function add_payment($data) {
        return $this->db->insert('payments', $data);
    }
}
