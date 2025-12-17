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

        $projects = $this->Project_model->get_projects(1000, 0); // fetch all for dashboard, adjust limit as needed

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
        ];
        $this->load->view('home', $data);
    }
}
