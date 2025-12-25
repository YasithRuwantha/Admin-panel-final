        
    
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
        if ($alpha === 'az') {
            $alpha = 'az';
        } elseif ($alpha === 'za') {
            $alpha = 'za';
        } else {
            $alpha = 'recent';
        }

        // Status filter
        $status_filter = $this->input->get('status_filter', true);
        $status_filter = is_string($status_filter) ? trim($status_filter) : '';

        $invoices = $this->Invoice_model->get_invoices_by_date_range_and_search($range, $search, $per_page, $offset, $alpha, $status_filter);
        $total_invoices = $this->Invoice_model->count_invoices_by_date_range_and_search($range, $search, $status_filter);
        $total_pages = ceil($total_invoices / $per_page);
        $this->load->model('Payment_model');
        $payment_methods = $this->Invoice_model->get_payment_methods();
        // For each invoice, fetch its items and payment info
        foreach ($invoices as &$invoice) {
            $invoice['items'] = $this->Invoice_model->get_invoice_items($invoice['id']);
            $invoice['payments'] = $this->Invoice_model->get_payments_by_invoice($invoice['id']);
        }
        $this->load->view('list_invoice', [
            'invoices' => $invoices,
            'payment_methods' => $payment_methods,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'selected_range' => $range,
            'search' => $search,
            'alpha' => $alpha,
            'status_filter' => $status_filter,
            'per_page' => $per_page
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

	// public function export_invoice($id) {
    //     // Clean output buffer to prevent corruption
    //     if (ob_get_length()) ob_end_clean();
    //     $autoloadPath = FCPATH . 'vendor/autoload.php';
    //     if (!file_exists($autoloadPath)) {
    //         $autoloadPath = APPPATH . '../vendor/autoload.php';
    //     }
    //     require_once $autoloadPath;
    //     $invoice = $this->Invoice_model->get_invoice_by_id($id);
    //     if (!$invoice) show_404();
    //     $items = $this->Invoice_model->get_invoice_items($id);
    //     $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     // Title row
    //     $sheet->mergeCells('A1:H1');
    //     $sheet->setCellValue('A1', 'Invoice Details');
    //     $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    //     $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    //     // Header row
    //     $headers = ['Name', 'Invoice No', 'Address', 'Date', 'Project Code', 'Item Description', 'Amount', 'Total'];
    //     $sheet->fromArray($headers, null, 'A2');
    //     $sheet->getStyle('A2:H2')->getFont()->setBold(true);
    //     $sheet->getStyle('A2:H2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2');
    //     $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    //     // Data row(s)
    //     $rowNum = 3;
    //     if (!empty($items)) {
    //         $first = true;
    //         foreach ($items as $item) {
    //             $item_amount = (float)$item['amount'];
    //             $invoice_total = (float)$invoice['amount'];
    //             if ($first) {
    //                 $sheet->fromArray([
    //                     $invoice['name'],
    //                     $invoice['invoice_no'],
    //                     $invoice['address'],
    //                     $invoice['invoice_date'],
    //                     $invoice['project_code'],
    //                     $item['description'],
    //                     $item_amount,
    //                     $invoice_total,
    //                 ], null, 'A' . $rowNum);
    //                 // Set number format for Amount and Total
    //                 $sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
    //                 $sheet->getStyle('H' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
    //                 $first = false;
    //             } else {
    //                 $sheet->fromArray([
    //                     '', '', '', '', '',
    //                     $item['description'],
    //                     $item_amount,
    //                     '',
    //                 ], null, 'A' . $rowNum);
    //                 $sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
    //             }
    //             $rowNum++;
    //         }
    //     } else {
    //         $invoice_total = (float)$invoice['amount'];
    //         $sheet->fromArray([
    //             $invoice['name'],
    //             $invoice['invoice_no'],
    //             $invoice['address'],
    //             $invoice['invoice_date'],
    //             $invoice['project_code'],
    //             $invoice['description'] ?? '',
    //             $invoice_total,
    //             $invoice_total,
    //         ], null, 'A3');
    //         $sheet->getStyle('G3')->getNumberFormat()->setFormatCode('#,##0.00');
    //         $sheet->getStyle('H3')->getNumberFormat()->setFormatCode('#,##0.00');
    //     }
    //     // Auto-size columns
    //     foreach (range('A', 'H') as $col) {
    //         $sheet->getColumnDimension($col)->setAutoSize(true);
    //     }
    //     // Output as XLSX
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment; filename=invoice_' . $id . '_' . date('Ymd_His') . '.xlsx');
    //     $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    //     $writer->save('php://output');
    //     exit;
    // }




	public function export_all() {
            // Clean output buffer to prevent corruption
            if (ob_get_length()) ob_end_clean();
            $autoloadPath = FCPATH . 'vendor/autoload.php';
            if (!file_exists($autoloadPath)) {
                $autoloadPath = APPPATH . '../vendor/autoload.php';
            }
            require_once $autoloadPath;
            $this->load->model('Invoice_model');
            // Get filters from GET params
            $range = $this->input->get('range', true) ?? 'all';
            $search = $this->input->get('search', true) ?? '';
            $alpha = $this->input->get('alpha', true) ?? 'recent';
            $status_filter = $this->input->get('status_filter', true) ?? '';
            $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 10;
            if (!in_array($per_page, [10, 25, 50, 100])) {
                $per_page = 10;
            }
            $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
            if ($page < 1) $page = 1;
            $offset = ($page - 1) * $per_page;
            $invoices = $this->Invoice_model->get_invoices_by_date_range_and_search($range, $search, $per_page, $offset, $alpha, $status_filter);
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            // Title row
            $sheet->mergeCells('A1:H1');
            $sheet->setCellValue('A1', 'All Invoices');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            // Header row (add Status column)
            $headers = ['Name', 'Invoice No', 'Address', 'Date', 'Project Code', 'Item Description', 'Amount', 'Total', 'Status'];
            $sheet->fromArray($headers, null, 'A2');
            $sheet->getStyle('A2:I2')->getFont()->setBold(true);
            $sheet->getStyle('A2:I2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2');
            // Align all header columns A-I to left
            foreach (range('A','I') as $col) {
                $sheet->getStyle($col . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            }

            // Align all data columns A-H to left for all data rows (after $rowNum is set)
            $alignDataRows = function($sheet, $rowStart, $rowEnd) {
                for ($i = $rowStart; $i < $rowEnd; $i++) {
                    foreach (range('A','I') as $col) {
                        $sheet->getStyle($col . $i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    }
                }
            };
            // Data rows
            $rowNum = 3;
            $groupColor1 = 'FFFFFFFF'; // white
            $groupColor2 = 'FFDAECFF'; // light blue
            $useColor1 = true;
            foreach ($invoices as $invoice) {
                $items = $this->Invoice_model->get_invoice_items($invoice['id']);
                $payments = $this->Invoice_model->get_payments_by_invoice($invoice['id']);
                $total_paid = 0;
                foreach ($payments as $pay) {
                    $total_paid += $pay['payment_amount'];
                }
                $invoice_total = (float)$invoice['amount'];
                $status = '';
                $status_detail = '';
                if ($total_paid == 0) {
                    $status = 'Pending';
                } elseif ($total_paid < $invoice_total) {
                    $status = 'Partially Paid';
                    $status_detail = ' (Remaining: ' . number_format($invoice_total - $total_paid, 2) . ')';
                } elseif ($total_paid == $invoice_total) {
                    $status = 'Paid';
                } elseif ($total_paid > $invoice_total) {
                    $status = 'Over Paid';
                    $status_detail = ' (Overpaid: ' . number_format($total_paid - $invoice_total, 2) . ')';
                }
                $first = true;
                $groupStart = $rowNum;
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $item_amount = (float)$item['amount'];
                        if ($first) {
                            $sheet->fromArray([
                                $invoice['name'],
                                $invoice['invoice_no'],
                                $invoice['address'],
                                $invoice['invoice_date'],
                                $invoice['project_code'],
                                $item['description'],
                                $item_amount,
                                $invoice_total,
                                $status . $status_detail,
                            ], null, 'A' . $rowNum);
                            $sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                            $sheet->getStyle('H' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                            $first = false;
                        } else {
                            $sheet->fromArray([
                                '', '', '', '', '',
                                $item['description'],
                                $item_amount,
                                '',
                                '',
                            ], null, 'A' . $rowNum);
                            $sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                        }
                        $rowNum++;
                    }
                } else {
                    $sheet->fromArray([
                        $invoice['name'],
                        $invoice['invoice_no'],
                        $invoice['address'],
                        $invoice['invoice_date'],
                        $invoice['project_code'],
                        $invoice['description'] ?? '',
                        $invoice_total,
                        $invoice_total,
                        $status . $status_detail,
                    ], null, 'A' . $rowNum);
                    $sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle('H' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                    $rowNum++;
                }
                // Color the group rows and keep table grid lines
                $groupEnd = $rowNum - 1;
                $fillColor = $useColor1 ? $groupColor1 : $groupColor2;
                for ($r = $groupStart; $r <= $groupEnd; $r++) {
                    $style = $sheet->getStyle('A' . $r . ':I' . $r);
                    $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($fillColor);
                    $style->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF000000'));
                }
                $useColor1 = !$useColor1;
            }
            // Align all data columns A-H to left for all data rows (now $rowNum is set)
            $alignDataRows($sheet, 3, $rowNum);
            // Auto-size columns
            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            // Output as XLSX (ensure no output before headers)
            if (ob_get_length()) ob_end_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename=invoices_' . date('Ymd_His') . '.xlsx');
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        }
}
