<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Project</title>
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
                        <h4 class="mb-0" style="color:#222;font-weight:600;">Project Details</h4>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row">
                            <dt class="col-sm-3">ID</dt><dd class="col-sm-9"><?php echo htmlspecialchars($project['id']); ?></dd>
                            <dt class="col-sm-3">Project Name</dt><dd class="col-sm-9"><?php echo htmlspecialchars($project['name']); ?></dd>
                            <dt class="col-sm-3">Project Code</dt><dd class="col-sm-9"><?php echo htmlspecialchars($project['project_code']); ?></dd>
                            <dt class="col-sm-3">Client Name</dt><dd class="col-sm-9"><?php echo htmlspecialchars($project['client']); ?></dd>
                            <dt class="col-sm-3">Address</dt><dd class="col-sm-9"><?php echo nl2br(htmlspecialchars($project['address'])); ?></dd>
                            <dt class="col-sm-3">Project Value</dt><dd class="col-sm-9"><?php echo htmlspecialchars(number_format((float)$project['paysheet_value'], 2)); ?></dd>
                            <dt class="col-sm-3">Start Date</dt><dd class="col-sm-9"><?php echo htmlspecialchars($project['start_date']); ?></dd>
                            <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><?php echo htmlspecialchars($project['status']); ?></dd>
                        </dl>
                        <div class="d-flex justify-content-end mt-4">
                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                <a href="<?php echo site_url('project/edit/' . $project['id']); ?>" class="btn btn-warning me-2"><i class="bi bi-pencil-square me-2"></i>Edit</a>
                            <?php endif; ?>
                            <a href="<?php echo site_url('project/list'); ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back to list</a>
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
