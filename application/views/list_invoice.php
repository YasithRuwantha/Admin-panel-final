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
        <div class="table-responsive">
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
                                        $status = '<span class="badge bg-info text-dark">Partially Paid</span>';
                                    } elseif ($total_paid == $invoice_total) {
                                        $status = '<span class="badge bg-success">Paid</span>';
                                    } elseif ($total_paid > $invoice_total) {
                                        $status = '<span class="badge bg-danger">Over Paid</span>';
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
                                    <?php if ($total_paid < $invoice_total): ?>
                                        <button type="button" class="btn btn-success btn-sm mt-1" onclick="showPaymentModal(<?php echo $invoice['id']; ?>, '<?php echo htmlspecialchars($invoice['invoice_no']); ?>')">Receive Payment</button>
                                    <?php endif; ?>
                                    <a href="<?php echo site_url('invoice/pdf/' . $invoice['id']); ?>" class="btn btn-danger btn-lg mt-2 fw-bold d-flex align-items-center justify-content-center" style="gap:6px;" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
                                            <path d="M5.5 7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H5v1h.5a.5.5 0 0 1 0 1H5v1a.5.5 0 0 1-1 0v-4a.5.5 0 0 1 .5-.5h1zm2.5.5a.5.5 0 0 1 .5-.5h.5v4a.5.5 0 0 1-1 0v-4zm2.5-.5a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 1 .5-.5z"/>
                                            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h6.5L14 4.5zm-3-2.5H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V5h-3a1 1 0 0 1-1-1V2z"/>
                                        </svg>
                                        <span>PDF</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center">No invoices found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
                </div>

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
