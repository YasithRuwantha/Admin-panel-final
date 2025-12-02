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

    public function get_all_projects() {
        return $this->db->get('project')->result_array();
    }
}
