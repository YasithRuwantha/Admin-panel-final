<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_by_type($type) {
        return $this->db->where('config_type', $type)
                       ->where('is_active', 1)
                       ->order_by('sort_order', 'ASC')
                       ->get('config')
                       ->result_array();
    }
    
    public function add_config($data) {
        return $this->db->insert('config', $data);
    }
    
    public function update_config($id, $data) {
        return $this->db->where('id', $id)->update('config', $data);
    }
    
    public function delete_config($id) {
        return $this->db->where('id', $id)->delete('config');
    }
    
    public function get_config_by_id($id) {
        return $this->db->where('id', $id)->get('config')->row_array();
    }
}
