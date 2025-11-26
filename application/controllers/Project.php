
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library(['session']);
        $this->load->database();
        $this->load->helper(['form', 'url', 'auth']);
        require_login();
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
        $this->output->set_header('Pragma: no-cache');
        $this->load->model('Project_model');
    }

    public function add() {
        if ($this->input->post()) {
            $data = [
                'name' => $this->input->post('name'),
                'project_code' => $this->input->post('project_code'),
                'client' => $this->input->post('client'),
                'address' => $this->input->post('address'),
                'paysheet_value' => $this->input->post('paysheet_value'),
                'start_date' => $this->input->post('start_date'),
                'status' => $this->input->post('status'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->Project_model->add_project($data);
            $this->session->set_flashdata('success', 'Project added successfully');
            redirect('project/add');
        }
        $this->load->view('add_project');
    }
	    public function list() {
        $projects = $this->Project_model->get_all_projects();
        $this->load->view('list_projects', ['projects' => $projects]);
    }
}
