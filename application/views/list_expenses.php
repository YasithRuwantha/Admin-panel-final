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
    <div class="container-fluid mt-4 px-4" style="margin-left:220px;">
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

        <!-- Paid To / Paid By Filter -->
        <form id="paidFilterForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="alpha" value="<?php echo htmlspecialchars($alpha ?? 'recent'); ?>">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <label class="fw-semibold me-2">Filter by:</label>
            <select name="paid_to_filter" class="form-select form-select-sm" style="width:auto;" onchange="document.getElementById('paidFilterForm').submit();">
                <option value="">Paid To (All)</option>
                <?php if (!empty($paid_to_list)): ?>
                    <?php foreach ($paid_to_list as $paid_to): ?>
                        <option value="<?php echo htmlspecialchars($paid_to); ?>"<?php echo (isset($paid_to_filter) && $paid_to_filter === $paid_to) ? ' selected' : ''; ?>><?php echo htmlspecialchars($paid_to); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <select name="paid_by_filter" class="form-select form-select-sm" style="width:auto;" onchange="document.getElementById('paidFilterForm').submit();">
                <option value="">Paid By (All)</option>
                <?php if (!empty($paid_by_list)): ?>
                    <?php foreach ($paid_by_list as $paid_by): ?>
                        <option value="<?php echo htmlspecialchars($paid_by); ?>"<?php echo (isset($paid_by_filter) && $paid_by_filter === $paid_by) ? ' selected' : ''; ?>><?php echo htmlspecialchars($paid_by); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </form>

        <!-- Alphabetical Filter + Rows per page selector (combined) -->
        <form id="alphaForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <input type="hidden" name="paid_to_filter" value="<?php echo htmlspecialchars($paid_to_filter ?? ''); ?>">
            <input type="hidden" name="paid_by_filter" value="<?php echo htmlspecialchars($paid_by_filter ?? ''); ?>">
            <label class="fw-semibold me-2">Sort by Project Name:</label>
            <select name="alpha" class="form-select form-select-sm" style="width:auto;" onchange="document.getElementById('alphaForm').submit();">
                <option value="recent"<?php echo (!isset($alpha) || $alpha === 'recent') ? ' selected' : ''; ?>>Recent</option>
                <option value="az"<?php echo (isset($alpha) && $alpha === 'az') ? ' selected' : ''; ?>>A-Z</option>
                <option value="za"<?php echo (isset($alpha) && $alpha === 'za') ? ' selected' : ''; ?>>Z-A</option>
            </select>
            <label for="perPageSelect" class="fw-semibold ms-3 me-2">Number of rows:</label>
            <select name="per_page" id="perPageSelect" class="form-select form-select-sm" style="width:auto;" onchange="document.getElementById('alphaForm').submit();">
                <?php $perPageOptions = [10, 25, 50, 100]; ?>
                <?php foreach ($perPageOptions as $opt): ?>
                    <option value="<?php echo $opt; ?>"<?php echo (isset($per_page) && $per_page == $opt) ? ' selected' : ''; ?>><?php echo $opt; ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Search Bar -->
        <form id="searchForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="text" name="search" id="expenseSearch" class="form-control" style="max-width:1300px;" placeholder="Search by project name, code, date, category, description, paid to, paid by, payment method, status, or remark..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <div class="table-responsive bg-white rounded shadow-sm p-4" style="min-height:500px;">
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
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle w-100" type="button" id="manageDropdown<?php echo $expense['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-gear"></i> Manage
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="manageDropdown<?php echo $expense['id']; ?>">
                                            <li><a class="dropdown-item" href="<?php echo site_url('expense/view/' . $expense['id']); ?>"><i class="bi bi-eye"></i> View</a></li>
                                            <li><a class="dropdown-item" href="<?php echo site_url('expense/export_expense/' . $expense['id']); ?>"><i class="bi bi-download"></i> Export</a></li>
                                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                                <li><a class="dropdown-item" href="<?php echo site_url('expense/edit/' . $expense['id']); ?>"><i class="bi bi-pencil-square"></i> Edit</a></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="showDeleteModal(<?php echo $expense['id']; ?>); return false;"><i class="bi bi-trash"></i> Delete</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <!-- Delete Confirmation Modal (one per row, unique id) -->
                                    <div class="modal fade" id="deleteModal<?php echo $expense['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $expense['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $expense['id']; ?>">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this expense?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <a id="deleteConfirmBtn<?php echo $expense['id']; ?>" href="<?php echo site_url('expense/delete/' . $expense['id']); ?>" class="btn btn-danger">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                    function showDeleteModal(expenseId) {
                                        var modal = new bootstrap.Modal(document.getElementById('deleteModal' + expenseId));
                                        modal.show();
                                    }
                                    </script>
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
                <?php
                // Build base query string for pagination links, preserving filters
                $query_params = [
                    'range' => htmlspecialchars($selected_range ?? 'all'),
                    'search' => htmlspecialchars($search ?? ''),
                    'alpha' => htmlspecialchars($alpha ?? 'recent'),
                    'paid_to_filter' => htmlspecialchars($paid_to_filter ?? ''),
                    'paid_by_filter' => htmlspecialchars($paid_by_filter ?? ''),
                    'per_page' => htmlspecialchars($per_page ?? 10)
                ];
                $base_query = http_build_query($query_params);
                ?>
                <li class="page-item<?php if ($current_page <= 1) echo ' disabled'; ?>">
                    <a class="page-link" href="?<?php echo $base_query . '&page=' . ($current_page - 1); ?>" tabindex="-1">Prev</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item<?php if ($i == $current_page) echo ' active'; ?>">
                        <a class="page-link" href="?<?php echo $base_query . '&page=' . $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item<?php if ($current_page >= $total_pages) echo ' disabled'; ?>">
                    <a class="page-link" href="?<?php echo $base_query . '&page=' . ($current_page + 1); ?>">Next</a>
                </li>
            </ul>
			<br><br>
        </nav>
        <?php endif; ?>
    </div>
</div>
</body>
<style>
/* Enhance visibility of Manage dropdown */
.dropdown-menu {
    font-size: 1.1rem;
    padding: 0.5rem 0;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    min-width: 180px;
}
.dropdown-menu .dropdown-item {
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: box-shadow 0.2s, background 0.2s;
}
.dropdown-menu .dropdown-item i {
    margin-right: 8px;
    font-size: 1.2em;
}
.dropdown-menu .dropdown-item:hover, .dropdown-menu .dropdown-item:focus {
    background: #f5f5f7;
    box-shadow: 0 4px 18px 0 rgba(100,100,100,0.25), 0 1.5px 4px 0 rgba(0,0,0,0.10);
    z-index: 2;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</html>
