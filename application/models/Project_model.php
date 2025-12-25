    

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends CI_Model {
            public function get_project_by_id($id) {
                return $this->db->get_where('project', ['id' => $id])->row_array();
            }

            public function update_project($id, $data) {
                $this->db->where('id', $id);
                return $this->db->update('project', $data);
            }
        public function project_code_exists($project_code) {
            return $this->db->where('project_code', $project_code)->count_all_results('project') > 0;
        }
    public function __construct() {
        parent::__construct();
    }

    public function add_project($data) {
        return $this->db->insert('project', $data);
    }


    public function get_projects($limit = 10, $offset = 0) {
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('project', $limit, $offset);
        return $query->result_array();
    }

	// Check if a project name already exists
    public function project_name_exists($name) {
        return $this->db->where('name', $name)->count_all_results('project') > 0;
    }

    /**
     * Get projects filtered by created_at date range
     * @param string $range today|last7|month|all
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_projects_by_date_range($range = 'all', $limit = 1000, $offset = 0) {
        if ($range === 'today') {
            $this->db->where('DATE(created_at)', date('Y-m-d'));
        } elseif ($range === 'last7') {
            $this->db->where('created_at >=', date('Y-m-d', strtotime('-6 days')));
            $this->db->where('created_at <=', date('Y-m-d'));
        } elseif ($range === 'month') {
            $this->db->where('MONTH(created_at)', date('m'));
            $this->db->where('YEAR(created_at)', date('Y'));
        }
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('project', $limit, $offset);
        return $query->result_array();
    }

    public function count_projects() {
        return $this->db->count_all('project');
    }

	public function delete_project($id) {
        return $this->db->delete('project', ['id' => $id]);
    }

	    /**
     * Count projects filtered by created_at date range
     * @param string $range today|last7|month|all
     * @return int
     */
    public function count_projects_by_date_range($range = 'all') {
        if ($range === 'today') {
            $this->db->where('DATE(created_at)', date('Y-m-d'));
        } elseif ($range === 'last7') {
            $this->db->where('created_at >=', date('Y-m-d', strtotime('-6 days')));
            $this->db->where('created_at <=', date('Y-m-d'));
        } elseif ($range === 'month') {
            $this->db->where('MONTH(created_at)', date('m'));
            $this->db->where('YEAR(created_at)', date('Y'));
        }
        return $this->db->count_all_results('project');
    }


	    /**
     * Get projects filtered by date range and search query
     * @param string $range today|last7|month|all
     * @param string $search search string
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_projects_by_date_range_and_search($range = 'all', $search = '', $limit = 1000, $offset = 0, $alpha = 'az') {
        if ($range === 'today') {
                $this->db->where('DATE(start_date)', date('Y-m-d'));
        } elseif ($range === 'last7') {
                $this->db->where('start_date >=', date('Y-m-d', strtotime('-6 days')));
                $this->db->where('start_date <=', date('Y-m-d'));
        } elseif ($range === 'month') {
                $this->db->where('MONTH(start_date)', date('m'));
                $this->db->where('YEAR(start_date)', date('Y'));
        }
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('project_code', $search);
            $this->db->or_like('client', $search);
            $this->db->or_like('address', $search);
            $this->db->or_like('status', $search);
            $this->db->group_end();
        }
        if ($alpha === 'za') {
            $this->db->order_by('name', 'DESC');
        } elseif ($alpha === 'recent') {
            $this->db->order_by('created_at', 'DESC');
        } else {
            $this->db->order_by('name', 'ASC');
        }
        $query = $this->db->get('project', $limit, $offset);
        return $query->result_array();
    }

    /**
     * Count projects filtered by date range and search query
     * @param string $range today|last7|month|all
     * @param string $search search string
     * @return int
     */
    public function count_projects_by_date_range_and_search($range = 'all', $search = '') {
        if ($range === 'today') {
            $this->db->where('DATE(created_at)', date('Y-m-d'));
        } elseif ($range === 'last7') {
            $this->db->where('created_at >=', date('Y-m-d', strtotime('-6 days')));
            $this->db->where('created_at <=', date('Y-m-d'));
        } elseif ($range === 'month') {
            $this->db->where('MONTH(created_at)', date('m'));
            $this->db->where('YEAR(created_at)', date('Y'));
        }
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('project_code', $search);
            $this->db->or_like('client', $search);
            $this->db->or_like('address', $search);
            $this->db->or_like('status', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results('project');
    }
}
