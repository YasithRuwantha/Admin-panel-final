    
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

    public function get_quotes($limit = 10, $offset = 0) {
		$this->db->order_by('id', 'DESC');
        $query = $this->db->get('quote', $limit, $offset);
        return $query->result_array();
    }

    public function count_quotes() {
        return $this->db->count_all('quote');
    }
	    public function get_quote_by_id($id) {
        return $this->db->get_where('quote', ['id' => $id])->row_array();
    }

    public function update_quote_with_items($id, $quote_data, $items) {
        // Concatenate all descriptions for the quote table
        $all_descriptions = array_map(function($item) {
            return $item['description'];
        }, $items);
        $quote_data['description'] = implode(', ', $all_descriptions);

        // Update quote main data
        $this->db->where('id', $id)->update('quote', $quote_data);

        // Remove old items
        $this->db->where('quote_id', $id)->delete('quotation_items');

        $total = 0;
        foreach ($items as $item) {
            $item_data = [
                'quote_id'   => $id,
                'description'=> $item['description'],
                'amount'     => $item['amount']
            ];
            $this->db->insert('quotation_items', $item_data);
            $total += $item['amount'];
        }
        // Update quote total
        $this->db->where('id', $id)->update('quote', ['amount' => $total]);
        return $id;
    }
	public function delete_quote($id) {
        // Delete quotation items
        $this->db->where('quote_id', $id)->delete('quotation_items');
        // Delete the quote itself
        return $this->db->delete('quote', ['id' => $id]);
    }



	/**
     * Get quotes filtered by date range, search, and alpha
     * @param string $range today|last7|month|all
     * @param string $search
     * @param int $limit
     * @param int $offset
     * @param string $alpha recent|az|za
     * @return array
     */
    public function get_quotes_by_date_range_and_search($range = 'all', $search = '', $limit = 1000, $offset = 0, $alpha = 'recent') {
        if ($range === 'today') {
            $this->db->where('DATE(quote_date)', date('Y-m-d'));
        } elseif ($range === 'last7') {
            $this->db->where('quote_date >=', date('Y-m-d', strtotime('-6 days')));
            $this->db->where('quote_date <=', date('Y-m-d'));
        } elseif ($range === 'month') {
            $this->db->where('MONTH(quote_date)', date('m'));
            $this->db->where('YEAR(quote_date)', date('Y'));
        }
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('quotation_no', $search);
            $this->db->or_like('address', $search);
            $this->db->or_like('project_code', $search);
            $this->db->or_like('amount', $search);
            $this->db->group_end();
        }
        if ($alpha === 'az') {
            $this->db->order_by('name', 'ASC');
        } elseif ($alpha === 'za') {
            $this->db->order_by('name', 'DESC');
        } else {
            $this->db->order_by('quote_date', 'DESC');
        }
        $query = $this->db->get('quote', $limit, $offset);
        return $query->result_array();
    }

    /**
     * Count quotes filtered by date range and search
     * @param string $range today|last7|month|all
     * @param string $search
     * @return int
     */
    public function count_quotes_by_date_range_and_search($range = 'all', $search = '') {
        if ($range === 'today') {
            $this->db->where('DATE(quote_date)', date('Y-m-d'));
        } elseif ($range === 'last7') {
            $this->db->where('quote_date >=', date('Y-m-d', strtotime('-6 days')));
            $this->db->where('quote_date <=', date('Y-m-d'));
        } elseif ($range === 'month') {
            $this->db->where('MONTH(quote_date)', date('m'));
            $this->db->where('YEAR(quote_date)', date('Y'));
        }
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('quotation_no', $search);
            $this->db->or_like('address', $search);
            $this->db->or_like('project_code', $search);
            $this->db->or_like('amount', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results('quote');
    }


}
