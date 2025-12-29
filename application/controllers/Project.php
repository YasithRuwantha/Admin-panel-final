        
    
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
            $project_name = trim($this->input->post('name'));
            if ($this->Project_model->project_code_exists($project_code)) {
                $this->session->set_flashdata('error', 'Project code already exists.');
                redirect('project/add');
                return;
            }
            // Check for duplicate project name
            if ($this->Project_model->project_name_exists($project_name)) {
                $this->session->set_flashdata('error', 'Project name already exists.');
                redirect('project/add');
                return;
            }
            $data = [
                'name' => $project_name,
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
        $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 10;
        if (!in_array($per_page, [10, 25, 50, 100])) {
            $per_page = 10;
        }
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $per_page;

        // Date range filter
        $range = $this->input->get('range', true);
        if (!in_array($range, ['today', 'last7', 'month', 'all'])) {
            $range = 'all';
        }

        // Search filter
        $search = $this->input->get('search', true);
        $search = is_string($search) ? trim($search) : '';

        // Alphabetical filter
        $alpha = $this->input->get('alpha', true);
        if ($alpha === 'za') {
            $alpha = 'za';
        } elseif ($alpha === 'az') {
            $alpha = 'az';
        } else {
            $alpha = 'recent';
        }

        // Status filter (default to Ongoing)
        // Only default to 'Ongoing' if status_filter is not present in GET (not even as empty string)
        if (isset($_GET['status_filter'])) {
            $status_filter = $this->input->get('status_filter', true);
        } else {
            $status_filter = 'Ongoing';
        }

        $projects = $this->Project_model->get_projects_by_date_range_and_search($range, $search, $per_page, $offset, $alpha, $status_filter);
        // For pagination, count total projects in range and search
        $total_projects = $this->Project_model->count_projects_by_date_range_and_search($range, $search, $status_filter);
        $total_pages = ceil($total_projects / $per_page);
        $this->load->view('list_projects', [
            'projects' => $projects,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'selected_range' => $range,
            'search' => $search,
            'alpha' => $alpha,
            'per_page' => $per_page,
            'status_filter' => $status_filter
        ]);
    }

    public function view($id) {
        $project = $this->Project_model->get_project_by_id($id);
        if (!$project) {
            show_404();
            return;
        }
        // Fetch documents for this project
        $documents = $this->db->get_where('project_documents', ['project_id' => $id])->result_array();
        $this->load->view('view_project', ['project' => $project, 'documents' => $documents]);
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



	/**
         * Handle document upload for a project
         * URL: project/upload_documents/{project_id}
         */
        public function upload_documents($project_id) {
            // Only allow POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                show_404();
                return;
            }
            // Check project exists
            $project = $this->Project_model->get_project_by_id($project_id);
            if (!$project) {
                $this->session->set_flashdata('error', 'Project not found.');
                redirect('project/list');
                return;
            }
            // Prepare upload directory using project name (slugified)
            $project_name_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $project['name']), '-'));
            $upload_dir = FCPATH . 'uploads/projects/' . $project_name_slug . '/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $files = $_FILES['documents'] ?? null;
            $success = 0;
            $errors = [];
            if ($files && isset($files['name']) && is_array($files['name'])) {
                $count = count($files['name']);
                for ($i = 0; $i < $count; $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $tmp_name = $files['tmp_name'][$i];
                        $orig_name = basename($files['name'][$i]);
                        $safe_name = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $orig_name);
                        $target_path = $upload_dir . $safe_name;
                        if (move_uploaded_file($tmp_name, $target_path)) {
                            // Save to DB
                            $this->db->insert('project_documents', [
                                'project_id' => $project_id,
                                'file_name' => $orig_name,
                                'file_path' => 'uploads/projects/' . $project_name_slug . '/' . $safe_name,
                                'uploaded_at' => date('Y-m-d H:i:s')
                            ]);
                            $success++;
                        } else {
                            $errors[] = $orig_name . ' (move failed)';
                        }
                    } else {
                        $errors[] = $files['name'][$i] . ' (upload error)';
                    }
                }
            } else {
                $this->session->set_flashdata('error', 'No files selected.');
                redirect('project/list');
                return;
            }
            if ($success > 0) {
                $this->session->set_flashdata('success', "$success document(s) uploaded successfully.");
            }
            if (!empty($errors)) {
                $this->session->set_flashdata('error', 'Some files failed: ' . implode(', ', $errors));
            }
            redirect('project/list');
        }


}
