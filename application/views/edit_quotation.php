<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Quotation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container-fluid" style="margin-left:220px; padding-top:40px;">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 col-md-11">
                <div class="card shadow-sm border-0" style="background:#fff; min-height:730px; width:100%; max-width:1100px; margin:auto;">
                    <div class="card-header d-flex align-items-center border-bottom" style="border-radius:0.5rem 0.5rem 0 0; background:#fff;">
                        <i class="bi bi-file-earmark-text" style="font-size:1.5rem;margin-right:10px;color:#ffc107;"></i>
                        <h4 class="mb-0" style="color:#222;font-weight:600;">Edit Quotation</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if($this->session->flashdata('success')): ?>
                            <div class="alert alert-success mb-3"><?php echo $this->session->flashdata('success'); ?></div>
                        <?php endif; ?>
                        <?php if($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger mb-3"><?php echo $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($quote['name']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Quotation No</label>
                                    <input type="text" name="quotation_no" class="form-control" required value="<?php echo htmlspecialchars($quote['quotation_no']); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control" required value="<?php echo htmlspecialchars($quote['address']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="quote_date" class="form-control" required value="<?php echo htmlspecialchars($quote['quote_date']); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Project Code</label>
                                <input type="text" name="project_code" class="form-control" required value="<?php echo htmlspecialchars($quote['project_code']); ?>">
                            </div>
                            <div class="mb-3">
                                <table class="table table-bordered" id="services-table" style="background:#f8f9fa;border:2px solid #000;box-shadow:0 2px 8px rgba(0,0,0,0.08);margin-bottom:20px;">
                                    <thead>
                                        <tr style="background:#f8f9fa;">
                                            <th>Service Description</th>
                                            <th>Amount</th>
                                            <th style="width:60px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($quote['items'])): ?>
                                            <?php foreach ($quote['items'] as $item): ?>
                                                <tr>
                                                    <td><input type="text" name="description[]" class="form-control" required value="<?php echo htmlspecialchars($item['description']); ?>"></td>
                                                    <td><input type="number" step="0.01" name="amount[]" class="form-control amount-input" required value="<?php echo htmlspecialchars($item['amount']); ?>"></td>
                                                    <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td><input type="text" name="description[]" class="form-control" required></td>
                                                <td><input type="number" step="0.01" name="amount[]" class="form-control amount-input" required></td>
                                                <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3"><button type="button" class="btn btn-sm" id="add-row" style="background:#ffc107;color:#222;"><i class="bi bi-plus-circle me-2"></i>Add Service</button></td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold;">Total</td>
                                            <td><input type="number" step="0.01" name="total" class="form-control" readonly value="0" id="total"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn px-4 py-2" style="background:#ffc107;color:#222;" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal"><i class="bi bi-save me-2"></i>Update</button>
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
                                                Are you sure you want to update this quotation?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn" style="background:#ffc107;color:#222;" onclick="document.querySelector('form').submit();">Yes, Update</button>
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
<script>
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.amount-input').forEach(function(input) {
            let val = parseFloat(input.value);
            if (!isNaN(val)) total += val;
        });
        document.getElementById('total').value = total.toFixed(2);
    }
    document.getElementById('add-row').addEventListener('click', function() {
        let tbody = document.querySelector('#services-table tbody');
        let row = document.createElement('tr');
        row.innerHTML = '<td><input type="text" name="description[]" class="form-control" required></td>' +
                        '<td><input type="number" step="0.01" name="amount[]" class="form-control amount-input" required></td>' +
                        '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>';
        tbody.appendChild(row);
    });
    document.querySelector('#services-table').addEventListener('input', function(e) {
        if (e.target.classList.contains('amount-input')) {
            updateTotal();
        }
    });
    document.querySelector('#services-table').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            let row = e.target.closest('tr');
            row.parentNode.removeChild(row);
            updateTotal();
        }
    });
    updateTotal();
</script>
</body>
</html>
