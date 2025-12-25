<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Project</title>
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

            .btn {
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
                        <i class="bi bi-folder-plus" style="font-size:1.5rem;margin-right:10px;color:#0d6efd;"></i>
                        <h4 class="mb-0" style="color:#222;font-weight:600;">Add New Project</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if($this->session->flashdata('success')): ?>
                            <div class="alert alert-success mb-3"><?php echo $this->session->flashdata('success'); ?></div>
                        <?php endif; ?>
                        <?php if($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger mb-3"><?php echo $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>
                        <form method="post" action="<?php echo site_url('project/add'); ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Project Name</label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="Enter project name">
                            </div>
                            <div class="mb-3">
                                <label for="project_code" class="form-label">Project Code</label>
                                <input type="text" class="form-control" id="project_code" name="project_code" required placeholder="Enter project code">
                            </div>
                            <div class="mb-3">
                                <label for="client" class="form-label">Client Name</label>
                                <input type="text" class="form-control" id="client" name="client" placeholder="Enter client name">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Enter address">
                            </div>
                            <div class="mb-3">
                                <label for="paysheet_value" class="form-label">Project Value</label>
                                <input type="text" class="form-control" id="paysheet_value" name="paysheet_value" placeholder="Enter project value">
                            </div>
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Project Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="Planned">Planned</option>
                                    <option value="Ongoing">Ongoing</option>
                                    <option value="Completed">Completed</option>
                                    <option value="On Hold">On Hold</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4 py-2"><i class="bi bi-plus-circle me-2"></i>Add Project</button>
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

<script>
const paysheetInput = document.getElementById('paysheet_value');
if (paysheetInput) {
    let lastRawValue = '';
    paysheetInput.addEventListener('input', function(e) {
        let value = this.value.replace(/,/g, '');
        lastRawValue = value;
        if (!isNaN(value) && value.length > 0) {
            let parts = value.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            this.value = parts.join('.');
        } else {
            this.value = '';
        }
    });
    paysheetInput.addEventListener('focus', function() {
        if (this.value) {
            this.value = this.value.replace(/,/g, '');
        }
    });
    paysheetInput.addEventListener('blur', function() {
        let value = this.value.replace(/,/g, '');
        if (!isNaN(value) && value.length > 0) {
            let parts = value.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            this.value = parts.join('.');
        } else {
            this.value = '';
        }
    });
    let parentForm = paysheetInput.form;
    if (parentForm) {
        parentForm.addEventListener('submit', function(e) {
            if (paysheetInput.value) {
                paysheetInput.value = paysheetInput.value.replace(/,/g, '');
            }
        });
    }
}
</script>

</body>
</html>
