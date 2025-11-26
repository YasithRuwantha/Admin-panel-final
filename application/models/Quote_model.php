<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quote_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function add_quote_with_items($quote_data, $items) {
        // Concatenate all descriptions for the quote table
        $all_descriptions = array_map(function($item) {
            return $item['description'];
        }, $items);
        $quote_data['description'] = implode(', ', $all_descriptions);

        $this->db->insert('quote', $quote_data);
        $quote_id = $this->db->insert_id();

        $total = 0;
        foreach ($items as $item) {
            $item_data = [
                'quote_id'   => $quote_id,
                'description'=> $item['description'],
                'amount'     => $item['amount']
            ];
            $this->db->insert('quotation_items', $item_data);
            $total += $item['amount'];
        }
        // Update quote total
        $this->db->where('id', $quote_id)->update('quote', ['amount' => $total]);
        return $quote_id;
    }

    public function get_quote_items($quote_id) {
        return $this->db->get_where('quotation_items', ['quote_id' => $quote_id])->result_array();
    }

    public function get_all_quotes() {
        return $this->db->get('quote')->result_array();
    }
}
