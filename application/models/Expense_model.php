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

    public function get_expenses_by_date_range_and_search($limit = 10, $offset = 0, $range = 'all', $search = '', $alpha = 'recent') {
        if ($range === 'today') {
            $this->db->where('DATE(expense_date)', date('Y-m-d'));
        } elseif ($range === 'last7') {
            $this->db->where('expense_date >=', date('Y-m-d', strtotime('-6 days')));
            $this->db->where('expense_date <=', date('Y-m-d'));
        } elseif ($range === 'month') {
            $this->db->where('MONTH(expense_date)', date('m'));
            $this->db->where('YEAR(expense_date)', date('Y'));
        }
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('project_name', $search);
            $this->db->or_like('project_code', $search);
            $this->db->or_like('expense_date', $search);
            $this->db->or_like('category', $search);
            $this->db->or_like('description', $search);
            $this->db->or_like('paid_to', $search);
            $this->db->or_like('paid_by', $search);
            $this->db->or_like('payment_method', $search);
            $this->db->or_like('status', $search);
            $this->db->or_like('remark', $search);
            $this->db->group_end();
        }
        if ($alpha === 'az') {
            $this->db->order_by('project_name', 'ASC');
        } elseif ($alpha === 'za') {
            $this->db->order_by('project_name', 'DESC');
        } else {
            $this->db->order_by('id', 'DESC');
        }
        $query = $this->db->get('expense', $limit, $offset);
        return $query->result_array();
    }

    public function count_expenses_by_date_range_and_search($range = 'all', $search = '') {
        if ($range === 'today') {
            $this->db->where('DATE(expense_date)', date('Y-m-d'));
        } elseif ($range === 'last7') {
            $this->db->where('expense_date >=', date('Y-m-d', strtotime('-6 days')));
            $this->db->where('expense_date <=', date('Y-m-d'));
        } elseif ($range === 'month') {
            $this->db->where('MONTH(expense_date)', date('m'));
            $this->db->where('YEAR(expense_date)', date('Y'));
        }
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('project_name', $search);
            $this->db->or_like('project_code', $search);
            $this->db->or_like('expense_date', $search);
            $this->db->or_like('category', $search);
            $this->db->or_like('description', $search);
            $this->db->or_like('paid_to', $search);
            $this->db->or_like('paid_by', $search);
            $this->db->or_like('payment_method', $search);
            $this->db->or_like('status', $search);
            $this->db->or_like('remark', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results('expense');
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
	public function delete_expense($id) {
        $this->db->where('id', $id);
        return $this->db->delete('expense');
    }
}
