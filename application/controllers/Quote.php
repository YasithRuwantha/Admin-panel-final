        
    
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
            'alpha' => $alpha,
            'per_page' => $per_page
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

        // Get show_signature param from GET (default true if not set)
        $show_signature = $this->input->get('show_signature');
        if ($show_signature === null) {
            $show_signature = true;
        } else {
            $show_signature = ($show_signature == '1' || $show_signature === true || $show_signature === 'true');
        }

        // Load HTML content
        $data['quote'] = $quote;
        $data['show_signature'] = $show_signature;
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


	// public function export_quote($id) {
    //     // Clean output buffer to prevent corruption
    //     if (ob_get_length()) ob_end_clean();
    //     $autoloadPath = FCPATH . 'vendor/autoload.php';
    //     if (!file_exists($autoloadPath)) {
    //         $autoloadPath = APPPATH . '../vendor/autoload.php';
    //     }
    //     require_once $autoloadPath;
    //     $quote = $this->Quote_model->get_quote_by_id($id);
    //     if (!$quote) show_404();
    //     $items = $this->Quote_model->get_quote_items($id);
    //     $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     // Title row
    //     $sheet->mergeCells('A1:H1');
    //     $sheet->setCellValue('A1', 'Quotation Details');
    //     $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    //     $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    //     // Header row
    //     $headers = ['Name', 'Quotation No', 'Address', 'Date', 'Project Code', 'Item Description', 'Amount', 'Total'];
    //     $sheet->fromArray($headers, null, 'A2');
    //     $sheet->getStyle('A2:H2')->getFont()->setBold(true);
    //     $sheet->getStyle('A2:H2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2');
    //     $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    //     // Data row(s)
    //     $rowNum = 3;
    //     if (!empty($items)) {
    //         foreach ($items as $item) {
    //             $sheet->fromArray([
    //                 $quote['name'],
    //                 $quote['quotation_no'],
    //                 $quote['address'],
    //                 $quote['quote_date'],
    //                 $quote['project_code'],
    //                 $item['description'],
    //                 $item['amount'],
    //                 $quote['amount'],
    //             ], null, 'A' . $rowNum);
    //             $rowNum++;
    //         }
    //     } else {
    //         $sheet->fromArray([
    //             $quote['name'],
    //             $quote['quotation_no'],
    //             $quote['address'],
    //             $quote['quote_date'],
    //             $quote['project_code'],
    //             $quote['description'] ?? '',
    //             $quote['amount'],
    //             $quote['amount'],
    //         ], null, 'A3');
    //     }
    //     // Auto-size columns
    //     foreach (range('A', 'H') as $col) {
    //         $sheet->getColumnDimension($col)->setAutoSize(true);
    //     }
    //     // Output as XLSX
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment; filename=quotation_' . $id . '_' . date('Ymd_His') . '.xlsx');
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
            $this->load->model('Quote_model');
            // Get filters from GET params
            $range = $this->input->get('range', true) ?? 'all';
            $search = $this->input->get('search', true) ?? '';
            $alpha = $this->input->get('alpha', true) ?? 'recent';
            $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 10;
            if (!in_array($per_page, [10, 25, 50, 100])) {
                $per_page = 10;
            }
            $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
            if ($page < 1) $page = 1;
            $offset = ($page - 1) * $per_page;
            $quotations = $this->Quote_model->get_quotes_by_date_range_and_search($range, $search, $per_page, $offset, $alpha);
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            // Title row
            $sheet->mergeCells('A1:H1');
            $sheet->setCellValue('A1', 'All Quotations');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            // Header row
            $headers = ['Name', 'Quotation No', 'Address', 'Date', 'Project Code', 'Item Description', 'Amount', 'Total'];
            $sheet->fromArray($headers, null, 'A2');
            $sheet->getStyle('A2:H2')->getFont()->setBold(true);
            $sheet->getStyle('A2:H2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2');
            // Align header columns: all left except Amount and Total (G, H) right
            foreach (range('A','F') as $col) {
                $sheet->getStyle($col . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            }
            foreach (['G','H'] as $col) {
                $sheet->getStyle($col . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }
            // Data rows
            $rowNum = 3;
            $groupColor1 = 'FFFFFFFF'; // white
            $groupColor2 = 'FFDAECFF'; // light blue
            $useColor1 = true;
            foreach ($quotations as $quote) {
                $items = $this->Quote_model->get_quote_items($quote['id']);
                $first = true;
                $groupStart = $rowNum;
                if (!empty($items)) {
                    foreach ($items as $item) {
                        if ($first) {
                            $sheet->fromArray([
                                $quote['name'],
                                $quote['quotation_no'],
                                $quote['address'],
                                $quote['quote_date'],
                                $quote['project_code'],
                                $item['description'],
                                $item['amount'],
                                $quote['amount'],
                            ], null, 'A' . $rowNum);
                            $first = false;
                        } else {
                            $sheet->fromArray([
                                '', '', '', '', '',
                                $item['description'],
                                $item['amount'],
                                '',
                            ], null, 'A' . $rowNum);
                        }
                        // Align columns for this row: A-F left, G-H right
                        foreach (range('A','F') as $col) {
                            $sheet->getStyle($col . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                        }
                        foreach (['G','H'] as $col) {
                            $sheet->getStyle($col . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        }
                        $rowNum++;
                    }
                } else {
                    $sheet->fromArray([
                        $quote['name'],
                        $quote['quotation_no'],
                        $quote['address'],
                        $quote['quote_date'],
                        $quote['project_code'],
                        $quote['description'] ?? '',
                        $quote['amount'],
                        $quote['amount'],
                    ], null, 'A' . $rowNum);
                    foreach (range('A','F') as $col) {
                        $sheet->getStyle($col . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    }
                    foreach (['G','H'] as $col) {
                        $sheet->getStyle($col . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    }
                    $rowNum++;
                }
                // Color the group rows and keep table grid lines
                $groupEnd = $rowNum - 1;
                $fillColor = $useColor1 ? $groupColor1 : $groupColor2;
                for ($r = $groupStart; $r <= $groupEnd; $r++) {
                    $style = $sheet->getStyle('A' . $r . ':H' . $r);
                    $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($fillColor);
                    $style->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF000000'));
                }
                $useColor1 = !$useColor1;
            }
            // Auto-size columns
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            // Output as XLSX
            if (ob_get_length()) ob_end_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename=quotations_' . date('Ymd_His') . '.xlsx');
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        }


}
