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
                        <th>Service Description</th>
                        <th>Amount</th>
                        <th>Total</th>
                        <th>Received Payments</th>
                        <th></th>
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
                                            <?php echo htmlspecialchars($item['description']); ?><br>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($invoice['description']); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;">
                                    <?php if (!empty($invoice['items'])): ?>
                                        <?php foreach ($invoice['items'] as $item): ?>
                                            <?php echo htmlspecialchars(number_format($item['amount'], 2)); ?><br>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars(number_format($invoice['amount'], 2)); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line; font-weight:bold;">
                                    <?php echo htmlspecialchars(number_format($invoice['amount'], 2)); ?>
                                </td>
                                <td style="word-break:break-word;max-width:220px;white-space:pre-line;">
                                    <?php if (!empty($invoice['payments'])): ?>
                                        <?php foreach ($invoice['payments'] as $payment): ?>
                                            <div style="margin-bottom:6px;">
                                                <span class="badge bg-success"><?php echo htmlspecialchars(number_format($payment['payment_amount'], 2)); ?></span>
                                                <small><?php echo htmlspecialchars($payment['payment_date']); ?></small><br>
                                                <span><?php echo htmlspecialchars($payment['payment_mode']); ?></span>
                                                <?php if (!empty($payment['reference_no'])): ?>
                                                    <span>Ref: <?php echo htmlspecialchars($payment['reference_no']); ?></span>
                                                <?php endif; ?>
                                                <?php if (!empty($payment['remarks'])): ?>
                                                    <span>Remarks: <?php echo htmlspecialchars($payment['remarks']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">No payments</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" onclick="showPaymentModal(<?php echo $invoice['id']; ?>, '<?php echo htmlspecialchars($invoice['invoice_no']); ?>')">Receive Payment</button>
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
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Online">Online</option>
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
