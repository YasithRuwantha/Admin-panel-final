<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Invoice</title>

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

            /* Items list on mobile */
            .items-list li {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.25rem;
            }

            .items-list span:last-child {
                align-self: flex-end;
            }

            /* Payments blocks */
            .payment-block {
                font-size: 0.9rem;
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
                        <h4 class="mb-0">Invoice Details</h4>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row">
                            <dt class="col-sm-3 col-12">Name</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($invoice['name']); ?></dd>
                            <dt class="col-sm-3 col-12">Invoice No</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($invoice['invoice_no']); ?></dd>
                            <dt class="col-sm-3 col-12">Address</dt><dd class="col-sm-9 col-12"><?php echo nl2br(htmlspecialchars($invoice['address'])); ?></dd>
                            <dt class="col-sm-3 col-12">Date</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($invoice['invoice_date']); ?></dd>
                            <dt class="col-sm-3 col-12">Project Code</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($invoice['project_code']); ?></dd>
                            <dt class="col-sm-3 col-12">Project Name</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($invoice['project_name']); ?></dd>
                            <dt class="col-sm-3 col-12">Items</dt>
                            <dd class="col-sm-9 col-12">
                                <?php if (!empty($invoice['items'])): ?>
                                    <ul class="list-unstyled mb-0 items-list">
                                        <?php foreach ($invoice['items'] as $item): ?>
                                            <li class="d-flex justify-content-between border-bottom py-1">
                                                <span><?php echo htmlspecialchars($item['description']); ?></span>
                                                <span><?php echo htmlspecialchars(number_format((float)$item['amount'], 2)); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="text-muted">No items</span>
                                <?php endif; ?>
                            </dd>
                            <dt class="col-sm-3 col-12">Total</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars(number_format((float)$invoice['amount'], 2)); ?></dd>
                            <?php 
                                $total_paid = 0;
                                if (!empty($invoice['payments'])) {
                                    foreach ($invoice['payments'] as $pay) {
                                        $total_paid += (float)$pay['payment_amount'];
                                    }
                                }
                                $invoice_total = (float)$invoice['amount'];
                                if ($total_paid < $invoice_total) {
                                    $remaining = max(0, $invoice_total - $total_paid);
                                    echo '<dt class="col-sm-3 col-12">Remaining To Pay</dt><dd class="col-sm-9 col-12"><span class="fw-bold">' . htmlspecialchars(number_format($remaining, 2)) . '</span></dd>';
                                } elseif ($total_paid > $invoice_total) {
                                    $overpaid = max(0, $total_paid - $invoice_total);
                                    echo '<dt class="col-sm-3 col-12">Overpaid</dt><dd class="col-sm-9 col-12"><span class="fw-bold">' . htmlspecialchars(number_format($overpaid, 2)) . '</span></dd>';
                                } else {
                                    echo '<dt class="col-sm-3 col-12">Payment Status</dt><dd class="col-sm-9 col-12"><span class="badge bg-success">Paid</span></dd>';
                                }
                            ?>
                            <dt class="col-sm-3 col-12">Payments</dt>
                            <dd class="col-sm-9 col-12">
                                <?php if (!empty($invoice['payments'])): ?>
                                    <div class="mt-1">
                                        <?php foreach ($invoice['payments'] as $pay): ?>
                                            <div class="border rounded p-2 mb-2 bg-light payment-block">
                                                <small><b>Amount:</b> <?php echo htmlspecialchars(number_format((float)$pay['payment_amount'], 2)); ?></small><br>
                                                <small><b>Date:</b> <?php echo htmlspecialchars($pay['payment_date']); ?></small><br>
                                                <small><b>Mode:</b> <?php echo htmlspecialchars($pay['payment_mode']); ?></small><br>
                                                <?php if (!empty($pay['reference_no'])): ?><small><b>Ref:</b> <?php echo htmlspecialchars($pay['reference_no']); ?></small><br><?php endif; ?>
                                                <?php if (!empty($pay['remarks'])): ?><small><b>Remarks:</b> <?php echo htmlspecialchars($pay['remarks']); ?></small><?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">No payments</span>
                                <?php endif; ?>
                            </dd>
                        </dl>

                        <div class="d-flex justify-content-end mt-4 action-buttons">
                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                <a href="<?php echo site_url('invoice/edit/' . $invoice['id']); ?>" class="btn btn-primary me-2">
                                    <i class="bi bi-pencil-square me-2"></i>Edit
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo site_url('invoice/list'); ?>" class="btn btn-outline-secondary">
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
