<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Quotation</title>

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

            /* Stack form columns vertically on mobile */
            .row.mb-3 > div {
                margin-bottom: 1rem;
            }

            /* Make inputs full width */
            .form-control {
                width: 100%;
            }

            /* Improve table on mobile */
            #services-table {
                font-size: 0.9rem;
            }

            #services-table th,
            #services-table td {
                padding: 0.75rem 0.5rem;
            }

            /* Make remove button smaller and centered */
            .remove-row {
                width: 38px;
                height: 38px;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Save button full width on mobile */
            .action-buttons {
                flex-direction: column !important;
                width: 100%;
            }

            .action-buttons .btn {
                width: 100%;
                justify-content: center;
            }

            /* Card max-width adjustment */
            .card {
                max-width: 100% !important;
                margin: 0 auto;
            }
        }

        /* Card styling */
        .card {
            border-radius: 0.75rem;
            background: #fff;
        }

        .card-header {
            background: #fff !important;
            border-bottom: 1px solid #dee2e6;
        }

        .card-header h4 {
            color: #222;
            font-weight: 600;
        }

        /* Services table enhancements */
        #services-table {
            background: #f8f9fa;
            border: 2px solid #000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        #services-table thead tr {
            background: #f8f9fa;
        }

        #add-row {
            background: #0d6dfc;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    
    <div class="container-fluid main-container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 col-md-11 col-12">
                <div class="card shadow-sm border-0" style="min-height:730px;">
                    <div class="card-header d-flex align-items-center border-bottom">
                        <i class="bi bi-file-earmark-text" style="font-size:1.5rem;margin-right:10px;color:#0d6efd;"></i>
                        <h4 class="mb-0">Add New Quotation</h4>
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

                            <div class="mb-4">
                                <table class="table table-bordered" id="services-table">
                                    <thead>
                                        <tr>
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
                                            <td colspan="3">
                                                <button type="button" class="btn btn-sm" id="add-row">
                                                    <i class="bi bi-plus-circle me-2"></i>Add Service
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold;">Total</td>
                                            <td><input type="text" step="0.01" name="total" class="form-control" readonly value="0" id="total"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end action-buttons">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="bi bi-save me-2"></i>Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

// Dynamic row addition and total calculation
function updateTotal() {
    let total = 0;
    document.querySelectorAll('.amount-input').forEach(function(input) {
        let val = parseFloat(removeCommas(input.value));
        if (!isNaN(val)) total += val;
    });
    let totalField = document.getElementById('total');
    totalField.value = formatWithCommas(total.toFixed(2));
}

document.getElementById('add-row').addEventListener('click', function() {
    let tbody = document.querySelector('#services-table tbody');
    let row = document.createElement('tr');
    row.innerHTML = '<td><input type="text" name="description[]" class="form-control" required placeholder="Enter service description"></td>' +
                    '<td><input type="text" step="0.01" name="amount[]" class="form-control amount-input" required placeholder="0.00"></td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>';
    tbody.appendChild(row);

    // Attach formatting to new amount input
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

updateTotal();
</script>

</body>
</html>
