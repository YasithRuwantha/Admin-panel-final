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
        $payment_methods = $this->Invoice_model->get_payment_methods();
        $this->load->model('Project_model');
        $projects = $this->Project_model->get_projects(1000, 0); // fetch all for dropdown, adjust limit as needed
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
                'project_name' => $this->input->post('project_name'),
                // 'description' will be set in the model
                // 'amount' will be set after items are added
            ];
            $this->Invoice_model->add_invoice_with_items($invoice_data, $items);
            redirect('invoice/list');
        } else {
            $this->load->view('add_invoice', [
                'payment_methods' => $payment_methods,
                'projects' => $projects
            ]);
        }
    }

	    public function list() {
        $per_page = 10;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $per_page;
        $invoices = $this->Invoice_model->get_invoices($per_page, $offset);
        $total_invoices = $this->Invoice_model->count_invoices();
        $total_pages = ceil($total_invoices / $per_page);
        $this->load->model('Payment_model');
        $payment_methods = $this->Invoice_model->get_payment_methods();
        // For each invoice, fetch its items and payment info
        foreach ($invoices as &$invoice) {
            $invoice['items'] = $this->Invoice_model->get_invoice_items($invoice['id']);
            // Fetch all payments for this invoice using the model
            $invoice['payments'] = $this->Invoice_model->get_payments_by_invoice($invoice['id']);
        }
        $this->load->view('list_invoice', [
            'invoices' => $invoices,
            'payment_methods' => $payment_methods,
            'current_page' => $page,
            'total_pages' => $total_pages
        ]);
    }

	    public function receive_payment() {
            // Only admin can record payments
            if (function_exists('require_admin')) { require_admin(); }
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

    public function pdf($id) {
        // Get invoice data
        $invoice = $this->Invoice_model->get_invoice_by_id($id);
        if (!$invoice) {
            show_404();
        }
        $invoice['items'] = $this->Invoice_model->get_invoice_items($id);
        $invoice['payments'] = $this->Invoice_model->get_payments_by_invoice($id);
        
        // Load HTML content
        $data['invoice'] = $invoice;
        $html = $this->load->view('invoice_pdf', $data, true);
        
        // Use DomPDF for PDF generation
        require_once(APPPATH.'libraries/dompdf/autoload.inc.php');
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output PDF for inline display
        $filename = $invoice['invoice_no'] . '.pdf';
        $dompdf->stream($filename, array("Attachment" => false));
    }

	    public function view($id) {
        $invoice = $this->Invoice_model->get_invoice_by_id($id);
        if (!$invoice) {
            $this->session->set_flashdata('error', 'Invoice not found');
            redirect('invoice/list');
            return;
        }
        $invoice['items'] = $this->Invoice_model->get_invoice_items($id);
        $invoice['payments'] = $this->Invoice_model->get_payments_by_invoice($id);
        $this->load->view('view_invoice', ['invoice' => $invoice]);
    }

    public function edit($id) {
        // Only admin can edit invoices
        if (function_exists('require_admin')) { require_admin(); }
        $invoice = $this->Invoice_model->get_invoice_by_id($id);
        if (!$invoice) {
            $this->session->set_flashdata('error', 'Invoice not found');
            redirect('invoice/list');
            return;
        }
        // Load related data for edit view
        $invoice['items'] = $this->Invoice_model->get_invoice_items($id);
        $invoice['payments'] = $this->Invoice_model->get_payments_by_invoice($id);
        $payment_methods = $this->Invoice_model->get_payment_methods();
        if ($this->input->method() === 'post') {
            $update = [
                'name'         => $this->input->post('name'),
                'invoice_no'   => $this->input->post('invoice_no'),
                'address'      => $this->input->post('address'),
                'invoice_date' => $this->input->post('invoice_date'),
                'project_code' => $this->input->post('project_code'),
                'project_name' => $this->input->post('project_name'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ];
            // Collect item rows from the form
            $descriptions = (array)$this->input->post('description');
            $amounts      = (array)$this->input->post('amount');
            $items = [];
            for ($i = 0; $i < count($descriptions); $i++) {
                $desc = isset($descriptions[$i]) ? trim($descriptions[$i]) : '';
                $amt  = isset($amounts[$i]) ? $amounts[$i] : '';
                if ($desc !== '' && $amt !== '') {
                    $items[] = ['description' => $desc, 'amount' => (float)$amt];
                }
            }
            if (!empty($items)) {
                $this->Invoice_model->update_invoice_with_items($id, $update, $items);
            } else {
                $this->Invoice_model->update_invoice($id, $update);
            }
                // Update payments if provided
                $payment_amounts = (array)$this->input->post('payment_amount');
                $payment_dates   = (array)$this->input->post('payment_date');
                $payment_modes   = (array)$this->input->post('payment_mode');
                $reference_nos   = (array)$this->input->post('reference_no');
                $remarks_list    = (array)$this->input->post('remarks');

                $payments = [];
                $count = max(count($payment_amounts), count($payment_dates));
                for ($i = 0; $i < $count; $i++) {
                    $payments[] = [
                        'payment_amount' => isset($payment_amounts[$i]) ? $payment_amounts[$i] : '',
                        'payment_date'   => isset($payment_dates[$i]) ? $payment_dates[$i] : '',
                        'payment_mode'   => isset($payment_modes[$i]) ? $payment_modes[$i] : '',
                        'reference_no'   => isset($reference_nos[$i]) ? $reference_nos[$i] : '',
                        'remarks'        => isset($remarks_list[$i]) ? $remarks_list[$i] : '',
                    ];
                }
                $this->load->model('Payment_model');
                $this->Payment_model->delete_payments_by_invoice($id);
                $this->Payment_model->add_multiple_payments($id, $payments);
            $this->session->set_flashdata('success', 'Invoice updated successfully');
            redirect('invoice/list');
            return;
        }
        $this->load->model('Project_model');
        $projects = $this->Project_model->get_projects(1000, 0); // fetch all for dropdown, adjust limit as needed
        $this->load->view('edit_invoice', [
            'invoice' => $invoice,
            'projects' => $projects,
            'payment_methods' => $payment_methods,
        ]);
    }

	public function delete($id) {
        // Only admin can delete invoices
        if (function_exists('require_admin')) { require_admin(); }
        $invoice = $this->Invoice_model->get_invoice_by_id($id);
        if (!$invoice) {
            $this->session->set_flashdata('error', 'Invoice not found');
            redirect('invoice/list');
            return;
        }
        // Delete related payments
        $this->load->model('Payment_model');
        $this->Payment_model->delete_payments_by_invoice($id);
        // Delete invoice and its items
        $this->Invoice_model->delete_invoice($id);
        $this->session->set_flashdata('success', 'Invoice deleted successfully');
        redirect('invoice/list');
    }
}
