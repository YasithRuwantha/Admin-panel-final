<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends CI_Model {
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
