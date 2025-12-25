<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Invoice</title>

    <!-- Essential for mobile responsiveness -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background: #f8f9fa;
            overflow-x: hidden;
        }

        /* Desktop: sidebar offset */
        .main-content {
            margin-left: 220px;
            transition: margin-left 0.3s ease;
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
                padding: 20px 15px !important;
            }

            /* Search input full width on mobile */
            #invoiceSearch {
                max-width: 100% !important;
                width: 100%;
            }

            /* Table improvements for mobile */
            .table-responsive {
                font-size: 0.875rem;
            }

            th, td { white-space: nowrap; }

            th:nth-child(1), td:nth-child(1) { min-width: 140px; }
            th:nth-child(2), td:nth-child(2) { min-width: 120px; }
            th:nth-child(3), td:nth-child(3) { min-width: 160px; }
            th:nth-child(4), td:nth-child(4) { min-width: 110px; }
            th:nth-child(5), td:nth-child(5) { min-width: 130px; }
            th:nth-child(6), td:nth-child(6) { min-width: 120px; }
            th:nth-child(7), td:nth-child(7) { min-width: 120px; }



            /* Stack grouped filters */
            .filters-upper-row {
                flex-direction: row !important;
                flex-wrap: wrap;
                gap: 1rem;
                width: 100%;
            }
            .filters-upper-row > div {
                flex: 1 1 auto;
                min-width: 200px;
            }

            /* Date filter buttons */
            #dateRangeForm {
                flex-direction: column !important;
                align-items: flex-start !important;
            }
            #dateRangeForm .btn {
                min-width: 100px;
            }

            /* Export button full width on mobile */
            .export-mobile {
                width: 100%;
                margin-top: 0.5rem;
            }
        }

        @media (min-width: 769px) {
            .filters-upper-row {
                gap: 2rem;
            }
            .export-desktop {
                margin-left: 1.5rem;
            }
        }

        /* Date filter buttons - same as Home */
        .date-btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .date-btn-group .btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            min-width: 70px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        @media (max-width: 576px) {
            .date-btn-group .btn {
                font-size: 0.72rem;
                padding: 0.2rem 0.4rem;
            }
        }
        #dateRangeForm {
            align-items: flex-start;
        }
        #dateRangeForm label {
            margin-bottom: 0;
            margin-top: 0.35rem;
        }

        /* Dropdown menu styling */
        .dropdown-menu {
            font-size: 1.1rem;
            padding: 0.5rem 0;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            min-width: 180px;
        }
        .dropdown-menu .dropdown-item {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        .dropdown-menu .dropdown-item i {
            margin-right: 8px;
            font-size: 1.2em;
        }
        .dropdown-menu .dropdown-item:hover, .dropdown-menu .dropdown-item:focus {
            background: #f5f5f7;
            box-shadow: 0 4px 18px 0 rgba(100,100,100,0.25), 0 1.5px 4px 0 rgba(0,0,0,0.10);
        }
    </style>
</head>
<body>

<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container-fluid mt-4 px-4 main-content">
        <!-- Updated header with + Add Invoice button exactly like List Projects -->
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap header-row">
            <h2 class="mb-0">List Invoice</h2>
            <a href="<?php echo site_url('invoice/add_invoice'); ?>" class="btn btn-primary add-invoice-btn">+ Add Invoice</a>
        </div>
        <style>
        @media (max-width: 768px) {
            .header-row {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 1rem !important;
            }
            .add-invoice-btn {
                width: 100% !important;
                min-width: 0 !important;
                justify-content: center;
                display: flex;
            }
        }
        </style>

        <!-- Date Range Filter Buttons - same style as Home -->
        <form id="dateRangeForm" method="get" class="mb-4 d-flex flex-column flex-sm-row flex-wrap align-items-start gap-3">
            <label class="fw-semibold nowrap me-3">Filter by date:</label>
            <input type="hidden" name="range" id="rangeInput" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="alpha" id="alphaInput" value="<?php echo htmlspecialchars($alpha ?? 'recent'); ?>">
            <div class="date-btn-group">
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'today' ? ' active' : ''; ?>" data-range="today">Today</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'last7' ? ' active' : ''; ?>" data-range="last7">Last 7 days</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'month' ? ' active' : ''; ?>" data-range="month">This month</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'all' ? ' active' : ''; ?>" data-range="all">All time</button>
            </div>
        </form>

        <!-- Sort, Status, Rows per page + Export - grouped like Home/List Projects -->
        <form id="alphaForm" method="get" class="mb-3">
            <div class="d-flex align-items-center filters-upper-row flex-wrap">
                <div class="d-flex align-items-center gap-2">
                    <label class="fw-semibold me-2 mb-0">Sort by Name:</label>
                    <select name="alpha" class="form-select form-select-sm" style="width:auto;min-width:120px;" onchange="this.form.submit();">
                        <option value="recent"<?php echo (!isset($alpha) || $alpha === 'recent') ? ' selected' : ''; ?>>Recent</option>
                        <option value="az"<?php echo (isset($alpha) && $alpha === 'az') ? ' selected' : ''; ?>>A-Z</option>
                        <option value="za"<?php echo (isset($alpha) && $alpha === 'za') ? ' selected' : ''; ?>>Z-A</option>
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <label class="fw-semibold me-2 mb-0">Status:</label>
                    <select name="status_filter" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit();">
                        <option value=""<?php echo (!isset($status_filter) || $status_filter === '') ? ' selected' : ''; ?>>All</option>
                        <option value="Paid"<?php echo (isset($status_filter) && $status_filter === 'Paid') ? ' selected' : ''; ?>>Paid</option>
                        <option value="Over Paid"<?php echo (isset($status_filter) && $status_filter === 'Over Paid') ? ' selected' : ''; ?>>Over Paid</option>
                        <option value="Pending"<?php echo (isset($status_filter) && $status_filter === 'Pending') ? ' selected' : ''; ?>>Pending</option>
                        <option value="Partially Paid"<?php echo (isset($status_filter) && $status_filter === 'Partially Paid') ? ' selected' : ''; ?>>Partially Paid</option>
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <label for="perPageSelect" class="fw-semibold me-2 mb-0">Number of rows:</label>
                    <select name="per_page" id="perPageSelect" class="form-select form-select-sm" style="width:auto;min-width:100px;" onchange="this.form.submit();">
                        <?php $perPageOptions = [10, 25, 50, 100]; ?>
                        <?php foreach ($perPageOptions as $opt): ?>
                            <option value="<?php echo $opt; ?>"<?php echo (isset($per_page) && $per_page == $opt) ? ' selected' : ''; ?>><?php echo $opt; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Export button - desktop inline, mobile full width -->
                    <?php
                        $export_params = [];
                        if (!empty($selected_range)) $export_params['range'] = $selected_range;
                        if (!empty($search)) $export_params['search'] = $search;
                        if (!empty($alpha)) $export_params['alpha'] = $alpha;
                        if (!empty($status_filter)) $export_params['status_filter'] = $status_filter;
                        if (!empty($per_page)) $export_params['per_page'] = $per_page;
                        if (!empty($current_page)) $export_params['page'] = $current_page;
                        $export_url = site_url('invoice/export_all');
                        if (!empty($export_params)) $export_url .= '?' . http_build_query($export_params);
                    ?>
                    <a href="<?php echo $export_url; ?>" class="btn btn-success btn-sm export-desktop d-none d-lg-inline-flex align-items-center">
                        Export Invoices
                    </a>
                </div>

                <!-- Mobile export fallback: full width and margin-top -->
                <div class="ms-auto d-lg-none">
                    <a href="<?php echo $export_url; ?>" class="btn btn-success ">
                        Export Invoices
                    </a>
                </div>
            </div>

            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>">
        </form>

        <!-- Search Bar (List Projects style) -->
        <form id="searchForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="text" name="search" id="invoiceSearch" class="form-control" style="max-width:1250px;" placeholder="Search by name, invoice no, address, project code, or status..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <button type="submit" class="btn btn-primary" style="min-width:120px; font-weight:500; font-size:1.05rem;">Search</button>
        </form>
        <style>
        @media (max-width: 768px) {
            #invoiceSearch {
                max-width: 100% !important;
                width: 100%;
            }
            #searchForm .btn {
                width: 100%;
                margin-top: 0.5rem;
            }
        }
        </style>

        <div class="table-responsive bg-white rounded shadow-sm p-4" style="min-height:500px;">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Invoice No</th>
                        <th>Address</th>
                        <th>Date</th>
                        <th>Project Code</th>
                        <th>Amount</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($invoices)): ?>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"> <?php echo htmlspecialchars($invoice['name']); ?> </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"> <?php echo htmlspecialchars($invoice['invoice_no']); ?> </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"> <?php echo htmlspecialchars($invoice['address']); ?> </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"> <?php echo htmlspecialchars($invoice['invoice_date']); ?> </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"> <?php echo htmlspecialchars($invoice['project_code']); ?> </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;">
                                    <?php if (!empty($invoice['items'])): ?>
                                        <?php foreach ($invoice['items'] as $item): ?>
                                            <?php echo htmlspecialchars(number_format((float)$item['amount'], 2)); ?><br>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars(number_format((float)$invoice['amount'], 2)); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line; font-weight:bold;">
                                    <?php echo htmlspecialchars(number_format((float)$invoice['amount'], 2)); ?>
                                </td>
                                <td>
                                    <?php 
                                    $total_paid = 0;
                                    if (!empty($invoice['payments'])) {
                                        foreach ($invoice['payments'] as $pay) {
                                            $total_paid += $pay['payment_amount'];
                                        }
                                    }
                                    $invoice_total = $invoice['amount'];
                                    $status = '';
                                    if ($total_paid == 0) {
                                        $status = '<span class="badge bg-warning text-dark">Pending</span>';
                                    } elseif ($total_paid < $invoice_total) {
                                        $remaining = max(0, (float)$invoice_total - (float)$total_paid);
                                        $status = '<span class="badge bg-info text-dark">Partially Paid</span>' .
                                                  '<div class="small text-muted mt-1"><span class="fw-bold">Remaining To Pay:</span> <span class="fw-bold">' . htmlspecialchars(number_format($remaining, 2)) . '</span></div>';
                                    } elseif ($total_paid == $invoice_total) {
                                        $status = '<span class="badge bg-success">Paid</span>';
                                    } elseif ($total_paid > $invoice_total) {
                                        $overpaid = max(0, (float)$total_paid - (float)$invoice_total);
                                        $status = '<span class="badge bg-danger">Over Paid</span>' .
                                                  '<div class="small text-muted mt-1"><span class="fw-bold">Overpaid:</span> <span class="fw-bold">' . htmlspecialchars(number_format($overpaid, 2)) . '</span></div>';
                                    }
                                    echo $status;
                                    ?>
                                    <?php if (!empty($invoice['payments'])): ?>
                                        <div class="mt-1">
                                            <?php foreach ($invoice['payments'] as $pay): ?>
                                                <div class="border rounded p-2 mb-1 bg-light">
                                                    <small><b>Amount:</b> <?php echo htmlspecialchars(number_format((float)$pay['payment_amount'], 2)); ?></small><br>
                                                    <small><b>Date:</b> <?php echo htmlspecialchars($pay['payment_date']); ?></small><br>
                                                    <small><b>Mode:</b> <?php echo htmlspecialchars($pay['payment_mode']); ?></small><br>
                                                    <?php if (!empty($pay['reference_no'])): ?><small><b>Ref:</b> <?php echo htmlspecialchars($pay['reference_no']); ?></small><br><?php endif; ?>
                                                    <?php if (!empty($pay['remarks'])): ?><small><b>Remarks:</b> <?php echo htmlspecialchars($pay['remarks']); ?></small><?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="dropdown mt-2">
                                        <button class="btn btn-primary dropdown-toggle w-100" type="button" id="manageDropdown<?php echo $invoice['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                            Manage
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="manageDropdown<?php echo $invoice['id']; ?>">
                                            <?php if ($total_paid < $invoice_total && function_exists('is_admin') && is_admin()): ?>
                                                <li><a class="dropdown-item" href="#" onclick="showPaymentModal(<?php echo $invoice['id']; ?>, '<?php echo htmlspecialchars($invoice['invoice_no']); ?>'); return false;">Receive Payment</a></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item" href="<?php echo site_url('invoice/view/' . $invoice['id']); ?>">View</a></li>
                                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                                <li><a class="dropdown-item" href="<?php echo site_url('invoice/edit/' . $invoice['id']); ?>">Edit</a></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="showDeleteModal(<?php echo $invoice['id']; ?>); return false;">Delete</a></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item text-danger fw-bold d-flex align-items-center" href="<?php echo site_url('invoice/pdf/' . $invoice['id']); ?>" target="_blank">PDF</a></li>
                                        </ul>
                                    </div>
                                    <div class="modal fade" id="deleteModal<?php echo $invoice['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">Are you sure you want to delete this invoice?</div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <a href="<?php echo site_url('invoice/delete/' . $invoice['id']); ?>" class="btn btn-danger">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                    function showDeleteModal(invoiceId) {
                                        new bootstrap.Modal(document.getElementById('deleteModal' + invoiceId)).show();
                                    }
                                    </script>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center">No invoices found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center flex-wrap">
                <?php
                $query_params = [
                    'range' => htmlspecialchars($selected_range ?? 'all'),
                    'search' => htmlspecialchars($search ?? ''),
                    'alpha' => htmlspecialchars($alpha ?? 'recent'),
                    'status_filter' => htmlspecialchars($status_filter ?? ''),
                    'per_page' => htmlspecialchars($per_page ?? 10)
                ];
                $base_query = http_build_query($query_params);
                ?>
                <li class="page-item<?php if ($current_page <= 1) echo ' disabled'; ?>">
                    <a class="page-link" href="?<?php echo $base_query . '&page=' . ($current_page - 1); ?>">Prev</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item<?php if ($i == $current_page) echo ' active'; ?>">
                        <a class="page-link" href="?<?php echo $base_query . '&page=' . $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item<?php if ($current_page >= $total_pages) echo ' disabled'; ?>">
                    <a class="page-link" href="?<?php echo $base_query . '&page=' . ($current_page + 1); ?>">Next</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>

        <!-- Payment Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="<?php echo site_url('invoice/receive_payment'); ?>">
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentModalLabel">Receive Payment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="invoice_id" id="modal_invoice_id">
                            <div class="mb-3">
                                <label class="form-label">Invoice No</label>
                                <input type="text" class="form-control" id="modal_invoice_no" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Amount</label>
                                <input type="text" step="0.01" name="payment_amount" class="form-control amount-input" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Date</label>
                                <input type="date" name="payment_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Mode</label>
                                <select name="payment_mode" class="form-select" required>
                                    <?php if (!empty($payment_methods)): ?>
                                        <?php foreach ($payment_methods as $method): ?>
                                            <option value="<?php echo htmlspecialchars($method['config_value']); ?>">
                                                <?php echo htmlspecialchars($method['config_value']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Reference No</label>
                                <input type="text" name="reference_no" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Date range filter
document.querySelectorAll('.filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('rangeInput').value = btn.getAttribute('data-range');
        document.getElementById('alphaInput').value = 'recent';
        document.getElementById('dateRangeForm').submit();
    });
});

// Thousand separator for payment modal
function formatWithCommas(val) {
    val = val.replace(/,/g, '');
    if (val === '' || isNaN(val)) return '';
    let parts = val.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join('.');
}
function removeCommas(val) {
    return val.replace(/,/g, '');
}
function setupAmountInput() {
    var input = document.querySelector('#paymentModal .amount-input');
    if (input) {
        input.addEventListener('input', function() {
            this.value = formatWithCommas(removeCommas(this.value));
        });
        input.addEventListener('focus', function() {
            this.value = removeCommas(this.value);
        });
        input.addEventListener('blur', function() {
            this.value = formatWithCommas(removeCommas(this.value));
        });
        input.form.addEventListener('submit', function() {
            input.value = removeCommas(input.value);
        });
    }
}
function showPaymentModal(id, no) {
    document.getElementById('modal_invoice_id').value = id;
    document.getElementById('modal_invoice_no').value = no;
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
    setTimeout(setupAmountInput, 200);
}
</script>

</body>
</html>
