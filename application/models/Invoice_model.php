<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    // Add invoice and its items, storing total in invoice.amount
    public function add_invoice_with_items($invoice_data, $items) {
        // Concatenate all descriptions for the invoice table
        $all_descriptions = array_map(function($item) {
            return $item['description'];
        }, $items);
        $invoice_data['description'] = implode(', ', $all_descriptions);

        $this->db->insert('invoice', $invoice_data);
        $invoice_id = $this->db->insert_id();

        $total = 0;
        foreach ($items as $item) {
            $item_data = [
                'invoice_id'  => $invoice_id,
                'description' => $item['description'],
                'amount'      => $item['amount']
            ];
            $this->db->insert('invoice_items', $item_data);
            $total += $item['amount'];
        }
        // Update invoice total
        $this->db->where('id', $invoice_id)->update('invoice', ['amount' => $total]);
        return $invoice_id;
    }
    public function get_invoice_items($invoice_id) {
        return $this->db->get_where('invoice_items', ['invoice_id' => $invoice_id])->result_array();
    }

    public function get_all_invoices() {
        return $this->db->get('invoice')->result_array();
    }
}
