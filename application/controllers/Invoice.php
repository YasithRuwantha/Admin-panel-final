

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
            $items = [];
            $descriptions = $this->input->post('description');
            $amounts = $this->input->post('amount');
            for ($i = 0; $i < count($descriptions); $i++) {
                if (!empty($descriptions[$i]) && !empty($amounts[$i])) {
                    $items[] = [
                        'description' => $descriptions[$i],
                        'amount'      => (float)$amounts[$i]
                    ];
                }
            }
            $invoice_data = [
                'name'        => $this->input->post('name'),
                'invoice_no'  => $this->input->post('invoice_no'),
                'address'     => $this->input->post('address'),
                'invoice_date'=> $this->input->post('invoice_date'),
                'project_code'=> $this->input->post('project_code'),
                // 'description' will be set in the model
                // 'amount' will be set after items are added
            ];
            $this->Invoice_model->add_invoice_with_items($invoice_data, $items);
            redirect('invoice/list');
        } else {
            $this->load->view('add_invoice');
        }
    }

	    public function list() {
        $invoices = $this->Invoice_model->get_all_invoices();
        // For each invoice, fetch its items
        foreach ($invoices as &$invoice) {
            $invoice['items'] = $this->Invoice_model->get_invoice_items($invoice['id']);
        }
        $this->load->view('list_invoice', ['invoices' => $invoices]);
    }

	    public function receive_payment() {
        $this->load->model('Payment_model');
        if ($this->input->post()) {
            $data = [
                'invoice_id'     => $this->input->post('invoice_id'),
                'payment_amount' => $this->input->post('payment_amount'),
                'payment_date'   => $this->input->post('payment_date'),
                'payment_mode'   => $this->input->post('payment_mode'),
                'reference_no'   => $this->input->post('reference_no'),
                'remarks'        => $this->input->post('remarks'),
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
            $this->Payment_model->add_payment($data);
            redirect('invoice/list');
        }
    }
}
