<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Expenses</title>

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
        .main-content {
            margin-left: 220px;
            transition: margin-left 0.3s ease;
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
                padding: 20px 15px !important;
            }

            /* Search input full width on mobile */
            #expenseSearch {
                max-width: 100% !important;
                width: 100%;
            }

            /* Table improvements for mobile */
            .table-responsive {
                font-size: 0.875rem;
            }

            th, td { white-space: nowrap; }

            /* Give important columns min-width */
            th:nth-child(1), td:nth-child(1) { min-width: 160px; } /* Project Name */
            th:nth-child(2), td:nth-child(2) { min-width: 130px; } /* Project Code */
            th:nth-child(3), td:nth-child(3) { min-width: 120px; } /* Expense Date */
            th:nth-child(4), td:nth-child(4) { min-width: 140px; } /* Category */
            th:nth-child(5), td:nth-child(5) { min-width: 180px; } /* Description */
            th:nth-child(6), td:nth-child(6) { min-width: 130px; } /* Paid To */
            th:nth-child(7), td:nth-child(7) { min-width: 130px; } /* Paid By */
            th:nth-child(8), td:nth-child(8) { min-width: 120px; } /* Amount */

            /* Hide less critical columns on very small screens */


            /* Stack filters vertically on mobile */
            .filters-row, .paid-filters-row {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 1rem;
            }

            .filters-row .form-select, .paid-filters-row .form-select {
                width: 100% !important;
            }

            /* Date filter buttons wrap and stack nicely */
            #dateRangeForm {
                flex-direction: column !important;
                align-items: flex-start !important;
            }

            #dateRangeForm .btn {
                min-width: 100px;
            }

            /* Export button moves below on mobile */
            .export-mobile {
                margin-top: 0.5rem;
                width: 100%;
            }

            /* Pagination wrap */
            .pagination {
                flex-wrap: wrap;
            }
        }

        @media (min-width: 769px) {
            .filters-row, .paid-filters-row {
                gap: 1.5rem;
            }
        }

        /* Enhance visibility of Manage dropdown (unchanged) */
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
</head>
<body>

<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container-fluid mt-4 px-4 main-content">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap flex-md-nowrap flex-column flex-md-row">
            <div class="d-flex flex-column flex-md-row w-100 align-items-md-center justify-content-between">
                <h2 class="mb-2 mb-md-0">List Expenses</h2>
                <a href="<?php echo site_url('expense/add'); ?>" class="btn btn-primary add-expense-btn mt-2 mt-md-0 ms-0 ms-md-3" style="min-width:150px; font-weight:500; font-size:1.1rem;">+ Add Expense</a>
            </div>
        </div>
        <style>
        /* Responsive Add Expense Button */
        @media (max-width: 768px) {
            .add-expense-btn {
                width: 100% !important;
                display: block;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            .main-content h2 {
                width: 100%;
            }
        }
        </style>

        <!-- Date Range Filter Buttons as Form (List Projects style) -->
        <form id="dateRangeForm" method="get" class="mb-4 d-flex flex-column flex-sm-row flex-wrap align-items-start gap-3">
            <label class="fw-semibold nowrap me-3">Filter by date:</label>
            <input type="hidden" name="range" id="rangeInput" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="alpha" id="alphaInput" value="<?php echo htmlspecialchars($alpha ?? 'recent'); ?>">
            <div class="date-btn-group">
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'today' ? ' active' : ''; ?>" data-range="today">Today</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'last7' ? ' active' : ''; ?>" data-range="last7">Last 7 days</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'month' ? ' active' : ''; ?>" data-range="month">This month</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'all' ? ' active' : ''; ?>" data-range="all">All time</button>
            </div>
        </form>
        <style>
        /* Date filter buttons - smaller and wrap naturally (from list_projects.php) */
        .date-btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .date-btn-group .btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            min-width: 70px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        @media (max-width: 576px) {
            .date-btn-group .btn {
                font-size: 0.72rem;
                padding: 0.2rem 0.4rem;
            }
        }
        #dateRangeForm {
            align-items: flex-start;
        }
        #dateRangeForm label {
            margin-bottom: 0;
            margin-top: 0.35rem;
        }
        </style>

        <!-- Paid To / Paid By Filter -->
        <form id="paidFilterForm" method="get" class="mb-3 d-flex paid-filters-row align-items-center flex-wrap gap-2">
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

        <!-- Sort by Project Name and Number of Rows (side by side, mobile responsive, List Projects style) + Export Button -->
        <form id="alphaForm" method="get" class="mb-3">
            <div class="filters-upper-row d-flex align-items-center flex-wrap">
                <?php
                    $export_query = http_build_query([
                        'range' => htmlspecialchars($selected_range ?? 'all'),
                        'search' => htmlspecialchars($search ?? ''),
                        'alpha' => htmlspecialchars($alpha ?? 'recent'),
                        'paid_to_filter' => htmlspecialchars($paid_to_filter ?? ''),
                        'paid_by_filter' => htmlspecialchars($paid_by_filter ?? ''),
                        'per_page' => htmlspecialchars($per_page ?? 10),
                        'page' => htmlspecialchars($current_page ?? 1)
                    ]);
                ?>
                <!-- Row for sort, number of rows, and export button, side by side on desktop -->
                <div class="d-flex align-items-center gap-3 mb-2 mb-lg-0 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <label class="fw-semibold me-2 mb-0">Sort by Project Name:</label>
                        <select name="alpha" class="form-select form-select-sm" style="width:auto;min-width:120px;" onchange="this.form.submit();">
                            <option value="recent"<?php echo (!isset($alpha) || $alpha === 'recent') ? ' selected' : ''; ?>>Recent</option>
                            <option value="az"<?php echo (isset($alpha) && $alpha === 'az') ? ' selected' : ''; ?>>A-Z</option>
                            <option value="za"<?php echo (isset($alpha) && $alpha === 'za') ? ' selected' : ''; ?>>Z-A</option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label for="perPageSelect" class="fw-semibold me-2 mb-0">Number of rows:</label>
                        <select name="per_page" id="perPageSelect" class="form-select form-select-sm" style="width:auto;min-width:100px;" onchange="this.form.submit();">
                            <?php $perPageOptions = [10, 25, 50, 100]; ?>
                            <?php foreach ($perPageOptions as $opt): ?>
                                <option value="<?php echo $opt; ?>"<?php echo (isset($per_page) && $per_page == $opt) ? ' selected' : ''; ?>><?php echo $opt; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-none d-lg-flex align-items-center">
                        <a href="<?php echo site_url('expense/export_all?' . $export_query); ?>" class="btn btn-success btn-sm export-desktop align-items-center">
                            <i class="bi bi-download"></i> Export Expenses
                        </a>
                    </div>
                </div>
                <!-- Mobile export fallback: full width and margin-top -->
                <div class="w-100 mt-2 d-lg-none">
                    <a href="<?php echo site_url('expense/export_all?' . $export_query); ?>" class="btn btn-success ">
                        <i class="bi bi-download"></i> Export Expenses
                    </a>
                </div>
            </div>
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <input type="hidden" name="paid_to_filter" value="<?php echo htmlspecialchars($paid_to_filter ?? ''); ?>">
            <input type="hidden" name="paid_by_filter" value="<?php echo htmlspecialchars($paid_by_filter ?? ''); ?>">
        </form>
        <style>
        .filters-upper-row {
            flex-direction: row !important;
            flex-wrap: wrap;
            gap: 1rem;
            width: 100%;
        }
        .filters-upper-row > div {
            flex: 1 1 auto;
            min-width: 200px;
        }
        @media (max-width: 768px) {
            .filters-upper-row {
                flex-direction: row !important;
                flex-wrap: wrap;
                gap: 1rem;
                width: 100%;
            }
            .filters-upper-row > div {
                flex: 1 1 auto;
                min-width: 120px;
            }
        }
        </style>

        <!-- Search Bar (List Projects style) -->
        <form id="searchForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="text" name="search" id="expenseSearch" class="form-control" style="max-width:1250px;" placeholder="Search by project name, code, date, category, description, paid to, paid by, payment method, status, or remark..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <button type="submit" class="btn btn-primary" style="min-width:120px; font-weight:500; font-size:1.05rem;">Search</button>
        </form>
        <style>
        @media (max-width: 768px) {
            #expenseSearch {
                max-width: 100% !important;
                width: 100%;
            }
            #searchForm .btn {
                width: 100%;
                margin-top: 0.5rem;
            }
        }
        </style>

        <div class="table-responsive bg-white rounded shadow-sm p-4" style="min-height:500px;">
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
                        <th>Docs</th>
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
                                        <span class="text-muted">No doc</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle w-100" type="button" id="manageDropdown<?php echo $expense['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                            Manage
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="manageDropdown<?php echo $expense['id']; ?>">
                                            <li><a class="dropdown-item" href="<?php echo site_url('expense/view/' . $expense['id']); ?>">View</a></li>
                                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                                <li><a class="dropdown-item" href="<?php echo site_url('expense/edit/' . $expense['id']); ?>">Edit</a></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="showDeleteModal(<?php echo $expense['id']; ?>); return false;">Delete</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <!-- Delete Confirmation Modal -->
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
                        <tr><td colspan="13" class="text-center">No expenses found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center flex-wrap">
                <?php
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
        </nav>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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

</body>
</html>
