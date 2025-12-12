<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function add_payment($data) {
        return $this->db->insert('payments', $data);
    }

    public function delete_payments_by_invoice($invoice_id) {
        $this->db->where('invoice_id', $invoice_id);
        return $this->db->delete('payments');
    }

    public function add_multiple_payments($invoice_id, $payments) {
        foreach ($payments as $p) {
            if ($p['payment_amount'] === '' || $p['payment_date'] === '') {
                continue;
            }
            $data = [
                'invoice_id'     => $invoice_id,
                'payment_amount' => (float)$p['payment_amount'],
                'payment_date'   => $p['payment_date'],
                'payment_mode'   => isset($p['payment_mode']) ? $p['payment_mode'] : '',
                'reference_no'   => isset($p['reference_no']) ? $p['reference_no'] : '',
                'remarks'        => isset($p['remarks']) ? $p['remarks'] : '',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('payments', $data);
        }
        return true;
    }
}
