<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Invoice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container-fluid" style="margin-left:220px; padding-top:40px;">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 col-md-11">
                <div class="card shadow-sm border-0" style="background:#fff; min-height:730px; width:100%; max-width:1100px; margin:auto;">
                    <div class="card-header d-flex align-items-center border-bottom" style="border-radius:0.5rem 0.5rem 0 0; background:#fff;">
                        <i class="bi bi-file-earmark-plus" style="font-size:1.5rem;margin-right:10px;color:#0d6efd;"></i>
                        <h4 class="mb-0" style="color:#222;font-weight:600;">Add New Invoice</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if($this->session->flashdata('success')): ?>
                            <div class="alert alert-success mb-3"><?php echo $this->session->flashdata('success'); ?></div>
                        <?php endif; ?>
                        <?php if($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger mb-3"><?php echo $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name of the Client</label>
                                    <input type="text" name="name" class="form-control" required placeholder="Enter client name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Invoice No</label>
                                    <input type="text" name="invoice_no" class="form-control" required placeholder="Enter invoice number">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control" required placeholder="Enter address">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="invoice_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Project Code</label>
                                <input type="text" name="project_code" class="form-control" required placeholder="Enter project code">
                            </div>
                            <div class="mb-3">
                                <table class="table table-bordered" id="services-table" style="background:#f8f9fa;border:2px solid #000;box-shadow:0 2px 8px rgba(0,0,0,0.08);margin-bottom:20px;">
                                    <thead>
                                        <tr style="background:#f8f9fa;">
                                            <th>Service Description</th>
                                            <th>Amount</th>
                                            <th style="width:60px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="description[]" class="form-control" required placeholder="Enter service description"></td>
                                            <td><input type="number" step="0.01" name="amount[]" class="form-control amount-input" required placeholder="0.00"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3"><button type="button" class="btn btn-sm" id="add-row" style="background:#0d6dfc;color:#fff;"><i class="bi bi-plus-circle me-2"></i>Add Service</button></td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold;">Total</td>
                                            <td><input type="number" step="0.01" name="total" class="form-control" readonly value="0" id="total"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4 py-2"><i class="bi bi-save me-2"></i>Save</button>
                                <button type="button" class="btn btn-success ms-2 px-4 py-2" onclick="showPaymentModalForAddInvoice()"><i class="bi bi-cash-coin me-2"></i>Receive Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
                            <input type="hidden" name="invoice_id" id="modal_invoice_id_add">
                            <div class="mb-3">
                                <label class="form-label">Invoice No</label>
                                <input type="text" class="form-control" id="modal_invoice_no_add" readonly>
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
        function showPaymentModalForAddInvoice() {
                // Try to get invoice_no from the form if available
                var invoiceNo = document.querySelector('input[name="invoice_no"]').value;
                document.getElementById('modal_invoice_id_add').value = '';
                document.getElementById('modal_invoice_no_add').value = invoiceNo;
                var modal = new bootstrap.Modal(document.getElementById('paymentModal'));
                modal.show();
        }
        </script>
        </form>
        <script>
            function updateTotal() {
                let total = 0;
                document.querySelectorAll('.amount-input').forEach(function(input) {
                    let val = parseFloat(input.value);
                    if (!isNaN(val)) total += val;
                });
                document.getElementById('total').value = total.toFixed(2);
            }
            document.getElementById('add-row').addEventListener('click', function() {
                let tbody = document.querySelector('#services-table tbody');
                let row = document.createElement('tr');
                row.innerHTML = '<td><input type="text" name="description[]" class="form-control" required></td>' +
                               '<td><input type="number" step="0.01" name="amount[]" class="form-control amount-input" required></td>' +
                               '<td><button type="button" class="btn btn-danger btn-sm remove-row">&times;</button></td>';
                tbody.appendChild(row);
            });
            document.querySelector('#services-table').addEventListener('input', function(e) {
                if (e.target.classList.contains('amount-input')) {
                    updateTotal();
                }
            });
            document.querySelector('#services-table').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row')) {
                    let row = e.target.closest('tr');
                    row.parentNode.removeChild(row);
                    updateTotal();
                }
            });
            updateTotal();
        </script>
    </div>
</div>
</body>
</html>
