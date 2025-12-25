<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Expense</title>

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

            /* Make definition list mobile-friendly: labels stack above values */
            dl.row dt {
                font-weight: 600;
                margin-bottom: 0.5rem;
            }

            dl.row dd {
                margin-bottom: 1.5rem;
            }

            /* Images in documents section */
            .document-images img {
                max-width: 100%;
                height: auto;
                margin: 8px 0;
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

            /* Card full width */
            .card {
                max-width: 100% !important;
            }
        }

        /* Card enhancements */
        .card {
            border-radius: 0.75rem;
        }

        .card-header {
            background: #fff !important;
        }

        .card-header h4 {
            color: #222;
            font-weight: 600;
        }

        /* Document images layout */
        .document-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .document-images a {
            display: block;
        }

        .document-images img {
            border: 1px solid #ddd;
            border-radius: 4px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    
    <div class="container-fluid main-container">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="min-height:500px;">
                    <div class="card-header d-flex align-items-center border-bottom">
                        <i class="bi bi-eye" style="font-size:1.5rem;margin-right:10px;color:#6c757d;"></i>
                        <h4 class="mb-0">Expense Details</h4>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row">
                            <dt class="col-sm-3 col-12">ID</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($expense['id']); ?></dd>
                            <dt class="col-sm-3 col-12">Project Name</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($expense['project_name']); ?></dd>
                            <dt class="col-sm-3 col-12">Project Code</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($expense['project_code']); ?></dd>
                            <dt class="col-sm-3 col-12">Expense Date</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($expense['expense_date']); ?></dd>
                            <dt class="col-sm-3 col-12">Category</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($expense['category']); ?></dd>
                            <dt class="col-sm-3 col-12">Description</dt><dd class="col-sm-9 col-12"><?php echo nl2br(htmlspecialchars($expense['description'])); ?></dd>
                            <dt class="col-sm-3 col-12">Paid To</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($expense['paid_to']); ?></dd>
                            <dt class="col-sm-3 col-12">Paid By</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($expense['paid_by']); ?></dd>
                            <dt class="col-sm-3 col-12">Amount</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars(number_format((float)$expense['amount'], 2)); ?></dd>
                            <dt class="col-sm-3 col-12">Payment Method</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($expense['payment_method']); ?></dd>
                            <dt class="col-sm-3 col-12">Status</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($expense['status']); ?></dd>
                            <dt class="col-sm-3 col-12">Remark</dt><dd class="col-sm-9 col-12"><?php echo nl2br(htmlspecialchars($expense['remark'])); ?></dd>
                            <dt class="col-sm-3 col-12">Documents</dt>
                            <dd class="col-sm-9 col-12">
                                <?php if (!empty($expense['document_path'])): ?>
                                    <?php 
                                        $docs = json_decode($expense['document_path'], true);
                                        if (is_array($docs)) {
                                            echo '<div class="document-images">';
                                            foreach ($docs as $doc) {
                                                $ext = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
                                                $url = base_url(htmlspecialchars($doc));
                                                if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                                                    echo '<a href="' . $url . '" target="_blank"><img src="' . $url . '" alt="Document" style="max-width:150px;max-height:150px;"></a>';
                                                } else {
                                                    echo '<div class="my-2"><a href="' . $url . '" target="_blank">' . basename($doc) . '</a></div>';
                                                }
                                            }
                                            echo '</div>';
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

                        <div class="d-flex justify-content-end mt-4 action-buttons">
                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                <a href="<?php echo site_url('expense/edit/' . $expense['id']); ?>" class="btn btn-primary me-2">
                                    <i class="bi bi-pencil-square me-2"></i>Edit
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo site_url('expense/list_expenses'); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to list
                            </a>
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
