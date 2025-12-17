<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller {
        public function edit($id) {
            // Only admin can edit projects
            if (function_exists('require_admin')) { require_admin(); }
            $project = $this->Project_model->get_project_by_id($id);
            if (!$project) {
                show_404();
                return;
            }
            if ($this->input->post()) {
                $data = [
                    'name' => $this->input->post('name'),
                    'project_code' => $this->input->post('project_code'),
                    'client' => $this->input->post('client'),
                    'address' => $this->input->post('address'),
                    'paysheet_value' => $this->input->post('paysheet_value'),
                    'start_date' => $this->input->post('start_date'),
                    'status' => $this->input->post('status'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $this->Project_model->update_project($id, $data);
                $this->session->set_flashdata('success', 'Project updated successfully');
                redirect('project/list');
                return;
            }
            $this->load->view('edit_project', ['project' => $project]);
        }
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
            $project_code = $this->input->post('project_code');
            if ($this->Project_model->project_code_exists($project_code)) {
                $this->session->set_flashdata('error', 'Project code already exists.');
                redirect('project/add');
                return;
            }
            $data = [
                'name' => $this->input->post('name'),
                'project_code' => $project_code,
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
        $per_page = 10;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $per_page;
        $projects = $this->Project_model->get_projects($per_page, $offset);
        $total_projects = $this->Project_model->count_projects();
        $total_pages = ceil($total_projects / $per_page);
        $this->load->view('list_projects', [
            'projects' => $projects,
            'current_page' => $page,
            'total_pages' => $total_pages
        ]);
    }

    public function view($id) {
        $project = $this->Project_model->get_project_by_id($id);
        if (!$project) {
            show_404();
            return;
        }
        $this->load->view('view_project', ['project' => $project]);
    }

	public function delete($id) {
        // Only admin can delete projects
        if (function_exists('require_admin')) { require_admin(); }
        $project = $this->Project_model->get_project_by_id($id);
        if (!$project) {
            $this->session->set_flashdata('error', 'Project not found');
            redirect('project/list');
            return;
        }
        $this->Project_model->delete_project($id);
        $this->session->set_flashdata('success', 'Project deleted successfully');
        redirect('project/list');
    }


}
