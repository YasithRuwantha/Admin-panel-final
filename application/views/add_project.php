<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Project</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container" style="margin-left:220px; max-width:500px; padding-top:30px;">
        <h2 style="margin-bottom:12px;">Add Project</h2>
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success" style="margin-bottom:10px;"><?php echo $this->session->flashdata('success'); ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo site_url('project/add'); ?>">
            <div class="mb-2">
                <label for="name" class="form-label" style="margin-bottom:2px;">Project Name</label>
                <input type="text" class="form-control form-control-sm" id="name" name="name" required>
            </div>
            <div class="mb-2">
                <label for="project_code" class="form-label" style="margin-bottom:2px;">Project Code</label>
                <input type="text" class="form-control form-control-sm" id="project_code" name="project_code" required>
            </div>
            <div class="mb-2">
                <label for="client" class="form-label" style="margin-bottom:2px;">Client Name</label>
                <input type="text" class="form-control form-control-sm" id="client" name="client">
            </div>
            <div class="mb-2">
                <label for="address" class="form-label" style="margin-bottom:2px;">Address</label>
                <input type="text" class="form-control form-control-sm" id="address" name="address">
            </div>
            <div class="mb-2">
                <label for="paysheet_value" class="form-label" style="margin-bottom:2px;">Paysheet Value</label>
                <input type="text" class="form-control form-control-sm" id="paysheet_value" name="paysheet_value">
            </div>
            <div class="mb-2">
                <label for="start_date" class="form-label" style="margin-bottom:2px;">Project Start Date</label>
                <input type="date" class="form-control form-control-sm" id="start_date" name="start_date">
            </div>
            <div class="mb-2">
                <label for="status" class="form-label" style="margin-bottom:2px;">Status</label>
                <select class="form-control form-control-sm" id="status" name="status">
                    <option value="Planned">Planned</option>
                    <option value="Ongoing">Ongoing</option>
                    <option value="Completed">Completed</option>
                    <option value="On Hold">On Hold</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add Project</button>
        </form>
    </div>
</div>
</body>
</html>
