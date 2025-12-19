<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Invoice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container mt-5" style="margin-left:220px;">
        <h2>List Invoice</h2>
        <!-- Date Range Filter Buttons as Form -->
        <form id="dateRangeForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <label class="me-2 fw-semibold">Filter by date:</label>
            <input type="hidden" name="range" id="rangeInput" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="alpha" id="alphaInput" value="<?php echo htmlspecialchars($alpha ?? 'recent'); ?>">
            <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'today' ? ' active' : ''; ?>" data-range="today">Today</button>
            <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'last7' ? ' active' : ''; ?>" data-range="last7">Last 7 days</button>
            <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'month' ? ' active' : ''; ?>" data-range="month">This month</button>
            <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'all' ? ' active' : ''; ?>" data-range="all">All time</button>
        </form>

        <!-- Alphabetical and Status Filter -->
        <form id="alphaForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <label class="fw-semibold me-2">Sort by Name:</label>
            <select name="alpha" class="form-select form-select-sm" style="width:auto;" onchange="document.getElementById('alphaForm').submit();">
                <option value="recent"<?php echo (!isset($alpha) || $alpha === 'recent') ? ' selected' : ''; ?>>Recent</option>
                <option value="az"<?php echo (isset($alpha) && $alpha === 'az') ? ' selected' : ''; ?>>A-Z</option>
                <option value="za"<?php echo (isset($alpha) && $alpha === 'za') ? ' selected' : ''; ?>>Z-A</option>
            </select>
            <label class="fw-semibold ms-3 me-2">Status:</label>
            <select name="status_filter" class="form-select form-select-sm" style="width:auto;" onchange="document.getElementById('alphaForm').submit();">
                <option value=""<?php echo (!isset($status_filter) || $status_filter === '') ? ' selected' : ''; ?>>All</option>
                <option value="Paid"<?php echo (isset($status_filter) && $status_filter === 'Paid') ? ' selected' : ''; ?>>Paid</option>
                <option value="Over Paid"<?php echo (isset($status_filter) && $status_filter === 'Over Paid') ? ' selected' : ''; ?>>Over Paid</option>
                <option value="Pending"<?php echo (isset($status_filter) && $status_filter === 'Pending') ? ' selected' : ''; ?>>Pending</option>
                <option value="Partially Paid"<?php echo (isset($status_filter) && $status_filter === 'Partially Paid') ? ' selected' : ''; ?>>Partially Paid</option>
            </select>
        </form>

        <!-- Search Bar -->
        <form id="searchForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="text" name="search" id="invoiceSearch" class="form-control" style="max-width:1212px;" placeholder="Search by name, invoice no, address, project code, or status..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <div class="table-responsive">
            </body>
            <script>
            // Date range filter: submit form on button click, reset alpha to default (recent)
            document.querySelectorAll('.filter-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.getElementById('rangeInput').value = btn.getAttribute('data-range');
                    document.getElementById('alphaInput').value = 'recent';
                    document.getElementById('dateRangeForm').submit();
                });
            });
            </script>
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Invoice No</th>
                        <th>Address</th>
                        <th>Date</th>
                        <th>Project Code</th>
                        <!-- <th>Service Description</th> -->
                        <th>Amount</th>
                        <th>Total</th>
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
                                <!-- <td style="word-break:break-word;max-width:180px;white-space:pre-line;">
                                    <?php if (!empty($invoice['items'])): ?>
                                        <?php foreach ($invoice['items'] as $item): ?>
                                            <?php echo htmlspecialchars($item['description']); ?><br>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($invoice['description']); ?>
                                    <?php endif; ?>
                                </td> -->
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
                                            <i class="bi bi-gear"></i> Manage
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="manageDropdown<?php echo $invoice['id']; ?>">
                                            <?php if ($total_paid < $invoice_total && function_exists('is_admin') && is_admin()): ?>
                                                <li><a class="dropdown-item" href="#" onclick="showPaymentModal(<?php echo $invoice['id']; ?>, '<?php echo htmlspecialchars($invoice['invoice_no']); ?>'); return false;"><i class="bi bi-cash-coin"></i> Receive Payment</a></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item" href="<?php echo site_url('invoice/view/' . $invoice['id']); ?>"><i class="bi bi-eye"></i> View</a></li>
                                            <li><a class="dropdown-item" href="<?php echo site_url('invoice/export_invoice/' . $invoice['id']); ?>"><i class="bi bi-download"></i> Export</a></li>
                                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                                <li><a class="dropdown-item" href="<?php echo site_url('invoice/edit/' . $invoice['id']); ?>"><i class="bi bi-pencil-square"></i> Edit</a></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="showDeleteModal(<?php echo $invoice['id']; ?>); return false;"><i class="bi bi-trash"></i> Delete</a></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item text-danger fw-bold d-flex align-items-center" href="<?php echo site_url('invoice/pdf/' . $invoice['id']); ?>" target="_blank"><i class="bi bi-file-earmark-pdf"></i> PDF</a></li>
                                        </ul>
                                    </div>
                                    <!-- Delete Confirmation Modal (one per row, unique id) -->
                                    <div class="modal fade" id="deleteModal<?php echo $invoice['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $invoice['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $invoice['id']; ?>">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this invoice?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <a id="deleteConfirmBtn<?php echo $invoice['id']; ?>" href="<?php echo site_url('invoice/delete/' . $invoice['id']); ?>" class="btn btn-danger">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                    function showDeleteModal(invoiceId) {
                                        var modal = new bootstrap.Modal(document.getElementById('deleteModal' + invoiceId));
                                        modal.show();
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
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item<?php if ($current_page <= 1) echo ' disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" tabindex="-1">Prev</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item<?php if ($i == $current_page) echo ' active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item<?php if ($current_page >= $total_pages) echo ' disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
                </li>
            </ul>
			<br><br>
        </nav>
        <?php endif; ?>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?php echo site_url('invoice/receive_payment'); ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Receive Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="invoice_id" id="modal_invoice_id">
                    <div class="mb-3">
                        <label class="form-label">Invoice No</label>
                        <input type="text" class="form-control" id="modal_invoice_no" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Amount</label>
                        <input type="number" step="0.01" name="payment_amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Mode</label>
                        <select name="payment_mode" class="form-control" required>
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

<style>
/* Enhance visibility of Manage dropdown */
.dropdown-menu {
    font-size: 1.1rem;
    padding: 0.5rem 0;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    min-width: 180px;
}
.dropdown-menu .dropdown-item {
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: box-shadow 0.2s, background 0.2s;
}
.dropdown-menu .dropdown-item i {
    margin-right: 8px;
    font-size: 1.2em;
}
.dropdown-menu .dropdown-item:hover, .dropdown-menu .dropdown-item:focus {
    background: #f5f5f7;
    box-shadow: 0 4px 18px 0 rgba(100,100,100,0.25), 0 1.5px 4px 0 rgba(0,0,0,0.10);
    z-index: 2;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showPaymentModal(invoiceId, invoiceNo) {
    document.getElementById('modal_invoice_id').value = invoiceId;
    document.getElementById('modal_invoice_no').value = invoiceNo;
    var modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}
</script>
    </div>
</div>
</body>
</html>
