<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Project</title>

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

            /* Make definition list more mobile-friendly */
            dl.row dt {
                font-weight: 600;
                margin-bottom: 0.5rem;
            }

            dl.row dd {
                margin-bottom: 1.5rem;
            }

            /* Stack buttons vertically on small screens */
            .btn-group-mobile {
                flex-direction: column !important;
                gap: 0.75rem;
                width: 100%;
            }

            .btn-group-mobile .btn {
                width: 100%;
                justify-content: center;
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
                        <h4 class="mb-0">Project Details</h4>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row mb-0">
                            <dt class="col-sm-3 col-12">ID</dt>
                            <dd class="col-sm-9 col-12"><?php echo htmlspecialchars($project['id']); ?></dd>
                            
                            <dt class="col-sm-3 col-12">Project Name</dt>
                            <dd class="col-sm-9 col-12"><?php echo htmlspecialchars($project['name']); ?></dd>
                            
                            <dt class="col-sm-3 col-12">Project Code</dt>
                            <dd class="col-sm-9 col-12"><?php echo htmlspecialchars($project['project_code']); ?></dd>
                            
                            <dt class="col-sm-3 col-12">Client Name</dt>
                            <dd class="col-sm-9 col-12"><?php echo htmlspecialchars($project['client']); ?></dd>
                            
                            <dt class="col-sm-3 col-12">Address</dt>
                            <dd class="col-sm-9 col-12"><?php echo nl2br(htmlspecialchars($project['address'])); ?></dd>
                            
                            <dt class="col-sm-3 col-12">Project Value</dt>
                            <dd class="col-sm-9 col-12"><?php echo htmlspecialchars(number_format((float)$project['paysheet_value'], 2)); ?></dd>
                            
                            <dt class="col-sm-3 col-12">Start Date</dt>
                            <dd class="col-sm-9 col-12"><?php echo htmlspecialchars($project['start_date']); ?></dd>
                            
                            <dt class="col-sm-3 col-12">Status</dt>
                            <dd class="col-sm-9 col-12"><?php echo htmlspecialchars($project['status']); ?></dd>
                        </dl>

                        <!-- Documents Section -->
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="bi bi-paperclip"></i> Project Documents</h5>
                        <?php if (!empty($documents)): ?>
                        <div class="row g-3">
                            <?php foreach ($documents as $doc): ?>
                                <div class="col-md-3 col-6">
                                    <div class="card h-100 p-2 text-center border-0 shadow-sm">
                                        <?php
                                            $file_path = base_url($doc['file_path']);
                                            $file_name = htmlspecialchars($doc['file_name']);
                                            $ext = strtolower(pathinfo($doc['file_name'], PATHINFO_EXTENSION));
                                        ?>
                                        <?php if (in_array($ext, ['jpg','jpeg','png','gif','bmp','webp'])): ?>
                                            <a href="<?php echo $file_path; ?>" target="_blank"><img src="<?php echo $file_path; ?>" alt="<?php echo $file_name; ?>" class="img-fluid rounded mb-2" style="max-height:120px;"></a>
                                        <?php elseif ($ext === 'pdf'): ?>
                                            <a href="<?php echo $file_path; ?>" target="_blank">
                                                <img src="https://cdn.jsdelivr.net/gh/edent/SuperTinyIcons/images/svg/pdf.svg" alt="PDF" style="height:60px;width:auto;" class="mb-2"><br>
                                                <span class="badge bg-danger">PDF</span>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo $file_path; ?>" target="_blank">
                                                <i class="bi bi-file-earmark-text" style="font-size:2.5rem;color:#6c757d;"></i><br>
                                                <span class="badge bg-secondary">DOC</span>
                                            </a>
                                        <?php endif; ?>
                                        <div class="mt-2 small text-truncate" title="<?php echo $file_name; ?>"><?php echo $file_name; ?></div>
                                        <div class="text-muted" style="font-size:0.85em;">Uploaded: <?php echo date('Y-m-d', strtotime($doc['uploaded_at'])); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                            <div class="alert alert-info">No documents uploaded for this project.</div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end mt-4 btn-group-mobile">
                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                <a href="<?php echo site_url('project/edit/' . $project['id']); ?>" class="btn btn-warning me-2">
                                    <i class="bi bi-pencil-square me-2"></i>Edit
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo site_url('project/list'); ?>" class="btn btn-outline-secondary">
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
