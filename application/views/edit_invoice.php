<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Invoice</title>

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
        .main-container {
            margin-left: 220px;
            padding-top: 40px;
            transition: margin-left 0.3s ease;
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .main-container {
                margin-left: 0 !important;
                padding: 20px 15px;
            }

            /* Stack form rows vertically */
            .row.mb-3 > .col-md-6 {
                margin-bottom: 1rem;
            }

            /* Make inputs full width */
            .form-control,
            .form-select {
                width: 100%;
            }

            /* Improve table responsiveness */
            .table-responsive {
                font-size: 0.9rem;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            #itemsTable th:nth-child(1),
            #itemsTable td:nth-child(1) { min-width: 180px; } /* Description */

            #paymentsTable th,
            #paymentsTable td {
                min-width: 100px;
                padding: 0.5rem;
            }

            #paymentsTable th:last-child,
            #paymentsTable td:last-child {
                min-width: 80px;
            }

            /* Make remove buttons smaller and centered */
            .btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }

            /* Stack action buttons on mobile */
            .action-buttons {
                flex-direction: column !important;
                gap: 0.75rem;
                width: 100%;
            }

            .action-buttons .btn {
                width: 100%;
                justify-content: center;
            }

            /* Card full width on mobile */
            .card {
                max-width: 100% !important;
                margin: 0 auto;
            }
        }

        /* Card styling */
        .card {
            border-radius: 0.75rem;
            background: #fff;
        }

        .card-header {
            background: #fff !important;
            border-bottom: 1px solid #dee2e6;
        }

        .card-header h4 {
            color: #222;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    
    <div class="container-fluid main-container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 col-md-11 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex align-items-center border-bottom">
                        <i class="bi bi-pencil-square" style="font-size:1.5rem;margin-right:10px;color:#0d6efd;"></i>
                        <h4 class="mb-0">Edit Invoice</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if($this->session->flashdata('success')): ?>
                            <div class="alert alert-success mb-3"><?php echo $this->session->flashdata('success'); ?></div>
                        <?php endif; ?>
                        <?php if($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger mb-3"><?php echo $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>

                        <form method="post" action="<?php echo site_url('invoice/edit/' . $invoice['id']); ?>">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Client Name</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($invoice['name']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Invoice No</label>
                                    <input type="text" name="invoice_no" class="form-control" value="<?php echo htmlspecialchars($invoice['invoice_no']); ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Items</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th style="width:70%">Description</th>
                                                <th style="width:20%">Amount</th>
                                                <th style="width:10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($invoice['items'])): ?>
                                                <?php foreach ($invoice['items'] as $item): ?>
                                                    <tr>
                                                        <td><input type="text" name="description[]" class="form-control" value="<?php echo htmlspecialchars($item['description']); ?>"></td>
                                                        <td><input type="number" step="0.01" name="amount[]" class="form-control" value="<?php echo htmlspecialchars($item['amount']); ?>"></td>
                                                        <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItemRow(this)">Remove</button></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td><input type="text" name="description[]" class="form-control" placeholder="Service description"></td>
                                                    <td><input type="number" step="0.01" name="amount[]" class="form-control" placeholder="0.00"></td>
                                                    <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItemRow(this)">Remove</button></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItemRow()">
                                    <i class="bi bi-plus-lg me-1"></i>Add Item
                                </button>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Received Payments</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="paymentsTable">
                                        <thead>
                                            <tr>
                                                <th>Amount</th>
                                                <th>Date</th>
                                                <th>Mode</th>
                                                <th>Reference No</th>
                                                <th>Remarks</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($invoice['payments'])): ?>
                                                <?php foreach ($invoice['payments'] as $pay): ?>
                                                    <tr>
                                                        <td><input type="number" step="0.01" name="payment_amount[]" class="form-control" value="<?php echo htmlspecialchars($pay['payment_amount']); ?>"></td>
                                                        <td><input type="date" name="payment_date[]" class="form-control" value="<?php echo htmlspecialchars($pay['payment_date']); ?>"></td>
                                                        <td>
                                                            <select name="payment_mode[]" class="form-select">
                                                                <?php if (!empty($payment_methods)): ?>
                                                                    <?php foreach ($payment_methods as $method): ?>
                                                                        <?php $val = isset($method['config_value']) ? $method['config_value'] : (isset($method['value']) ? $method['value'] : ''); ?>
                                                                        <option value="<?php echo htmlspecialchars($val); ?>" <?php echo ($pay['payment_mode'] === $val) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($val); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="reference_no[]" class="form-control" value="<?php echo htmlspecialchars($pay['reference_no']); ?>"></td>
                                                        <td><input type="text" name="remarks[]" class="form-control" value="<?php echo htmlspecialchars($pay['remarks']); ?>"></td>
                                                        <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removePaymentRow(this)">Remove</button></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td><input type="number" step="0.01" name="payment_amount[]" class="form-control" placeholder="0.00"></td>
                                                    <td><input type="date" name="payment_date[]" class="form-control"></td>
                                                    <td>
                                                        <select name="payment_mode[]" class="form-select">
                                                            <?php if (!empty($payment_methods)): ?>
                                                                <?php foreach ($payment_methods as $method): ?>
                                                                    <?php $val = isset($method['config_value']) ? $method['config_value'] : (isset($method['value']) ? $method['value'] : ''); ?>
                                                                    <option value="<?php echo htmlspecialchars($val); ?>"><?php echo htmlspecialchars($val); ?></option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="reference_no[]" class="form-control" placeholder="Ref #"></td>
                                                    <td><input type="text" name="remarks[]" class="form-control" placeholder="Remarks"></td>
                                                    <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removePaymentRow(this)">Remove</button></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="addPaymentRow()">
                                    <i class="bi bi-plus-lg me-1"></i>Add Payment
                                </button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($invoice['address']); ?></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="invoice_date" class="form-control" value="<?php echo htmlspecialchars($invoice['invoice_date']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Project Code</label>
                                    <input type="text" name="project_code" class="form-control" value="<?php echo htmlspecialchars($invoice['project_code']); ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Project Name</label>
                                    <input type="text" name="project_name" class="form-control" value="<?php echo htmlspecialchars($invoice['project_name']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Total</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(number_format((float)$invoice['amount'], 2)); ?>" readonly>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end action-buttons">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal">
                                    <i class="bi bi-save me-2"></i>Update
                                </button>
                                <a href="<?php echo site_url('invoice/list'); ?>" class="btn btn-outline-secondary ms-2">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                            </div>

                            <!-- Confirmation Modal -->
                            <div class="modal fade" id="confirmUpdateModal" tabindex="-1" aria-labelledby="confirmUpdateModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmUpdateModalLabel">Confirm Update</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to update this invoice?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-primary" onclick="this.closest('form').submit();">Yes, Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function addItemRow() {
    var tbody = document.querySelector('#itemsTable tbody');
    var tr = document.createElement('tr');
    tr.innerHTML = '<td><input type="text" name="description[]" class="form-control" placeholder="Service description"></td>' +
                   '<td><input type="number" step="0.01" name="amount[]" class="form-control" placeholder="0.00"></td>' +
                   '<td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItemRow(this)">Remove</button></td>';
    tbody.appendChild(tr);
}

function removeItemRow(btn) {
    var tr = btn.closest('tr');
    var tbody = tr.parentNode;
    if (tbody.children.length > 1) {
        tbody.removeChild(tr);
    } else {
        tr.querySelector('input[name="description[]"]').value = '';
        tr.querySelector('input[name="amount[]"]').value = '';
    }
}

function addPaymentRow() {
    var tbody = document.querySelector('#paymentsTable tbody');
    var tr = document.createElement('tr');
    tr.innerHTML = '<td><input type="number" step="0.01" name="payment_amount[]" class="form-control" placeholder="0.00"></td>' +
                   '<td><input type="date" name="payment_date[]" class="form-control"></td>' +
                   '<td><select name="payment_mode[]" class="form-select">' +
                   '<?php if (!empty($payment_methods)): foreach ($payment_methods as $method): $val = isset($method["config_value"]) ? $method["config_value"] : (isset($method["value"]) ? $method["value"] : ""); ?>' +
                   '<option value="<?php echo htmlspecialchars($val); ?>"><?php echo htmlspecialchars($val); ?></option>' +
                   '<?php endforeach; endif; ?>' +
                   '</select></td>' +
                   '<td><input type="text" name="reference_no[]" class="form-control" placeholder="Ref #"></td>' +
                   '<td><input type="text" name="remarks[]" class="form-control" placeholder="Remarks"></td>' +
                   '<td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removePaymentRow(this)">Remove</button></td>';
    tbody.appendChild(tr);
}

function removePaymentRow(btn) {
    var tr = btn.closest('tr');
    var tbody = tr.parentNode;
    if (tbody.children.length > 1) {
        tbody.removeChild(tr);
    } else {
        tr.querySelector('input[name="payment_amount[]"]').value = '';
        tr.querySelector('input[name="payment_date[]"]').value = '';
        tr.querySelector('select[name="payment_mode[]"]').selectedIndex = 0;
        tr.querySelector('input[name="reference_no[]"]').value = '';
        tr.querySelector('input[name="remarks[]"]').value = '';
    }
}
</script>

</body>
</html>
