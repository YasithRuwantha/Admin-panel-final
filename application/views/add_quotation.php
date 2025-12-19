<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Quotation</title>
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
                        <i class="bi bi-file-earmark-text" style="font-size:1.5rem;margin-right:10px;color:#0d6efd;"></i>
                        <h4 class="mb-0" style="color:#222;font-weight:600;">Add New Quotation</h4>
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
                                    <input type="text" name="name" class="form-control" required placeholder="Enter name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Quotation No</label>
                                    <input type="text" name="quotation_no" class="form-control" required placeholder="Enter quotation number">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control" required placeholder="Enter address">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="quote_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Project Code</label>
                                <input type="text" name="project_code" class="form-control" required placeholder="Enter project code">
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
                                        <tr>
                                            <td><input type="text" name="description[]" class="form-control" required placeholder="Enter service description"></td>
                                            <td><input type="text" step="0.01" name="amount[]" class="form-control amount-input" required placeholder="0.00"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3"><button type="button" class="btn btn-sm" id="add-row" style="background:#0d6dfc;color:#fff;"><i class="bi bi-plus-circle me-2"></i>Add Service</button></td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold;">Total</td>
                                            <td><input type="text" step="0.01" name="total" class="form-control" readonly value="0" id="total"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4 py-2"><i class="bi bi-save me-2"></i>Save</button>
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
    // Thousand separator for all .amount-input fields
    function formatWithCommas(val) {
        val = val.replace(/,/g, '');
        if (val === '' || isNaN(val)) return '';
        let parts = val.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join('.');
    }
    function removeCommas(val) {
        return val.replace(/,/g, '');
    }
    function attachAmountInputEvents(input) {
        input.addEventListener('input', function() {
            let value = removeCommas(this.value);
            if (value && !isNaN(value)) {
                this.value = formatWithCommas(value);
            } else {
                this.value = '';
            }
        });
        input.addEventListener('focus', function() {
            this.value = removeCommas(this.value);
        });
        input.addEventListener('blur', function() {
            let value = removeCommas(this.value);
            if (value && !isNaN(value)) {
                this.value = formatWithCommas(value);
            } else {
                this.value = '';
            }
        });
    }
    // Attach to all current amount-inputs
    document.querySelectorAll('.amount-input').forEach(attachAmountInputEvents);
    // Attach to dynamically added rows
    document.getElementById('add-row').addEventListener('click', function() {
        let tbody = document.querySelector('#services-table tbody');
        let row = document.createElement('tr');
        row.innerHTML = '<td><input type="text" name="description[]" class="form-control" required placeholder="Enter service description"></td>' +
                        '<td><input type="text" step="0.01" name="amount[]" class="form-control amount-input" required placeholder="0.00"></td>' +
                        '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>';
        tbody.appendChild(row);
        setTimeout(function() {
            let inputs = document.querySelectorAll('.amount-input');
            inputs.forEach(function(input) {
                if (!input.hasAttribute('data-thousand-sep')) {
                    attachAmountInputEvents(input);
                    input.setAttribute('data-thousand-sep', '1');
                }
            });
        }, 100);
    });
    // Remove commas before form submit
    document.querySelector('form').addEventListener('submit', function(e) {
        document.querySelectorAll('.amount-input').forEach(function(input) {
            input.value = removeCommas(input.value);
        });
        var totalField = document.getElementById('total');
        if (totalField) {
            totalField.value = removeCommas(totalField.value);
        }
    });
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.amount-input').forEach(function(input) {
            let val = parseFloat(input.value.replace(/,/g, ''));
            if (!isNaN(val)) total += val;
        });
        let totalField = document.getElementById('total');
        totalField.value = formatWithCommas(total.toFixed(2));
    }
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
