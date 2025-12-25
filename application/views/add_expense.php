<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Expense</title>

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

            /* Stack all form rows vertically on mobile */
            .row.mb-3 > .col,
            .row.mb-3 > .col-md-6 {
                margin-bottom: 1rem;
            }

            /* Full width inputs */
            .form-control,
            .form-select {
                width: 100%;
            }

            /* Input groups (select + button) */
            .input-group .btn {
                flex-shrink: 0;
            }

            /* File upload section */
            .file-input-row .input-group {
                flex-direction: column;
            }

            .file-input-row .remove-file-btn {
                align-self: flex-end;
                margin-top: 0.5rem;
            }

            /* Save button full width */
            .save-button {
                width: 100%;
                justify-content: center;
            }

            /* Card full width */
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
                <div class="card shadow-sm border-0" style="min-height:730px;">
                    <div class="card-header d-flex align-items-center border-bottom">
                        <i class="bi bi-cash-coin" style="font-size:1.5rem;margin-right:10px;color:#0d6efd;"></i>
                        <h4 class="mb-0">Add Expense</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($this->session->flashdata('success')): ?>
                            <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Project Name</label>
                                    <select name="project_name" id="project_name" class="form-select" required onchange="setProjectCode()">
                                        <option value="">Select Project</option>
                                        <?php if (!empty($projects)): ?>
                                            <?php foreach ($projects as $project): ?>
                                                <option value="<?php echo htmlspecialchars($project['name']); ?>" data-code="<?php echo htmlspecialchars($project['project_code']); ?>">
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
                                <div class="col">
                                    <label>Date</label>
                                    <input type="date" name="expense_date" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label>Expenses Category</label>
                                    <select name="category" class="form-select" required>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['config_key']; ?>"><?php echo $cat['config_value']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Description</label>
                                <input type="text" name="description" class="form-control" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <label>Paid To</label>
                                    <div class="input-group">
                                        <select name="paid_to" id="paid_to_select" class="form-select" required>
                                            <option value="">Select</option>
                                            <?php if (!empty($paid_to_options)) : ?>
                                                <?php foreach ($paid_to_options as $opt): ?>
                                                    <option value="<?php echo htmlspecialchars($opt['config_key']); ?>"><?php echo htmlspecialchars($opt['config_value']); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" id="addPaidToBtn" title="Add User"><i class="bi bi-plus"></i></button>
                                    </div>
                                </div>
                                <div class="col">
                                    <label>Paid By</label>
                                    <div class="input-group">
                                        <select name="paid_by" id="paid_by_select" class="form-select" required>
                                            <option value="">Select</option>
                                            <?php if (!empty($paid_by_options)) : ?>
                                                <?php foreach ($paid_by_options as $opt): ?>
                                                    <option value="<?php echo htmlspecialchars($opt['config_key']); ?>"><?php echo htmlspecialchars($opt['config_value']); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" id="addPaidByBtn" title="Add User"><i class="bi bi-plus"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <label>Amount</label>
                                    <input type="text" step="0.01" name="amount" class="form-control amount-input" required>
                                </div>
                                <div class="col">
                                    <label>Payment Method</label>
                                    <select name="payment_method" class="form-select" required>
                                        <?php foreach ($payment_methods as $pm): ?>
                                            <option value="<?php echo $pm['config_key']; ?>"><?php echo $pm['config_value']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <label>Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="">Select</option>
                                        <?php if (!empty($status_options)) : ?>
                                            <?php foreach ($status_options as $st): ?>
                                                <option value="<?php echo htmlspecialchars($st['config_key']); ?>">
                                                    <?php echo htmlspecialchars($st['config_value']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Remark</label>
                                <input type="text" name="remark" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label><strong>Support Document(s)</strong></label>
                                <div id="file-input-list">
                                    <div class="input-group mb-2 file-input-row">
                                        <input type="file" name="document_path[]" class="form-control" multiple>
                                        <button type="button" class="btn btn-outline-danger remove-file-btn" style="display:none;">&times;</button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm mb-2" id="add-file-btn"><i class="bi bi-plus-circle"></i> Add Another File</button>
                                <small class="text-muted d-block">You can add multiple files. Click 'Add Another File' for more.</small>
                            </div>

                            <button type="submit" class="btn btn-primary save-button"><i class="bi bi-save"></i> Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for adding user -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="newUserName" class="form-label">User Name</label>
                    <input type="text" class="form-control" id="newUserName" placeholder="Enter user name">
                </div>
                <div id="userModalAlert" class="alert alert-danger d-none" role="alert">Please enter a user name.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveUserBtn">Add</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function setProjectCode() {
    var select = document.getElementById('project_name');
    var selected = select.options[select.selectedIndex];
    var code = selected.getAttribute('data-code') || '';
    document.getElementById('project_code').value = code;
}

// Thousand separator for Amount field
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
var amountInput = document.querySelector('.amount-input');
if (amountInput) {
    amountInput.addEventListener('input', function() {
        let value = removeCommas(this.value);
        if (value && !isNaN(value)) {
            this.value = formatWithCommas(value);
        } else {
            this.value = '';
        }
    });
    amountInput.addEventListener('focus', function() {
        this.value = removeCommas(this.value);
    });
    amountInput.addEventListener('blur', function() {
        let value = removeCommas(this.value);
        if (value && !isNaN(value)) {
            this.value = formatWithCommas(value);
        } else {
            this.value = '';
        }
    });
    // Remove commas before form submit
    amountInput.form.addEventListener('submit', function() {
        if (amountInput.value) {
            amountInput.value = removeCommas(amountInput.value);
        }
    });
}

// Add new file input row
document.getElementById('add-file-btn').addEventListener('click', function() {
    var container = document.getElementById('file-input-list');
    var row = document.createElement('div');
    row.className = 'input-group mb-2 file-input-row';
    row.innerHTML = '<input type="file" name="document_path[]" class="form-control" multiple>' +
                    '<button type="button" class="btn btn-outline-danger remove-file-btn">&times;</button>';
    container.appendChild(row);
    updateRemoveButtons();
});

// Remove file input row
document.getElementById('file-input-list').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-file-btn')) {
        var row = e.target.closest('.file-input-row');
        if (row) {
            row.remove();
            updateRemoveButtons();
        }
    }
});

// Show remove button only when more than one row
function updateRemoveButtons() {
    var rows = document.querySelectorAll('#file-input-list .file-input-row');
    rows.forEach(function(row, idx) {
        var btn = row.querySelector('.remove-file-btn');
        btn.style.display = (rows.length > 1) ? '' : 'none';
    });
}
updateRemoveButtons();

// Modal logic for adding user to Paid To / Paid By
let addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
let addPaidToBtn = document.getElementById('addPaidToBtn');
let addPaidByBtn = document.getElementById('addPaidByBtn');
let saveUserBtn = document.getElementById('saveUserBtn');
let newUserName = document.getElementById('newUserName');
let userModalAlert = document.getElementById('userModalAlert');
let currentTargetSelect = null;
let currentType = null;

addPaidToBtn.addEventListener('click', function() {
    currentTargetSelect = document.getElementById('paid_to_select');
    currentType = 'paid_to';
    newUserName.value = '';
    userModalAlert.classList.add('d-none');
    addUserModal.show();
});
addPaidByBtn.addEventListener('click', function() {
    currentTargetSelect = document.getElementById('paid_by_select');
    currentType = 'paid_by';
    newUserName.value = '';
    userModalAlert.classList.add('d-none');
    addUserModal.show();
});

saveUserBtn.addEventListener('click', function() {
    let name = newUserName.value.trim();
    if (!name) {
        userModalAlert.classList.remove('d-none');
        newUserName.focus();
        return;
    }
    // AJAX to add user to config table
    saveUserBtn.disabled = true;
    fetch('<?php echo base_url("index.php/expense/add_paid_user_config"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'user=' + encodeURIComponent(name) + '&type=' + encodeURIComponent(currentType)
    })
    .then(response => response.json())
    .then(data => {
        saveUserBtn.disabled = false;
        if (data.success) {
            let option = document.createElement('option');
            option.value = name;
            option.textContent = name;
            option.selected = true;
            currentTargetSelect.appendChild(option);
            addUserModal.hide();
        } else {
            userModalAlert.textContent = data.message || 'Failed to add user.';
            userModalAlert.classList.remove('d-none');
        }
    })
    .catch(() => {
        saveUserBtn.disabled = false;
        userModalAlert.textContent = 'Failed to add user (server error).';
        userModalAlert.classList.remove('d-none');
    });
});

// Focus input when modal shown
document.getElementById('addUserModal').addEventListener('shown.bs.modal', function () {
    newUserName.focus();
});
</script>

</body>
</html>
