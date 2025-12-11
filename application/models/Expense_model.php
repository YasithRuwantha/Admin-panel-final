<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function get_expense_categories() {
        $query = $this->db->get_where('config', ['config_type' => 'expense_category', 'is_active' => 1]);
        return $query->result_array();
    }

    public function get_payment_methods() {
        $query = $this->db->get_where('config', ['config_type' => 'payment_method', 'is_active' => 1]);
        return $query->result_array();
    }

    public function get_expenses($order_by = 'id DESC') {
        $this->db->order_by($order_by);
        return $this->db->get('expense')->result_array();
    }

    public function add_expense($data) {
        return $this->db->insert('expense', $data);
    }

    public function get_expense_by_id($id) {
        return $this->db->get_where('expense', ['id' => $id])->row_array();
    }

    public function update_expense($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('expense', $data);
    }
}
