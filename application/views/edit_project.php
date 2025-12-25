<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Project</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
            overflow-x: hidden;
        }

        /* Desktop: sidebar takes space */
        .main-content {
            margin-left: 220px;
            padding-top: 40px;
            transition: margin-left 0.3s ease;
        }

        /* Mobile: full width, no left margin */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
                padding: 20px 15px;
            }

            .card {
                min-height: auto !important;
            }

            .card-body {
                padding: 1.5rem !important;
            }

            /* Make update button full-width on mobile for easier tap */
            .update-btn-mobile {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container-fluid main-content">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 col-md-11 col-12">
                <div class="card shadow-sm border-0" style="background:#fff; min-height:730px;">
                    <div class="card-header d-flex align-items-center border-bottom" style="border-radius:0.5rem 0.5rem 0 0; background:#fff;">
                        <i class="bi bi-pencil-square" style="font-size:1.5rem;margin-right:10px;color:#ffc107;"></i>
                        <h4 class="mb-0" style="color:#222;font-weight:600;">Edit Project</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if($this->session->flashdata('success')): ?>
                            <div class="alert alert-success mb-3"><?php echo $this->session->flashdata('success'); ?></div>
                        <?php endif; ?>
                        <?php if($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger mb-3"><?php echo $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>
                        <form id="editProjectForm" method="post" action="<?php echo site_url('project/edit/' . $project['id']); ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Project Name</label>
                                <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($project['name']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="project_code" class="form-label">Project Code</label>
                                <input type="text" class="form-control" id="project_code" name="project_code" required value="<?php echo htmlspecialchars($project['project_code']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="client" class="form-label">Client Name</label>
                                <input type="text" class="form-control" id="client" name="client" value="<?php echo htmlspecialchars($project['client']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($project['address']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="paysheet_value" class="form-label">Project Value</label>
                                <input type="number" step="0.01" class="form-control" id="paysheet_value" name="paysheet_value" value="<?php echo htmlspecialchars($project['paysheet_value']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Project Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($project['start_date']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="Planned" <?php if($project['status']=='Planned') echo 'selected'; ?>>Planned</option>
                                    <option value="Ongoing" <?php if($project['status']=='Ongoing') echo 'selected'; ?>>Ongoing</option>
                                    <option value="Completed" <?php if($project['status']=='Completed') echo 'selected'; ?>>Completed</option>
                                    <option value="On Hold" <?php if($project['status']=='On Hold') echo 'selected'; ?>>On Hold</option>
                                    <option value="Cancelled" <?php if($project['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn px-4 py-2 update-btn-mobile" style="background:#ffc107;color:#222;" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal">
                                    <i class="bi bi-pencil-square me-2"></i>Update Project
                                </button>
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
                                            Are you sure you want to update this project?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn" style="background:#ffc107;color:#222;" onclick="document.getElementById('editProjectForm').submit();">Yes, Update</button>
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

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
