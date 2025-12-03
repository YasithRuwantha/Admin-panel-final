<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Expense</title>
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
                        <i class="bi bi-cash-coin" style="font-size:1.5rem;margin-right:10px;color:#0d6efd;"></i>
                        <h4 class="mb-0" style="color:#222;font-weight:600;">Add Expense</h4>
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
                    </script>
                    <script>
                    function setProjectCode() {
                        var select = document.getElementById('project_name');
                        var selected = select.options[select.selectedIndex];
                        var code = selected.getAttribute('data-code') || '';
                        document.getElementById('project_code').value = code;
                    }
                    </script>
                    <div class="row mb-3">
                        <div class="col">
                            <label>Date</label>
                            <input type="date" name="expense_date" class="form-control" required>
                        </div>
                        <div class="col">
                            <label>Expenses Category</label>
                            <select name="category" class="form-control" required>
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
                            <select name="paid_to" class="form-control" required>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['username']; ?>"><?php echo $user['username']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col">
                            <label>Paid By</label>
                            <select name="paid_by" class="form-control" required>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['username']; ?>"><?php echo $user['username']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label>Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="col">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control" required>
                                <?php foreach ($payment_methods as $pm): ?>
                                    <option value="<?php echo $pm['config_key']; ?>"><?php echo $pm['config_value']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Paid">Paid</option>
                                <option value="Cancelled">Cancelled</option>
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
                            <input type="file" name="document_path[]" class="form-control mb-2" onchange="addFileInput(this)">
                        </div>
                        <small class="text-muted">Choose a file, confirm, and add another. Each input allows one file only.</small>
                        <script>
                        function addFileInput(input) {
                            if (input.value) {
                                var container = document.getElementById('file-input-list');
                                if (container.lastElementChild === input) {
                                    var newInput = document.createElement('input');
                                    newInput.type = 'file';
                                    newInput.name = 'document_path[]';
                                    newInput.className = 'form-control mb-2';
                                    newInput.onchange = function() { addFileInput(newInput); };
                                    container.appendChild(newInput);
                                }
                            }
                        }
                        </script>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</html>
