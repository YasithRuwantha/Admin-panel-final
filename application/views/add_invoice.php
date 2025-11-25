<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Invoice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container" style="max-width:700px; margin-top:40px; margin-left:220px;">
        <h2>Add Invoice</h2>
        <form method="post">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Name of the Client</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Invoice No</label>
                    <input type="text" name="invoice_no" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date</label>
                    <input type="date" name="invoice_date" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Project Code</label>
                <input type="text" name="project_code" class="form-control" required>
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
                            <td><input type="text" name="description[]" class="form-control" required></td>
                            <td><input type="number" step="0.01" name="amount[]" class="form-control amount-input" required></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">&times;</button></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><button type="button" class="btn btn-sm" id="add-row" style="background:#0d6dfc;color:#fff;">Add Service</button></td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold;">Total</td>
                            <td><input type="number" step="0.01" name="total" class="form-control" readonly value="0" id="total"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
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
                               '<td><button type="button" class="btn btn-danger btn-sm remove-row">&times;</button></td>';
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
    </div>
</div>
</body>
</html>
