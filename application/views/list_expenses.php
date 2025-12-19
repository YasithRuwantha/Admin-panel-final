<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Expenses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container mt-5" style="margin-left:220px;">
        <h2>List Expenses</h2>
        <!-- Date Range Filter Buttons as Form -->
        <form id="dateRangeForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <label class="me-2 fw-semibold">Filter by date:</label>
            <input type="hidden" name="range" id="rangeInput" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="alpha" id="alphaInput" value="<?php echo htmlspecialchars($alpha ?? 'recent'); ?>">
            <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'today' ? ' active' : ''; ?>" data-range="today">Today</button>
            <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'last7' ? ' active' : ''; ?>" data-range="last7">Last 7 days</button>
            <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'month' ? ' active' : ''; ?>" data-range="month">This month</button>
            <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'all' ? ' active' : ''; ?>" data-range="all">All time</button>
        </form>

        <!-- Alphabetical Filter -->
        <form id="alphaForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <label class="fw-semibold me-2">Sort by Project Name:</label>
            <select name="alpha" class="form-select form-select-sm" style="width:auto;" onchange="document.getElementById('alphaForm').submit();">
                <option value="recent"<?php echo (!isset($alpha) || $alpha === 'recent') ? ' selected' : ''; ?>>Recent</option>
                <option value="az"<?php echo (isset($alpha) && $alpha === 'az') ? ' selected' : ''; ?>>A-Z</option>
                <option value="za"<?php echo (isset($alpha) && $alpha === 'za') ? ' selected' : ''; ?>>Z-A</option>
            </select>
        </form>

        <!-- Search Bar -->
        <form id="searchForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="text" name="search" id="expenseSearch" class="form-control" style="max-width:1212px;" placeholder="Search by project name, code, date, category, description, paid to, paid by, payment method, status, or remark..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <div class="table-responsive">
        </body>
        <script>
        // Date range filter: submit form on button click, reset alpha to default (recent)
        document.querySelectorAll('.filter-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('rangeInput').value = btn.getAttribute('data-range');
                document.getElementById('alphaInput').value = 'recent';
                document.getElementById('dateRangeForm').submit();
            });
        });
        </script>
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Project Code</th>
                    <th>Expense Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Paid To</th>
                    <th>Paid By</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Remark</th>
                    <th>Document</th>
                    <th style="width:140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($expenses)): ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['project_name']); ?></td>
                            <td><?php echo htmlspecialchars($expense['project_code']); ?></td>
                            <td><?php echo htmlspecialchars($expense['expense_date']); ?></td>
                            <td><?php echo htmlspecialchars($expense['category']); ?></td>
                            <td><?php echo htmlspecialchars($expense['description']); ?></td>
                            <td><?php echo htmlspecialchars($expense['paid_to']); ?></td>
                            <td><?php echo htmlspecialchars($expense['paid_by']); ?></td>
                            <td><?php echo htmlspecialchars(number_format((float)$expense['amount'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($expense['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($expense['status']); ?></td>
                            <td><?php echo htmlspecialchars($expense['remark']); ?></td>
                            <td>
                                <?php if (!empty($expense['document_path'])): ?>
                                    <a href="<?php echo site_url('expense/view/' . $expense['id']); ?>" class="btn btn-link p-0">View</a>
                                <?php else: ?>
                                    <span class="text-muted">No documents</span>
                                <?php endif; ?>
                            </td>
                                <td>
                                    <div class="d-flex flex-column gap-2 align-items-start">
                                        <a href="<?php echo site_url('expense/view/' . $expense['id']); ?>" class="btn btn-primary w-100" style="min-width:70px;"><i class="bi bi-eye"></i> View</a>
                                        <a href="<?php echo site_url('expense/export_expense/' . $expense['id']); ?>" class="btn btn-success w-100" style="min-width:70px;"><i class="bi bi-download"></i> Export</a>
                                        <?php if (function_exists('is_admin') && is_admin()): ?>
                                            <a href="<?php echo site_url('expense/edit/' . $expense['id']); ?>" class="btn btn-warning w-100" style="min-width:70px;"><i class="bi bi-pencil-square"></i> Edit</a>
                                            <button type="button" class="btn btn-danger w-100" style="min-width:70px;" onclick="showDeleteModal(<?php echo $expense['id']; ?>)"><i class="bi bi-trash"></i> Delete</button>
                                        <!-- Delete Confirmation Modal -->
                                        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete this expense?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <a id="deleteConfirmBtn" href="#" class="btn btn-danger">Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                                        <script>
                                        function showDeleteModal(expenseId) {
                                                var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                                                var btn = document.getElementById('deleteConfirmBtn');
                                                btn.href = '<?php echo site_url('expense/delete/'); ?>' + expenseId;
                                                modal.show();
                                        }
                                        </script>
                                        <?php endif; ?>
                                    </div>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="14" class="text-center">No expenses found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        <!-- Pagination -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item<?php if ($current_page <= 1) echo ' disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" tabindex="-1">Prev</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item<?php if ($i == $current_page) echo ' active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item<?php if ($current_page >= $total_pages) echo ' disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
                </li>
            </ul>
			<br><br>
        </nav>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
