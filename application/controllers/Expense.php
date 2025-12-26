            
        
    
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
            $project_name = $this->input->post('project_name');
            $safe_project_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $project_name);
            $upload_dir = FCPATH . 'uploads/expenses/' . $safe_project_name . '/';
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
                        $document_paths[] = 'uploads/expenses/' . $safe_project_name . '/' . $this->upload->data('file_name');
                    } else {
                        $this->session->set_flashdata('error', $this->upload->display_errors());
                        redirect('expense/add');
                    }
                    unset($_FILES['userfile']);
                }
            }
            $data = [
                'project_code'   => $this->input->post('project_code'),
                'project_name'   => $project_name,
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
        $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 10;
        if (!in_array($per_page, [10, 25, 50, 100])) {
            $per_page = 10;
        }
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $per_page;
        $range = $this->input->get('range') ?? 'all';
        $search = $this->input->get('search') ?? '';
        $alpha = $this->input->get('alpha') ?? 'recent';
        $paid_to_filter = $this->input->get('paid_to_filter') ?? '';
        $paid_by_filter = $this->input->get('paid_by_filter') ?? '';

        $expenses = $this->Expense_model->get_expenses_by_filters($per_page, $offset, $range, $search, $alpha, $paid_to_filter, $paid_by_filter);
        $total_expenses = $this->Expense_model->count_expenses_by_filters($range, $search, $paid_to_filter, $paid_by_filter);
        $total_pages = ceil($total_expenses / $per_page);

        // Get unique Paid To and Paid By lists for dropdowns
        $paid_to_list = $this->Expense_model->get_unique_paid_to();
        $paid_by_list = $this->Expense_model->get_unique_paid_by();

        $this->load->view('list_expenses', [
            'expenses' => $expenses,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'selected_range' => $range,
            'search' => $search,
            'alpha' => $alpha,
            'paid_to_filter' => $paid_to_filter,
            'paid_by_filter' => $paid_by_filter,
            'paid_to_list' => $paid_to_list,
            'paid_by_list' => $paid_by_list,
            'per_page' => $per_page
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

	// public function export_expense($id) {
    //     // Clean output buffer to prevent corruption
    //     if (ob_get_length()) ob_end_clean();
    //     $autoloadPath = FCPATH . 'vendor/autoload.php';
    //     if (!file_exists($autoloadPath)) {
    //         $autoloadPath = APPPATH . '../vendor/autoload.php';
    //     }
    //     require_once $autoloadPath;
    //     $expense = $this->Expense_model->get_expense_by_id($id);
    //     if (!$expense) show_404();
    //     $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     // Title row
    //     $sheet->mergeCells('A1:K1');
    //     $sheet->setCellValue('A1', 'Expense Details');
    //     $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    //     $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    //     // Header row
    //     $headers = ['Project Name', 'Project Code', 'Expense Date', 'Category', 'Description', 'Paid To', 'Paid By', 'Amount', 'Payment Method', 'Status', 'Remark'];
    //     $sheet->fromArray($headers, null, 'A2');
    //     $sheet->getStyle('A2:K2')->getFont()->setBold(true);
    //     $sheet->getStyle('A2:K2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2');
    //     $sheet->getStyle('A2:K2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    //     // Data row
    //     $sheet->fromArray([
    //         $expense['project_name'],
    //         $expense['project_code'],
    //         $expense['expense_date'],
    //         $expense['category'],
    //         $expense['description'],
    //         $expense['paid_to'],
    //         $expense['paid_by'],
    //         $expense['amount'],
    //         $expense['payment_method'],
    //         $expense['status'],
    //         $expense['remark'],
    //     ], null, 'A3');
    //     // Auto-size columns
    //     foreach (range('A', 'K') as $col) {
    //         $sheet->getColumnDimension($col)->setAutoSize(true);
    //     }
    //     // Output as XLSX
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment; filename=expense_' . $id . '_' . date('Ymd_His') . '.xlsx');
    //     $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    //     $writer->save('php://output');
    //     exit;
    // }

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




	// Export all shown/filtered expenses to Excel (like invoices/quotations)
    // Update export_all to alternate row group background colors (blue/white)
            public function export_all() {
                // Clean output buffer to prevent corruption
                if (ob_get_length()) ob_end_clean();
                $autoloadPath = FCPATH . 'vendor/autoload.php';
                if (!file_exists($autoloadPath)) {
                    $autoloadPath = APPPATH . '../vendor/autoload.php';
                }
                require_once $autoloadPath;

                // Get filters from GET
                $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 10;
                if (!in_array($per_page, [10, 25, 50, 100])) {
                    $per_page = 10;
                }
                $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
                if ($page < 1) $page = 1;
                $offset = ($page - 1) * $per_page;
                $range = $this->input->get('range') ?? 'all';
                $search = $this->input->get('search') ?? '';
                $alpha = $this->input->get('alpha') ?? 'recent';
                $paid_to_filter = $this->input->get('paid_to_filter') ?? '';
                $paid_by_filter = $this->input->get('paid_by_filter') ?? '';

                $expenses = $this->Expense_model->get_expenses_by_filters($per_page, $offset, $range, $search, $alpha, $paid_to_filter, $paid_by_filter);

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                // Title row
                $sheet->mergeCells('A1:K1');
                $sheet->setCellValue('A1', 'Expense List');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                // Header row
                $headers = ['Project Name', 'Project Code', 'Expense Date', 'Category', 'Description', 'Paid To', 'Paid By', 'Amount', 'Payment Method', 'Status', 'Remark'];
                $sheet->fromArray($headers, null, 'A2');
                $sheet->getStyle('A2:K2')->getFont()->setBold(true);
                $sheet->getStyle('A2:K2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2');
                $sheet->getStyle('A2:K2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $row = 3;
                $groupColor1 = 'FFFFFFFF'; // white
                $groupColor2 = 'FFDAECFF'; // light blue
                $useColor1 = true;
                foreach ($expenses as $expense) {
                    $groupStart = $row;
                    $sheet->setCellValue('A'.$row, $expense['project_name']);
                    $sheet->setCellValue('B'.$row, $expense['project_code']);
                    $sheet->setCellValue('C'.$row, $expense['expense_date']);
                    $sheet->setCellValue('D'.$row, $expense['category']);
                    $sheet->setCellValue('E'.$row, $expense['description']);
                    $sheet->setCellValue('F'.$row, $expense['paid_to']);
                    $sheet->setCellValue('G'.$row, $expense['paid_by']);
                    $sheet->setCellValue('H'.$row, $expense['amount']);
                    $sheet->setCellValue('I'.$row, $expense['payment_method']);
                    $sheet->setCellValue('J'.$row, $expense['status']);
                    $sheet->setCellValue('K'.$row, $expense['remark']);
                    // Alignment: all left except Amount (H)
                    $sheet->getStyle('A'.$row.':G'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('I'.$row.':K'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('H'.$row.':H'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    // Amount: number format with thousand separator
                    $sheet->getStyle('H'.$row.':H'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
                    $row++;
                    // Color the group row and keep table grid lines
                    $groupEnd = $row - 1;
                    $fillColor = $useColor1 ? $groupColor1 : $groupColor2;
                    for ($r = $groupStart; $r <= $groupEnd; $r++) {
                        $style = $sheet->getStyle('A' . $r . ':K' . $r);
                        $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($fillColor);
                        $style->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF000000'));
                    }
                    $useColor1 = !$useColor1;
                }
                // Auto-size columns
                foreach (range('A', 'K') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                // Output as XLSX
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename=expenses_' . date('Ymd_His') . '.xlsx');
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit;
            }
}
