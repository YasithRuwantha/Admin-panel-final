
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper(['url', 'auth']);
        $this->load->database();
        $this->load->library('session');
        require_login();
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
        $this->output->set_header('Pragma: no-cache');
        $this->load->model('Invoice_model');
    }

    public function add_invoice() {
        if ($this->input->post()) {
            $data = [
                'name'        => $this->input->post('name'),
                'invoice_no'  => $this->input->post('invoice_no'),
                'address'     => $this->input->post('address'),
                'invoice_date'=> $this->input->post('invoice_date'),
                'project_code'=> $this->input->post('project_code'),
                'description' => json_encode($this->input->post('description')),
                'amount'      => json_encode($this->input->post('amount')),
            ];
            $this->Invoice_model->add_invoice($data);
            redirect('invoice/list');
        } else {
            $this->load->view('add_invoice');
        }
    }

	    public function list() {
        $invoices = $this->Invoice_model->get_all_invoices();
        $this->load->view('list_invoice', ['invoices' => $invoices]);
    }
}
