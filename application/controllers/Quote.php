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
        $per_page = 10;
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
        if ($alpha === 'az') {
            $alpha = 'az';
        } elseif ($alpha === 'za') {
            $alpha = 'za';
        } else {
            $alpha = 'recent';
        }

        $quotations = $this->Quote_model->get_quotes_by_date_range_and_search($range, $search, $per_page, $offset, $alpha);
        $total_quotes = $this->Quote_model->count_quotes_by_date_range_and_search($range, $search);
        $total_pages = ceil($total_quotes / $per_page);
        // For each quote, fetch its items
        foreach ($quotations as &$quote) {
            $quote['items'] = $this->Quote_model->get_quote_items($quote['id']);
        }
        $this->load->view('list_quotation', [
            'quotations' => $quotations,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'selected_range' => $range,
            'search' => $search,
            'alpha' => $alpha
        ]);
    }

    public function view($id) {
        $quote = $this->Quote_model->get_quote_by_id($id);
        if (!$quote) {
            show_404();
        }
        $quote['items'] = $this->Quote_model->get_quote_items($id);
        $this->load->view('view_quotation', ['quote' => $quote]);
    }

	    public function edit($id) {
            // Only admin can edit quotations
            if (function_exists('require_admin')) { require_admin(); }
        $quote = $this->Quote_model->get_quote_by_id($id);
        if (!$quote) {
            show_404();
        }
        $quote['items'] = $this->Quote_model->get_quote_items($id);
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
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
            $this->Quote_model->update_quote_with_items($id, $quote_data, $items);
            $this->session->set_flashdata('success', 'Quotation updated successfully');
            redirect('quote/list');
        }
        $this->load->view('edit_quotation', ['quote' => $quote]);
    }

    public function pdf($id) {
        // Get quote data
        $quote = $this->Quote_model->get_quote_by_id($id);
        if (!$quote) {
            show_404();
        }
        $quote['items'] = $this->Quote_model->get_quote_items($id);
        
        // Load HTML content
        $data['quote'] = $quote;
        $html = $this->load->view('quotation_pdf', $data, true);
        
        // Use DomPDF for PDF generation
        require_once(APPPATH.'libraries/dompdf/autoload.inc.php');
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output PDF for inline display
        $filename = $quote['quotation_no'] . '.pdf';
        $dompdf->stream($filename, array("Attachment" => false));
    }
	public function delete($id) {
        // Only admin can delete quotations
        if (function_exists('require_admin')) { require_admin(); }
        $quote = $this->Quote_model->get_quote_by_id($id);
        if (!$quote) {
            $this->session->set_flashdata('error', 'Quotation not found');
            redirect('quote/list');
            return;
        }
        $this->Quote_model->delete_quote($id);
        $this->session->set_flashdata('success', 'Quotation deleted successfully');
        redirect('quote/list');
    }


}
