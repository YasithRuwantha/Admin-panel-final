    
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_model extends CI_Model {
        // Get unique Paid To values
        public function get_unique_paid_to() {
            $this->db->distinct();
            $this->db->select('paid_to');
            $this->db->where('paid_to !=', '');
            $query = $this->db->get('expense');
            $result = $query->result_array();
            $list = array();
            foreach ($result as $row) {
                $list[] = $row['paid_to'];
            }
            return array_unique($list);
        }

        // Get unique Paid By values
        public function get_unique_paid_by() {
            $this->db->distinct();
            $this->db->select('paid_by');
            $this->db->where('paid_by !=', '');
            $query = $this->db->get('expense');
            $result = $query->result_array();
            $list = array();
            foreach ($result as $row) {
                $list[] = $row['paid_by'];
            }
            return array_unique($list);
        }

        // Get expenses with filters
        public function get_expenses_by_filters($limit = 10, $offset = 0, $range = 'all', $search = '', $alpha = 'recent', $paid_to_filter = '', $paid_by_filter = '') {
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
            if (!empty($paid_to_filter)) {
                $this->db->where('paid_to', $paid_to_filter);
            }
            if (!empty($paid_by_filter)) {
                $this->db->where('paid_by', $paid_by_filter);
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

        // Count expenses with filters
        public function count_expenses_by_filters($range = 'all', $search = '', $paid_to_filter = '', $paid_by_filter = '') {
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
            if (!empty($paid_to_filter)) {
                $this->db->where('paid_to', $paid_to_filter);
            }
            if (!empty($paid_by_filter)) {
                $this->db->where('paid_by', $paid_by_filter);
            }
            return $this->db->count_all_results('expense');
        }
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

	// Insert paid_to or paid_by user into config table
    public function insert_paid_user_config($user, $type) {
        $data = [
            'config_type' => $type,
            'config_key' => $user,
            'config_value' => $user,
            'sort_order' => 0,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->insert('config', $data);
    }
}
