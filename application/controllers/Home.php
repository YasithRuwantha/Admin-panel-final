    
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(['url', 'auth']);
        $this->load->database();
        require_login();
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
        $this->output->set_header('Pragma: no-cache');
    }

    public function index() {
        $this->load->model('Project_model');
        $this->load->model('Invoice_model');
        $this->load->model('Expense_model');

        // Get date range from GET or POST, default to 'all'
        $range = $this->input->get('range', true) ?: $this->input->post('range', true);
        if (!in_array($range, ['today', 'last7', 'month', 'all'])) {
            $range = 'all';
        }

        // Get alpha sort
        $alpha = $this->input->get('alpha', true);
        if ($alpha === 'za') {
            $alpha = 'za';
        } elseif ($alpha === 'az') {
            $alpha = 'az';
        } else {
            $alpha = 'recent';
        }

        $projects = $this->Project_model->get_projects_by_date_range_and_search($range, '', 1000, 0, $alpha);

        $report_rows = [];
        foreach ($projects as $p) {
            $project_code = isset($p['project_code']) ? $p['project_code'] : '';
            $project_name = isset($p['name']) ? $p['name'] : (isset($p['project_name']) ? $p['project_name'] : '');
            $project_value = (float)($p['paysheet_value'] ?? 0);

            // Total income: sum of invoice.amount for this project
            $total_income = 0.0;
            if ($project_code !== '') {
                $q_income = $this->db->select('COALESCE(SUM(amount),0) AS total', false)
                    ->from('invoice')
                    ->where('project_code', $project_code)
                    ->get();
                $total_income = (float)($q_income->row_array()['total'] ?? 0);
            } else if ($project_name !== '') {
                $q_income = $this->db->select('COALESCE(SUM(amount),0) AS total', false)
                    ->from('invoice')
                    ->where('project_name', $project_name)
                    ->get();
                $total_income = (float)($q_income->row_array()['total'] ?? 0);
            }

            // Total expenses: sum of expense.amount for this project
            $total_expenses = 0.0;
            if ($project_code !== '') {
                $q_exp = $this->db->select('COALESCE(SUM(amount),0) AS total', false)
                    ->from('expense')
                    ->where('project_code', $project_code)
                    ->get();
                $total_expenses = (float)($q_exp->row_array()['total'] ?? 0);
            } else if ($project_name !== '') {
                $q_exp = $this->db->select('COALESCE(SUM(amount),0) AS total', false)
                    ->from('expense')
                    ->where('project_name', $project_name)
                    ->get();
                $total_expenses = (float)($q_exp->row_array()['total'] ?? 0);
            }

            // Computed fields
            $cash_in_project = $project_value - $total_expenses;
            $cash_in_hand    = $total_income - $total_expenses;

            $report_rows[] = [
                'project_name'   => $project_name,
                'project_code'   => $project_code,
                'project_value'  => $project_value,
                'total_income'   => $total_income,
                'total_expenses' => $total_expenses,
                'cash_in_project'=> $cash_in_project,
                'cash_in_hand'   => $cash_in_hand,
                'status'         => $p['status'] ?? '',
            ];
        }

        $data = [
            'report_rows' => $report_rows,
            'selected_range' => $range,
            'alpha' => $alpha,
        ];
        $this->load->view('home', $data);
    }

	public function export_excel() {
        $this->load->model('Project_model');
        $this->load->model('Invoice_model');
        $this->load->model('Expense_model');

        $range = $this->input->get('range', true) ?: $this->input->post('range', true);
        if (!in_array($range, ['today', 'last7', 'month', 'all'])) {
            $range = 'all';
        }
        $alpha = $this->input->get('alpha', true);
        if ($alpha === 'za') {
            $alpha = 'za';
        } elseif ($alpha === 'az') {
            $alpha = 'az';
        } else {
            $alpha = 'recent';
        }
        $project_code_filter = $this->input->get('project_code', true);
        $projects = $this->Project_model->get_projects_by_date_range_and_search($range, '', 1000, 0, $alpha);
        $report_rows = [];
        foreach ($projects as $p) {
            $project_code = isset($p['project_code']) ? $p['project_code'] : '';
            if ($project_code_filter && $project_code !== $project_code_filter) continue;
            $project_name = isset($p['name']) ? $p['name'] : (isset($p['project_name']) ? $p['project_name'] : '');
            $project_value = (float)($p['paysheet_value'] ?? 0);
            $total_income = 0.0;
            if ($project_code !== '') {
                $q_income = $this->db->select('COALESCE(SUM(amount),0) AS total', false)
                    ->from('invoice')
                    ->where('project_code', $project_code)
                    ->get();
                $total_income = (float)($q_income->row_array()['total'] ?? 0);
            } else if ($project_name !== '') {
                $q_income = $this->db->select('COALESCE(SUM(amount),0) AS total', false)
                    ->from('invoice')
                    ->where('project_name', $project_name)
                    ->get();
                $total_income = (float)($q_income->row_array()['total'] ?? 0);
            }
            $total_expenses = 0.0;
            if ($project_code !== '') {
                $q_exp = $this->db->select('COALESCE(SUM(amount),0) AS total', false)
                    ->from('expense')
                    ->where('project_code', $project_code)
                    ->get();
                $total_expenses = (float)($q_exp->row_array()['total'] ?? 0);
            } else if ($project_name !== '') {
                $q_exp = $this->db->select('COALESCE(SUM(amount),0) AS total', false)
                    ->from('expense')
                    ->where('project_name', $project_name)
                    ->get();
                $total_expenses = (float)($q_exp->row_array()['total'] ?? 0);
            }
            $cash_in_project = $project_value - $total_expenses;
            $cash_in_hand    = $total_income - $total_expenses;
            $report_rows[] = [
                'project_name'   => $project_name,
                'project_code'   => $project_code,
                'project_value'  => $project_value,
                'total_income'   => $total_income,
                'total_expenses' => $total_expenses,
                'cash_in_project'=> $cash_in_project,
                'cash_in_hand'   => $cash_in_hand,
                'status'         => $p['status'] ?? '',
            ];
        }
        // Clean output buffer to prevent corruption
        if (ob_get_length()) ob_end_clean();
        // Adjust autoload path if needed
        $autoloadPath = FCPATH . 'vendor/autoload.php';
        if (!file_exists($autoloadPath)) {
            $autoloadPath = APPPATH . '../vendor/autoload.php';
        }
        require_once $autoloadPath;
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title row
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'Project Financial Report');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header row
        $headers = ['Project', 'Total Budget', 'Total Expenses', 'Total Income', 'Cash in Hand', 'Cash In Project', 'Status'];
        $sheet->fromArray($headers, null, 'A2');
        $sheet->getStyle('A2:G2')->getFont()->setBold(true);
        $sheet->getStyle('A2:G2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2');
        $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Data rows
        $rowNum = 3;
        foreach ($report_rows as $row) {
            $sheet->fromArray([
                $row['project_name'],
                $row['project_value'],
                $row['total_expenses'],
                $row['total_income'],
                $row['cash_in_hand'],
                $row['cash_in_project'],
                $row['status'],
            ], null, 'A' . $rowNum);
            $rowNum++;
        }

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output as XLSX
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=project_report_' . date('Ymd_His') . '.xlsx');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
