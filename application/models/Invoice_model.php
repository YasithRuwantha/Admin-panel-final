    
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

	/**
     * Get invoices filtered by date range, search, and alpha
     * @param string $range today|last7|month|all
     * @param string $search
     * @param int $limit
     * @param int $offset
     * @param string $alpha recent|az|za
     * @return array
     */
    public function get_invoices_by_date_range_and_search($range = 'all', $search = '', $limit = 1000, $offset = 0, $alpha = 'recent', $status_filter = '') {
        if ($range === 'today') {
            $this->db->where('DATE(invoice_date)', date('Y-m-d'));
        } elseif ($range === 'last7') {
            $this->db->where('invoice_date >=', date('Y-m-d', strtotime('-6 days')));
            $this->db->where('invoice_date <=', date('Y-m-d'));
        } elseif ($range === 'month') {
            $this->db->where('MONTH(invoice_date)', date('m'));
            $this->db->where('YEAR(invoice_date)', date('Y'));
        }
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('invoice_no', $search);
            $this->db->or_like('address', $search);
            $this->db->or_like('project_code', $search);
            $this->db->group_end();
        }
        if ($alpha === 'az') {
            $this->db->order_by('name', 'ASC');
        } elseif ($alpha === 'za') {
            $this->db->order_by('name', 'DESC');
        } else {
            $this->db->order_by('invoice_date', 'DESC');
        }
        $query = $this->db->get('invoice', $limit, $offset);
        $results = $query->result_array();
        // If status_filter is set, filter results in PHP (since status is computed)
        if (!empty($status_filter)) {
            $filtered = [];
            foreach ($results as $invoice) {
                // Calculate status
                $total_paid = 0;
                $ci =& get_instance();
                $ci->load->model('Invoice_model');
                $payments = $ci->Invoice_model->get_payments_by_invoice($invoice['id']);
                foreach ($payments as $pay) {
                    $total_paid += $pay['payment_amount'];
                }
                $invoice_total = $invoice['amount'];
                $status = '';
                if ($total_paid == 0) {
                    $status = 'Pending';
                } elseif ($total_paid < $invoice_total) {
                    $status = 'Partially Paid';
                } elseif ($total_paid == $invoice_total) {
                    $status = 'Paid';
                } elseif ($total_paid > $invoice_total) {
                    $status = 'Over Paid';
                }
                if ($status === $status_filter) {
                    $filtered[] = $invoice;
                }
            }
            return $filtered;
        }
        return $results;
    }

    /**
     * Count invoices filtered by date range and search
     * @param string $range today|last7|month|all
     * @param string $search
     * @return int
     */
    public function count_invoices_by_date_range_and_search($range = 'all', $search = '', $status_filter = '') {
        if ($range === 'today') {
            $this->db->where('DATE(invoice_date)', date('Y-m-d'));
        } elseif ($range === 'last7') {
            $this->db->where('invoice_date >=', date('Y-m-d', strtotime('-6 days')));
            $this->db->where('invoice_date <=', date('Y-m-d'));
        } elseif ($range === 'month') {
            $this->db->where('MONTH(invoice_date)', date('m'));
            $this->db->where('YEAR(invoice_date)', date('Y'));
        }
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('invoice_no', $search);
            $this->db->or_like('address', $search);
            $this->db->or_like('project_code', $search);
            $this->db->group_end();
        }
        $query = $this->db->get('invoice');
        $results = $query->result_array();
        if (!empty($status_filter)) {
            $filtered = [];
            foreach ($results as $invoice) {
                $total_paid = 0;
                $ci =& get_instance();
                $ci->load->model('Invoice_model');
                $payments = $ci->Invoice_model->get_payments_by_invoice($invoice['id']);
                foreach ($payments as $pay) {
                    $total_paid += $pay['payment_amount'];
                }
                $invoice_total = $invoice['amount'];
                $status = '';
                if ($total_paid == 0) {
                    $status = 'Pending';
                } elseif ($total_paid < $invoice_total) {
                    $status = 'Partially Paid';
                } elseif ($total_paid == $invoice_total) {
                    $status = 'Paid';
                } elseif ($total_paid > $invoice_total) {
                    $status = 'Over Paid';
                }
                if ($status === $status_filter) {
                    $filtered[] = $invoice;
                }
            }
            return count($filtered);
        }
        return count($results);
    }


}
