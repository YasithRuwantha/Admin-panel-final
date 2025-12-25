<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Quotation</title>

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

            /* Services table on mobile */
            .services-table th,
            .services-table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.95rem;
            }

            .services-table .text-end {
                white-space: nowrap;
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
                        <i class="bi bi-file-earmark-text" style="font-size:1.5rem;margin-right:10px;color:#6c757d;"></i>
                        <h4 class="mb-0">Quotation Details</h4>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row">
                            <dt class="col-sm-3 col-12">Name</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($quote['name']); ?></dd>
                            <dt class="col-sm-3 col-12">Quotation No</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($quote['quotation_no']); ?></dd>
                            <dt class="col-sm-3 col-12">Address</dt><dd class="col-sm-9 col-12"><?php echo nl2br(htmlspecialchars($quote['address'])); ?></dd>
                            <dt class="col-sm-3 col-12">Date</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($quote['quote_date']); ?></dd>
                            <dt class="col-sm-3 col-12">Project Code</dt><dd class="col-sm-9 col-12"><?php echo htmlspecialchars($quote['project_code']); ?></dd>
                        </dl>

                        <div class="mb-3">
                            <h5 class="fw-semibold">Services</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered services-table">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($quote['items'])): ?>
                                            <?php foreach ($quote['items'] as $item): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                                                    <td class="text-end"><?php echo htmlspecialchars(number_format((float)$item['amount'], 2)); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($quote['description']); ?></td>
                                                <td class="text-end"><?php echo htmlspecialchars(number_format((float)$quote['amount'], 2)); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-end"><?php echo htmlspecialchars(number_format((float)$quote['amount'], 2)); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 action-buttons">
                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                <a href="<?php echo site_url('quote/edit/' . $quote['id']); ?>" class="btn btn-warning me-2">
                                    <i class="bi bi-pencil-square me-2"></i>Edit
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo site_url('quote/pdf/' . $quote['id']); ?>" target="_blank" class="btn btn-danger me-2">
                                <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                            </a>
                            <a href="<?php echo site_url('quote/list'); ?>" class="btn btn-outline-secondary">
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
