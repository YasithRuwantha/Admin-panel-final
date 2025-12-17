<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model {
        public function get_payment_methods() {
            $query = $this->db->get_where('config', ['config_type' => 'payment_method', 'is_active' => 1]);
            return $query->result_array();
        }
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

    public function get_invoices($limit = 10, $offset = 0) {
		$this->db->order_by('id', 'DESC');
        $query = $this->db->get('invoice', $limit, $offset);
        return $query->result_array();
    }

    public function count_invoices() {
        return $this->db->count_all('invoice');
    }

    public function get_invoice_by_id($id) {
        return $this->db->get_where('invoice', ['id' => $id])->row_array();
    }

    public function get_payments_by_invoice($invoice_id) {
    return $this->db->get_where('payments', ['invoice_id' => $invoice_id])->result_array();
    }

    public function update_invoice($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('invoice', $data);
    }

    // Update invoice header and replace items, recomputing total and description
    public function update_invoice_with_items($id, $data, $items) {
        // Update header first
        $this->db->where('id', $id)->update('invoice', $data);

        // Remove existing items
        $this->db->where('invoice_id', $id)->delete('invoice_items');

        // Insert new items and compute total
        $total = 0;
        $descriptions = [];
        foreach ($items as $item) {
            if (empty($item['description']) || $item['amount'] === '' || $item['amount'] === null) {
                continue;
            }
            $amount = (float)$item['amount'];
            $this->db->insert('invoice_items', [
                'invoice_id'  => $id,
                'description' => $item['description'],
                'amount'      => $amount,
            ]);
            $total += $amount;
            $descriptions[] = $item['description'];
        }

        // Update total and concatenated description on invoice
        $this->db->where('id', $id)->update('invoice', [
            'amount'      => $total,
            'description' => implode(', ', $descriptions),
        ]);

        return true;
    }
	public function delete_invoice($id) {
        // Delete invoice items
        $this->db->where('invoice_id', $id)->delete('invoice_items');
        // Delete the invoice itself
        return $this->db->delete('invoice', ['id' => $id]);
    }


}
