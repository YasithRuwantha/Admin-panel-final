<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Expense</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container-fluid" style="margin-left:220px; padding-top:40px;">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 col-md-11">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex align-items-center border-bottom" style="background:#fff;">
                        <i class="bi bi-pencil-square" style="font-size:1.5rem;margin-right:10px;color:#0d6efd;"></i>
                        <h4 class="mb-0" style="color:#222;font-weight:600;">Edit Expense</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if($this->session->flashdata('success')): ?>
                            <div class="alert alert-success mb-3"><?php echo $this->session->flashdata('success'); ?></div>
                        <?php endif; ?>
                        <?php if($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger mb-3"><?php echo $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>
                        <form method="post" action="<?php echo site_url('expense/edit/' . $expense['id']); ?>">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Project Name</label>
                                    <input type="text" name="project_name" class="form-control" value="<?php echo htmlspecialchars($expense['project_name']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Project Code</label>
                                    <input type="text" name="project_code" class="form-control" value="<?php echo htmlspecialchars($expense['project_code']); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Expense Date</label>
                                    <input type="date" name="expense_date" class="form-control" value="<?php echo htmlspecialchars($expense['expense_date']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <?php if (!empty($categories)): ?>
                                            <?php foreach ($categories as $cat): ?>
                                                <?php $val = isset($cat['config_value']) ? $cat['config_value'] : (isset($cat['value']) ? $cat['value'] : ''); ?>
                                                <option value="<?php echo htmlspecialchars($val); ?>" <?php echo ($expense['category'] === $val) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($val); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="<?php echo htmlspecialchars($expense['category']); ?>" selected><?php echo htmlspecialchars($expense['category']); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($expense['description']); ?></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Paid To</label>
                                    <input type="text" name="paid_to" class="form-control" value="<?php echo htmlspecialchars($expense['paid_to']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Paid By</label>
                                    <input type="text" name="paid_by" class="form-control" value="<?php echo htmlspecialchars($expense['paid_by']); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Amount</label>
                                    <input type="number" step="0.01" name="amount" class="form-control" value="<?php echo htmlspecialchars($expense['amount']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Payment Method</label>
                                    <select name="payment_method" class="form-select">
                                        <?php if (!empty($payment_methods)): ?>
                                            <?php foreach ($payment_methods as $pm): ?>
                                                <?php $val = isset($pm['config_value']) ? $pm['config_value'] : (isset($pm['value']) ? $pm['value'] : ''); ?>
                                                <option value="<?php echo htmlspecialchars($val); ?>" <?php echo ($expense['payment_method'] === $val) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($val); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="<?php echo htmlspecialchars($expense['payment_method']); ?>" selected><?php echo htmlspecialchars($expense['payment_method']); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <input type="text" name="status" class="form-control" value="<?php echo htmlspecialchars($expense['status']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Remark</label>
                                    <input type="text" name="remark" class="form-control" value="<?php echo htmlspecialchars($expense['remark']); ?>">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal"><i class="bi bi-save me-2"></i>Update</button>
                                <a href="<?php echo site_url('expense/list_expenses'); ?>" class="btn btn-outline-secondary ms-2"><i class="bi bi-arrow-left me-2"></i>Cancel</a>
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
                                            Are you sure you want to update this expense?
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
</body>
</html>
