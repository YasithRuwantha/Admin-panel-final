<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Project</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                                <label for="project_type" class="form-label">Project Type</label>
                                <?php $selected_types = isset($project['project_type']) ? explode(',', $project['project_type']) : []; ?>
                                <select class="form-select" id="project_type" name="project_type[]" multiple="multiple">
                                    <?php if (!empty($project_types)): ?>
                                        <?php foreach ($project_types as $type): ?>
                                            <option value="<?php echo htmlspecialchars($type['config_value']); ?>" <?php if(in_array($type['config_value'], $selected_types)) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($type['config_value']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Project Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($project['start_date']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="quotation_id" class="form-label">
                                    Linked Quotation <span class="text-muted small">(Optional)</span>
                                </label>
                                <select class="form-select" id="quotation_id" name="quotation_id">
                                    <option value="">-- None --</option>
                                    <?php if (!empty($quotes)): ?>
                                        <?php foreach ($quotes as $q): ?>
                                            <option value="<?php echo $q['id']; ?>"
                                                <?php if (!empty($project['quotation_id']) && $project['quotation_id'] == $q['id']) echo 'selected'; ?>>
                                                #<?php echo htmlspecialchars($q['quotation_no'] ?? $q['id']); ?>
                                                — <?php echo htmlspecialchars($q['name'] ?? ''); ?>
                                                (<?php echo number_format((float)$q['amount'], 2); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text">Select a quotation that this project is based on.</div>
                            </div>
                            <!-- Referred By -->
                            <div class="row mb-3">
                                <div class="col">
                                    <label>Referred By</label>
                                    <div class="input-group">
                                        <select name="referred_by" id="referred_by_select" class="form-select">
                                            <option value="">Select</option>
                                            <?php if (!empty($referred_by_options)) : ?>
                                                <?php foreach ($referred_by_options as $opt): ?>
                                                    <option value="<?php echo htmlspecialchars($opt['config_value']); ?>" <?php if (!empty($project['referred_by']) && $project['referred_by'] === $opt['config_value']) echo 'selected'; ?>><?php echo htmlspecialchars($opt['config_value']); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" id="addReferredByBtn" title="Add New"><i class="bi bi-plus"></i></button>
                                    </div>
                                </div>
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

<!-- Modal: Add Referred By -->
<div class="modal fade" id="addReferredByModal" tabindex="-1" aria-labelledby="addReferredByModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addReferredByModalLabel">Add Referred By</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="newReferredByName" class="form-label">Name</label>
                    <input type="text" class="form-control" id="newReferredByName" placeholder="Enter name">
                </div>
                <div id="referredByModalAlert" class="alert alert-danger d-none" role="alert">Please enter a name.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveReferredByBtn">Add</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#project_type').select2({
        placeholder: "Select Project Type(s)",
        allowClear: true
    });
    $('#quotation_id').select2({
        placeholder: "-- None (Optional) --",
        allowClear: true
    });
});

// Referred By modal logic
let addReferredByModal = new bootstrap.Modal(document.getElementById('addReferredByModal'));
let addReferredByBtn   = document.getElementById('addReferredByBtn');
let saveReferredByBtn  = document.getElementById('saveReferredByBtn');
let newReferredByName  = document.getElementById('newReferredByName');
let referredByAlert    = document.getElementById('referredByModalAlert');

addReferredByBtn.addEventListener('click', function() {
    newReferredByName.value = '';
    referredByAlert.classList.add('d-none');
    addReferredByModal.show();
});

saveReferredByBtn.addEventListener('click', function() {
    let name = newReferredByName.value.trim();
    if (!name) {
        referredByAlert.textContent = 'Please enter a name.';
        referredByAlert.classList.remove('d-none');
        newReferredByName.focus();
        return;
    }
    saveReferredByBtn.disabled = true;
    fetch('<?php echo base_url("index.php/project/add_referred_by_config"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'name=' + encodeURIComponent(name)
    })
    .then(r => r.json())
    .then(data => {
        saveReferredByBtn.disabled = false;
        if (data.success) {
            let option = document.createElement('option');
            option.value = name;
            option.textContent = name;
            option.selected = true;
            document.getElementById('referred_by_select').appendChild(option);
            addReferredByModal.hide();
        } else {
            referredByAlert.textContent = data.message || 'Failed to add.';
            referredByAlert.classList.remove('d-none');
        }
    })
    .catch(() => {
        saveReferredByBtn.disabled = false;
        referredByAlert.textContent = 'Server error. Please try again.';
        referredByAlert.classList.remove('d-none');
    });
});

document.getElementById('addReferredByModal').addEventListener('shown.bs.modal', function () {
    newReferredByName.focus();
});
</script>

</body>
</html>
