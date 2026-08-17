        
    
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller {
        public function edit($id) {
            // Only admin can edit projects
            if (function_exists('require_admin')) { require_admin(); }
            $this->load->model('Quote_model');
            $project = $this->Project_model->get_project_by_id($id);
            if (!$project) {
                show_404();
                return;
            }
            if ($this->input->post()) {
                $quotation_id_raw = $this->input->post('quotation_id');
                $data = [
                    'name'          => $this->input->post('name'),
                    'project_code'  => $this->input->post('project_code'),
                    'client'        => $this->input->post('client'),
                    'address'       => $this->input->post('address'),
                    'paysheet_value'=> $this->input->post('paysheet_value'),
                    'start_date'    => $this->input->post('start_date'),
                    'status'        => $this->input->post('status'),
                    'project_type'  => is_array($this->input->post('project_type')) ? implode(',', $this->input->post('project_type')) : $this->input->post('project_type'),
                    'quotation_id'  => (!empty($quotation_id_raw) ? (int)$quotation_id_raw : null),
                    'referred_by'   => $this->input->post('referred_by'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];
                $this->Project_model->update_project($id, $data);
                $this->session->set_flashdata('success', 'Project updated successfully');
                redirect('project/list');
                return;
            }
            $data['project']             = $project;
            $data['project_types']       = $this->Project_model->get_project_types();
            $data['quotes']              = $this->Quote_model->get_quotes(1000, 0);
            $data['referred_by_options'] = $this->Project_model->get_referred_by_options();
            $this->load->view('edit_project', $data);
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
        $this->load->model('Quote_model');
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
            $quotation_id_raw = $this->input->post('quotation_id');
            $data = [
                'name'          => $project_name,
                'project_code'  => $project_code,
                'client'        => $this->input->post('client'),
                'address'       => $this->input->post('address'),
                'paysheet_value'=> $this->input->post('paysheet_value'),
                'start_date'    => $this->input->post('start_date'),
                'status'        => $this->input->post('status'),
                'project_type'  => is_array($this->input->post('project_type')) ? implode(',', $this->input->post('project_type')) : $this->input->post('project_type'),
                'quotation_id'  => (!empty($quotation_id_raw) ? (int)$quotation_id_raw : null),
                'referred_by'   => $this->input->post('referred_by'),
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
            $this->Project_model->add_project($data);
            $this->session->set_flashdata('success', 'Project added successfully');
            redirect('project/add');
        }
        $data['project_types']      = $this->Project_model->get_project_types();
        $data['quotes']             = $this->Quote_model->get_quotes(1000, 0);
        $data['referred_by_options']= $this->Project_model->get_referred_by_options();
        $this->load->view('add_project', $data);
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

        // Project Type filter
        if (isset($_GET['project_type_filter'])) {
            $project_type_filter = $this->input->get('project_type_filter', true);
        } else {
            $project_type_filter = '';
        }

        $projects = $this->Project_model->get_projects_by_date_range_and_search($range, $search, $per_page, $offset, $alpha, $status_filter, $project_type_filter);
        // For pagination, count total projects in range and search
        $total_projects = $this->Project_model->count_projects_by_date_range_and_search($range, $search, $status_filter, $project_type_filter);
        $total_pages = ceil($total_projects / $per_page);
        
        $project_types = $this->Project_model->get_project_types();
        
        $this->load->view('list_projects', [
            'projects' => $projects,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'selected_range' => $range,
            'search' => $search,
            'alpha' => $alpha,
            'per_page' => $per_page,
            'status_filter' => $status_filter,
            'project_type_filter' => $project_type_filter,
            'project_types' => $project_types
        ]);
    }

    public function view($id) {
        $this->load->model('Quote_model');
        $project = $this->Project_model->get_project_by_id($id);
        if (!$project) {
            show_404();
            return;
        }

        // Fetch documents for this project
        $documents = $this->db->get_where('project_documents', ['project_id' => $id])->result_array();

        // Fetch linked quotation if any
        $linked_quotation       = null;
        $linked_quotation_items = [];
        if (!empty($project['quotation_id'])) {
            $linked_quotation = $this->Quote_model->get_quote_by_id($project['quotation_id']);
            if ($linked_quotation) {
                $linked_quotation_items = $this->Quote_model->get_quote_items($project['quotation_id']);
            }
        }

        // --- Financial Summary (same logic as Home.php) ---
        $project_code  = $project['project_code'] ?? '';
        $project_name  = $project['name'] ?? '';
        $project_value = (float)($project['paysheet_value'] ?? 0);

        // Total Invoices: sum of invoice.amount for this project
        $total_invoices = 0.0;
        if ($project_code !== '') {
            $q = $this->db->select('COALESCE(SUM(amount),0) AS total', false)
                ->from('invoice')->where('project_code', $project_code)->get();
            $total_invoices = (float)($q->row_array()['total'] ?? 0);
        } elseif ($project_name !== '') {
            $q = $this->db->select('COALESCE(SUM(amount),0) AS total', false)
                ->from('invoice')->where('project_name', $project_name)->get();
            $total_invoices = (float)($q->row_array()['total'] ?? 0);
        }

        // Total Income: sum of payments received for this project's invoices
        $total_income = 0.0;
        if ($project_code !== '') {
            $q = $this->db->select('COALESCE(SUM(payments.payment_amount),0) AS total', false)
                ->from('payments')
                ->join('invoice', 'invoice.id = payments.invoice_id', 'inner')
                ->where('invoice.project_code', $project_code)->get();
            $total_income = (float)($q->row_array()['total'] ?? 0);
        } elseif ($project_name !== '') {
            $q = $this->db->select('COALESCE(SUM(payments.payment_amount),0) AS total', false)
                ->from('payments')
                ->join('invoice', 'invoice.id = payments.invoice_id', 'inner')
                ->where('invoice.project_name', $project_name)->get();
            $total_income = (float)($q->row_array()['total'] ?? 0);
        }

        // Total Expenses: sum of expense.amount for this project
        $total_expenses = 0.0;
        if ($project_code !== '') {
            $q = $this->db->select('COALESCE(SUM(amount),0) AS total', false)
                ->from('expense')->where('project_code', $project_code)->get();
            $total_expenses = (float)($q->row_array()['total'] ?? 0);
        } elseif ($project_name !== '') {
            $q = $this->db->select('COALESCE(SUM(amount),0) AS total', false)
                ->from('expense')->where('project_name', $project_name)->get();
            $total_expenses = (float)($q->row_array()['total'] ?? 0);
        }

        // Computed fields
        $cash_in_hand    = $total_income - $total_expenses;
        $cash_in_project = $project_value - $total_expenses;
        $profit_loss     = $total_invoices - $total_expenses;

        // Expenses broken down by category for this project
        $expense_by_category = [];
        if ($project_code !== '') {
            $q = $this->db->select('category, COALESCE(SUM(amount),0) AS total', false)
                ->from('expense')
                ->where('project_code', $project_code)
                ->group_by('category')
                ->order_by('total', 'DESC')
                ->get();
            $expense_by_category = $q->result_array();
        } elseif ($project_name !== '') {
            $q = $this->db->select('category, COALESCE(SUM(amount),0) AS total', false)
                ->from('expense')
                ->where('project_name', $project_name)
                ->group_by('category')
                ->order_by('total', 'DESC')
                ->get();
            $expense_by_category = $q->result_array();
        }

        $this->load->view('view_project', [
            'project'                => $project,
            'documents'              => $documents,
            'linked_quotation'       => $linked_quotation,
            'linked_quotation_items' => $linked_quotation_items,
            'total_invoices'         => $total_invoices,
            'total_income'           => $total_income,
            'total_expenses'         => $total_expenses,
            'cash_in_hand'           => $cash_in_hand,
            'cash_in_project'        => $cash_in_project,
            'profit_loss'            => $profit_loss,
            'expense_by_category'    => $expense_by_category,
        ]);
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


    // AJAX endpoint to add a new 'Referred By' option to the config table
    public function add_referred_by_config() {
        $name = trim($this->input->post('name'));
        if (!$name) {
            echo json_encode(['success' => false, 'message' => 'Name is required.']);
            return;
        }
        $result = $this->Project_model->insert_referred_by_config($name);
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add referrer.']);
        }
    }

}
