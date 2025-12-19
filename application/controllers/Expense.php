    
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library(['session']);
        $this->load->database();
        $this->load->helper(['form', 'url', 'auth']);
        require_login();
        $this->load->model('Expense_model');
        $this->load->model('Config_model');
    }

    public function add() {
        // Get dropdown data
        $categories = $this->Expense_model->get_expense_categories();
        $payment_methods = $this->Expense_model->get_payment_methods();
        // Load config-driven dropdowns for paid_to, paid_by, and status
        $paid_to_options = $this->Config_model->get_by_type('paid_to');
        $paid_by_options = $this->Config_model->get_by_type('paid_by');
        $status_options   = $this->Config_model->get_by_type('status');

        if ($this->input->post()) {
            // Ensure absolute upload path and directory existence across environments
            $upload_dir = FCPATH . 'uploads/expenses/';
            if (!is_dir($upload_dir)) {
                @mkdir($upload_dir, 0755, true);
            }
            $config['upload_path']   = $upload_dir;
            $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx';
            $config['max_size']      = 4096;
            $this->load->library('upload', $config);
            $document_paths = [];
            if (!empty($_FILES['document_path']['name'][0])) {
                $filesCount = count($_FILES['document_path']['name']);
                for ($i = 0; $i < $filesCount; $i++) {
                    // Skip empty file inputs
                    if (empty($_FILES['document_path']['name'][$i])) {
                        continue;
                    }
                    $_FILES['userfile']['name']     = $_FILES['document_path']['name'][$i];
                    $_FILES['userfile']['type']     = $_FILES['document_path']['type'][$i];
                    $_FILES['userfile']['tmp_name'] = $_FILES['document_path']['tmp_name'][$i];
                    $_FILES['userfile']['error']    = $_FILES['document_path']['error'][$i];
                    $_FILES['userfile']['size']     = $_FILES['document_path']['size'][$i];
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload('userfile')) {
                        $document_paths[] = 'uploads/expenses/' . $this->upload->data('file_name');
                    } else {
                        $this->session->set_flashdata('error', $this->upload->display_errors());
                        redirect('expense/add');
                    }
                    unset($_FILES['userfile']);
                }
            }
            $data = [
                'project_code'   => $this->input->post('project_code'),
                'project_name'   => $this->input->post('project_name'),
                'expense_date'   => $this->input->post('expense_date'),
                'category'       => $this->input->post('category'),
                'description'    => $this->input->post('description'),
                'paid_to'        => $this->input->post('paid_to'),
                'paid_by'        => $this->input->post('paid_by'),
                'amount'         => $this->input->post('amount'),
                'payment_method' => $this->input->post('payment_method'),
                'status'         => $this->input->post('status'),
                'remark'         => $this->input->post('remark'),
                'document_path'  => !empty($document_paths) ? json_encode($document_paths) : '',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
            $this->Expense_model->add_expense($data);
            $this->session->set_flashdata('success', 'Expense added successfully');
            redirect('expense/add');
        }
        $this->load->model('Project_model');
        $projects = $this->Project_model->get_projects(1000, 0); // fetch all for dropdown, adjust limit as needed
        $this->load->view('add_expense', [
            'categories' => $categories,
            'payment_methods' => $payment_methods,
            'paid_to_options' => $paid_to_options,
            'paid_by_options' => $paid_by_options,
            'status_options' => $status_options,
            'projects' => $projects
        ]);
    }

    public function list_expenses() {
        $per_page = 10;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $per_page;
        $range = $this->input->get('range') ?? 'all';
        $search = $this->input->get('search') ?? '';
        $alpha = $this->input->get('alpha') ?? 'recent';
        $expenses = $this->Expense_model->get_expenses_by_date_range_and_search($per_page, $offset, $range, $search, $alpha);
        $total_expenses = $this->Expense_model->count_expenses_by_date_range_and_search($range, $search);
        $total_pages = ceil($total_expenses / $per_page);
        $this->load->view('list_expenses', [
            'expenses' => $expenses,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'selected_range' => $range,
            'search' => $search,
            'alpha' => $alpha
        ]);
    }

    public function view($id) {
        $expense = $this->Expense_model->get_expense_by_id($id);
        if (!$expense) {
            $this->session->set_flashdata('error', 'Expense not found');
            redirect('expense/list_expenses');
            return;
        }
        $this->load->view('view_expense', ['expense' => $expense]);
    }

    public function edit($id) {
        // Only admin can edit expenses
        if (function_exists('require_admin')) { require_admin(); }
        $expense = $this->Expense_model->get_expense_by_id($id);
        if (!$expense) {
            $this->session->set_flashdata('error', 'Expense not found');
            redirect('expense/list_expenses');
            return;
        }
        // Get dropdown data for edit form (same as add)
        $categories = $this->Expense_model->get_expense_categories();
        $payment_methods = $this->Expense_model->get_payment_methods();
        // Config-driven dropdowns
        $paid_to_options = $this->Config_model->get_by_type('paid_to');
        $paid_by_options = $this->Config_model->get_by_type('paid_by');
        $status_options   = $this->Config_model->get_by_type('status');

        if ($this->input->method() === 'post') {
            $update = array(
                'project_name' => $this->input->post('project_name'),
                'project_code' => $this->input->post('project_code'),
                'expense_date' => $this->input->post('expense_date'),
                'category' => $this->input->post('category'),
                'description' => $this->input->post('description'),
                'paid_to' => $this->input->post('paid_to'),
                'paid_by' => $this->input->post('paid_by'),
                'amount' => $this->input->post('amount'),
                'payment_method' => $this->input->post('payment_method'),
                'status' => $this->input->post('status'),
                'remark' => $this->input->post('remark'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            if ($this->Expense_model->update_expense($id, $update)) {
                $this->session->set_flashdata('success', 'Expense updated successfully');
                redirect('expense/list_expenses');
                return;
            }
            $this->session->set_flashdata('error', 'Failed to update expense');
        }

        $this->load->view('edit_expense', [
            'expense' => $expense,
            'categories' => $categories,
            'payment_methods' => $payment_methods,
            'paid_to_options' => $paid_to_options,
            'paid_by_options' => $paid_by_options,
            'status_options' => $status_options,
        ]);
    }

	public function delete($id) {
        // Only admin can delete expenses
        if (function_exists('require_admin')) { require_admin(); }
        $expense = $this->Expense_model->get_expense_by_id($id);
        if (!$expense) {
            $this->session->set_flashdata('error', 'Expense not found');
            redirect('expense/list_expenses');
            return;
        }
        $this->Expense_model->delete_expense($id);
        $this->session->set_flashdata('success', 'Expense deleted successfully');
        redirect('expense/list_expenses');
    }

	public function export_expense($id) {
        // Clean output buffer to prevent corruption
        if (ob_get_length()) ob_end_clean();
        $autoloadPath = FCPATH . 'vendor/autoload.php';
        if (!file_exists($autoloadPath)) {
            $autoloadPath = APPPATH . '../vendor/autoload.php';
        }
        require_once $autoloadPath;
        $expense = $this->Expense_model->get_expense_by_id($id);
        if (!$expense) show_404();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Title row
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'Expense Details');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        // Header row
        $headers = ['Project Name', 'Project Code', 'Expense Date', 'Category', 'Description', 'Paid To', 'Paid By', 'Amount', 'Payment Method', 'Status', 'Remark'];
        $sheet->fromArray($headers, null, 'A2');
        $sheet->getStyle('A2:K2')->getFont()->setBold(true);
        $sheet->getStyle('A2:K2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2');
        $sheet->getStyle('A2:K2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        // Data row
        $sheet->fromArray([
            $expense['project_name'],
            $expense['project_code'],
            $expense['expense_date'],
            $expense['category'],
            $expense['description'],
            $expense['paid_to'],
            $expense['paid_by'],
            $expense['amount'],
            $expense['payment_method'],
            $expense['status'],
            $expense['remark'],
        ], null, 'A3');
        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // Output as XLSX
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=expense_' . $id . '_' . date('Ymd_His') . '.xlsx');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

	// AJAX endpoint to add Paid To/Paid By user to config table
    public function add_paid_user_config() {
        $this->load->model('Expense_model');
        $user = $this->input->post('user');
        $type = $this->input->post('type'); // 'paid_to' or 'paid_by'
        if (!$user || !$type || !in_array($type, ['paid_to', 'paid_by'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid input.']);
            return;
        }
        $result = $this->Expense_model->insert_paid_user_config($user, $type);
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add user.']);
        }
    }
}
