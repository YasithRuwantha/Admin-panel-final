
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quote extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library(['session']);
        $this->load->database();
        $this->load->helper(['form', 'url', 'auth']);
        require_login();
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
        $this->output->set_header('Pragma: no-cache');
        $this->load->model('Quote_model');
    }

    public function add() {
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
            $quote_data = [
                'name'          => $this->input->post('name'),
                'quotation_no'  => $this->input->post('quotation_no'),
                'address'       => $this->input->post('address'),
                'quote_date'    => $this->input->post('quote_date'),
                'project_code'  => $this->input->post('project_code'),
                // 'description' will be set in the model
                // 'amount' will be set after items are added
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
            $this->Quote_model->add_quote_with_items($quote_data, $items);
            $this->session->set_flashdata('success', 'Quotation added successfully');
            redirect('quote/add');
        }
        $this->load->view('add_quotation');
    }

	    public function list() {
        $quotations = $this->Quote_model->get_all_quotes();
        // For each quote, fetch its items
        foreach ($quotations as &$quote) {
            $quote['items'] = $this->Quote_model->get_quote_items($quote['id']);
        }
        $this->load->view('list_quotation', ['quotations' => $quotations]);
    }
}
