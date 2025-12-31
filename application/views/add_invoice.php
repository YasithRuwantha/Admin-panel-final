<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Invoice</title>

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

            /* Stack form columns vertically on mobile */
            .row.mb-3 > div {
                margin-bottom: 1rem;
            }

            /* Make inputs and selects full width */
            .form-control, .form-select {
                width: 100%;
            }

            /* Improve table on mobile */
            #services-table {
                font-size: 0.9rem;
            }

            #services-table th,
            #services-table td {
                padding: 0.75rem 0.5rem;
            }

            /* Make remove button smaller and centered */
            .remove-row {
                width: 38px;
                height: 38px;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Stack action buttons vertically on mobile */
            .action-buttons {
                flex-direction: column !important;
                gap: 0.75rem;
                width: 100%;
            }

            .action-buttons .btn {
                width: 100%;
                justify-content: center;
            }

            /* Card max-width adjustment */
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

        /* Services table enhancements */
        #services-table {
            background: #f8f9fa;
            border: 2px solid #000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        #services-table thead tr {
            background: #f8f9fa;
        }

        #add-row {
            background: #0d6dfc;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    
    <div class="container-fluid main-container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 col-md-11 col-12">
                <div class="card shadow-sm border-0" style="min-height:730px;">
                    <div class="card-header d-flex align-items-center border-bottom">
                        <i class="bi bi-file-earmark-plus" style="font-size:1.5rem;margin-right:10px;color:#0d6efd;"></i>
                        <h4 class="mb-0">Add New Invoice</h4>
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
                                    <label class="form-label">Project Name</label>
                                    <select name="project_name" id="project_name" class="form-select" required onchange="setProjectCode()">
                                        <option value="">Select Project</option>
                                        <?php if (!empty($projects)): ?>
                                            <?php foreach ($projects as $project): ?>
                                                <option value="<?php echo htmlspecialchars($project['name']); ?>" 
                                                    data-code="<?php echo htmlspecialchars($project['project_code']); ?>"
                                                    data-client="<?php echo htmlspecialchars($project['client'] ?? ''); ?>"
                                                    data-address="<?php echo htmlspecialchars($project['address'] ?? ''); ?>">
                                                    <?php echo htmlspecialchars($project['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Project Code</label>
                                    <input type="text" name="project_code" id="project_code" class="form-control" required readonly placeholder="Auto-filled">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name of the Client</label>
                                    <input type="text" name="name" class="form-control" required placeholder="Auto-filled" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control" required placeholder="Auto-filled" readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Invoice No</label>
                                    <input type="text" name="invoice_no" class="form-control" required placeholder="Enter invoice number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="invoice_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <table class="table table-bordered" id="services-table">
                                    <thead>
                                        <tr>
                                            <th>Service Description</th>
                                            <th>Amount</th>
                                            <th style="width:60px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select name="description_select[]" class="form-select service-desc-dropdown" onchange="handleServiceDescChange(this)">
                                                    <option value="">Select from list...</option>
                                                    <?php 
                                                    if (isset($service_descriptions) && is_array($service_descriptions)) {
                                                        foreach ($service_descriptions as $desc) {
                                                            echo '<option value="'.htmlspecialchars($desc['config_value']).'">'.htmlspecialchars($desc['config_value']).'</option>';
                                                        }
                                                    }
                                                    ?>
                                                    <!-- <option value="__custom__">Other (Type below)</option> -->
                                                </select>
                                                <input type="text" name="description[]" class="form-control mt-2 service-desc-input" placeholder="Type service description" style="display:block;" />
                                            </td>
                                            <td><input type="text" step="0.01" name="amount[]" class="form-control amount-input" required placeholder="0.00"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3">
                                                <button type="button" class="btn btn-sm" id="add-row">
                                                    <i class="bi bi-plus-circle me-2"></i>Add Service
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold;">Total</td>
                                            <td><input type="text" step="0.01" name="total" class="form-control" readonly value="0" id="total"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end action-buttons">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="bi bi-save me-2"></i>Save
                                </button>
                                <!-- <button type="button" class="btn btn-success ms-2 px-4 py-2" onclick="showPaymentModalForAddInvoice()">
                                    <i class="bi bi-cash-coin me-2"></i>Receive Payment
                                </button> -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
function setProjectCode() {
    var select = document.getElementById('project_name');
    var selected = select.options[select.selectedIndex];
    var code = selected.getAttribute('data-code') || '';
    var client = selected.getAttribute('data-client') || '';
    var address = selected.getAttribute('data-address') || '';
    document.getElementById('project_code').value = code;
    document.querySelector('input[name="name"]').value = client;
    document.querySelector('input[name="address"]').value = address;
}

function showPaymentModalForAddInvoice() {
    var invoiceNo = document.querySelector('input[name="invoice_no"]').value;
    document.getElementById('modal_invoice_id_add').value = '';
    document.getElementById('modal_invoice_no_add').value = invoiceNo;
    var modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

// Thousand separator formatting
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

function attachAmountInputEvents(input) {
    input.addEventListener('input', function() {
        let value = removeCommas(this.value);
        if (value && !isNaN(value)) {
            this.value = formatWithCommas(value);
        } else {
            this.value = '';
        }
    });
    input.addEventListener('focus', function() {
        this.value = removeCommas(this.value);
    });
    input.addEventListener('blur', function() {
        let value = removeCommas(this.value);
        if (value && !isNaN(value)) {
            this.value = formatWithCommas(value);
        } else {
            this.value = '';
        }
    });
}

// Attach events to existing inputs
document.querySelectorAll('.amount-input').forEach(attachAmountInputEvents);

// Dynamic row addition and total calculation
function updateTotal() {
    let total = 0;
    document.querySelectorAll('.amount-input').forEach(function(input) {
        let val = parseFloat(removeCommas(input.value));
        if (!isNaN(val)) total += val;
    });
    document.getElementById('total').value = formatWithCommas(total.toFixed(2));
}

document.getElementById('add-row').addEventListener('click', function() {
    let tbody = document.querySelector('#services-table tbody');
    let row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="description_select[]" class="form-select service-desc-dropdown" onchange="handleServiceDescChange(this)">
                <option value="">Select from list...</option>
                <?php 
                if (isset($service_descriptions) && is_array($service_descriptions)) {
                    foreach ($service_descriptions as $desc) {
                        echo '<option value="'.htmlspecialchars($desc['config_value']).'">'.htmlspecialchars($desc['config_value']).'</option>';
                    }
                }
                ?>
            </select>
            <input type="text" name="description[]" class="form-control mt-2 service-desc-input" placeholder="Type service description" style="display:block;" />
        </td>
        <td><input type="text" step="0.01" name="amount[]" class="form-control amount-input" required placeholder="0.00"></td>
        <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(row);

    // Attach formatting to new amount input
    setTimeout(() => {
        let newInput = row.querySelector('.amount-input');
        if (newInput && !newInput.hasAttribute('data-thousand-sep')) {
            attachAmountInputEvents(newInput);
            newInput.setAttribute('data-thousand-sep', '1');
        }
    }, 100);
});
// Service description dropdown logic
function handleServiceDescChange(select) {
    var input = select.parentElement.querySelector('.service-desc-input');
    if (select.value === '__custom__') {
        input.style.display = '';
        input.required = true;
        input.value = '';
        input.focus();
        select.value = '';
    } else if (select.value) {
        input.style.display = '';
        input.required = false;
        input.value = select.value;
        select.value = '';
    } else {
        input.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}

document.querySelector('#services-table').addEventListener('input', function(e) {
    if (e.target.classList.contains('amount-input')) {
        updateTotal();
    }
});

document.querySelector('#services-table').addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        let row = e.target.closest('tr');
        row.remove();
        updateTotal();
    }
});

// Clean commas on form submit
document.querySelector('form').addEventListener('submit', function(e) {
    document.querySelectorAll('.amount-input').forEach(function(input) {
        input.value = removeCommas(input.value);
    });
    let totalField = document.getElementById('total');
    if (totalField) {
        totalField.value = removeCommas(totalField.value);
    }
});

updateTotal();
</script>

</body>
</html>
