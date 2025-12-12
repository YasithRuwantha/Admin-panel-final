<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Expense</title>
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
                        <i class="bi bi-eye" style="font-size:1.5rem;margin-right:10px;color:#6c757d;"></i>
                        <h4 class="mb-0" style="color:#222;font-weight:600;">Expense Details</h4>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row">
                            <dt class="col-sm-3">ID</dt><dd class="col-sm-9"><?php echo htmlspecialchars($expense['id']); ?></dd>
                            <dt class="col-sm-3">Project Name</dt><dd class="col-sm-9"><?php echo htmlspecialchars($expense['project_name']); ?></dd>
                            <dt class="col-sm-3">Project Code</dt><dd class="col-sm-9"><?php echo htmlspecialchars($expense['project_code']); ?></dd>
                            <dt class="col-sm-3">Expense Date</dt><dd class="col-sm-9"><?php echo htmlspecialchars($expense['expense_date']); ?></dd>
                            <dt class="col-sm-3">Category</dt><dd class="col-sm-9"><?php echo htmlspecialchars($expense['category']); ?></dd>
                            <dt class="col-sm-3">Description</dt><dd class="col-sm-9"><?php echo nl2br(htmlspecialchars($expense['description'])); ?></dd>
                            <dt class="col-sm-3">Paid To</dt><dd class="col-sm-9"><?php echo htmlspecialchars($expense['paid_to']); ?></dd>
                            <dt class="col-sm-3">Paid By</dt><dd class="col-sm-9"><?php echo htmlspecialchars($expense['paid_by']); ?></dd>
                            <dt class="col-sm-3">Amount</dt><dd class="col-sm-9"><?php echo htmlspecialchars(number_format((float)$expense['amount'], 2)); ?></dd>
                            <dt class="col-sm-3">Payment Method</dt><dd class="col-sm-9"><?php echo htmlspecialchars($expense['payment_method']); ?></dd>
                            <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><?php echo htmlspecialchars($expense['status']); ?></dd>
                            <dt class="col-sm-3">Remark</dt><dd class="col-sm-9"><?php echo nl2br(htmlspecialchars($expense['remark'])); ?></dd>
                            <dt class="col-sm-3">Documents</dt>
                            <dd class="col-sm-9">
                                <?php if (!empty($expense['document_path'])): ?>
                                    <?php 
                                        $docs = json_decode($expense['document_path'], true);
                                        if (is_array($docs)) {
                                            foreach ($docs as $doc) {
                                                $ext = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
                                                $url = base_url(htmlspecialchars($doc));
                                                if (in_array($ext, ['jpg','jpeg','png'])) {
                                                    echo '<a href="' . $url . '" target="_blank"><img src="' . $url . '" style="max-width:120px;max-height:120px;margin:5px;border:1px solid #ddd;border-radius:4px;" alt="Document"></a>';
                                                } else {
                                                    echo '<div><a href="' . $url . '" target="_blank">' . basename($doc) . '</a></div>';
                                                }
                                            }
                                        } else {
                                            $url = base_url(htmlspecialchars($expense['document_path']));
                                            echo '<a href="' . $url . '" target="_blank">View document</a>';
                                        }
                                    ?>
                                <?php else: ?>
                                    <span class="text-muted">No documents</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                        <div class="d-flex justify-content-end mt-4">
                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                <a href="<?php echo site_url('expense/edit/' . $expense['id']); ?>" class="btn btn-primary me-2"><i class="bi bi-pencil-square me-2"></i>Edit</a>
                            <?php endif; ?>
                            <a href="<?php echo site_url('expense/list_expenses'); ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back to list</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
