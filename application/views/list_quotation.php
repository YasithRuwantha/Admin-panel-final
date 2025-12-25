<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Quotation</title>

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
            #quotationSearch {
                max-width: 100% !important;
                width: 100%;
            }

            /* Table improvements for mobile */
            .table-responsive {
                font-size: 0.875rem;
            }

            th, td { white-space: nowrap; }

            /* Give important columns min-width */
            th:nth-child(1), td:nth-child(1) { min-width: 140px; } /* Name */
            th:nth-child(2), td:nth-child(2) { min-width: 120px; } /* Quotation No */
            th:nth-child(3), td:nth-child(3) { min-width: 160px; } /* Address */
            th:nth-child(4), td:nth-child(4) { min-width: 110px; } /* Date */
            th:nth-child(5), td:nth-child(5) { min-width: 130px; } /* Project Code */
            th:nth-child(6), td:nth-child(6) { min-width: 120px; } /* Total */



            /* Stack filters vertically on mobile */
            .filters-row {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 1rem;
            }

            .filters-row .form-select {
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
            .filters-row {
                gap: 1.5rem;
            }
        }

        /* Enhance visibility of Manage dropdown*/
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
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap header-row">
            <h2 class="mb-0">List Quotation</h2>
            <a href="<?php echo site_url('quote/add'); ?>" class="btn btn-primary add-quotation-btn">+ Add Quotation</a>
        </div>
        <style>
        @media (max-width: 768px) {
            .header-row {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 1rem !important;
            }
            .add-quotation-btn {
                width: 100% !important;
                min-width: 0 !important;
                justify-content: center;
                display: flex;
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

        <!-- Sort by Name and Number of Rows (side by side, mobile responsive, List Projects style) + Export Button -->
        <form id="alphaForm" method="get" class="mb-3">
            <div class="d-flex align-items-center filters-upper-row flex-wrap flex-md-nowrap">
                <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
                    <label class="fw-semibold me-2 mb-0">Sort by Name:</label>
                    <select name="alpha" class="form-select form-select-sm" style="width:auto;min-width:120px;" onchange="this.form.submit();">
                        <option value="recent"<?php echo (!isset($alpha) || $alpha === 'recent') ? ' selected' : ''; ?>>Recent</option>
                        <option value="az"<?php echo (isset($alpha) && $alpha === 'az') ? ' selected' : ''; ?>>A-Z</option>
                        <option value="za"<?php echo (isset($alpha) && $alpha === 'za') ? ' selected' : ''; ?>>Z-A</option>
                    </select>
                    <label for="perPageSelect" class="fw-semibold me-2 mb-0 ms-md-4">Number of rows:</label>
                    <select name="per_page" id="perPageSelect" class="form-select form-select-sm" style="width:auto;min-width:100px;" onchange="this.form.submit();">
                        <?php $perPageOptions = [10, 25, 50, 100]; ?>
                        <?php foreach ($perPageOptions as $opt): ?>
                            <option value="<?php echo $opt; ?>"<?php echo (isset($per_page) && $per_page == $opt) ? ' selected' : ''; ?>><?php echo $opt; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php
                        $export_params = [];
                        if (!empty($selected_range)) $export_params['range'] = $selected_range;
                        if (!empty($search)) $export_params['search'] = $search;
                        if (!empty($alpha)) $export_params['alpha'] = $alpha;
                        if (!empty($per_page)) $export_params['per_page'] = $per_page;
                        if (!empty($current_page)) $export_params['page'] = $current_page;
                        $export_url = site_url('quote/export_all');
                        if (!empty($export_params)) {
                            $export_url .= '?' . http_build_query($export_params);
                        }
                    ?>
                    <!-- Desktop export button: separate div but next to number of rows -->
                    <div class="d-none d-lg-inline-flex align-items-center ms-md-3">
                        <a href="<?php echo $export_url; ?>" class="btn btn-success btn-sm export-desktop align-items-center">
                            <i class="bi bi-download"></i> Export Quotations
                        </a>
                    </div>
                </div>
                <!-- Mobile export fallback: full width and margin-top -->
                <div class="w-100 mt-2 d-lg-none">
                    <a href="<?php echo $export_url; ?>" class="btn btn-success ">
                        <i class="bi bi-download"></i> Export Quotations
                    </a>
                </div>
            </div>
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>">
        </form>
        <style>
        @media (min-width: 769px) {
            .filters-upper-row > .d-flex.flex-wrap.flex-md-nowrap {
                flex-wrap: nowrap !important;
            }
            .filters-upper-row label[for="perPageSelect"] {
                margin-left: 2rem !important;
            }
        }
        </style>
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
            <input type="text" name="search" id="quotationSearch" class="form-control" style="max-width:1250px;" placeholder="Search by name, quotation no, address, project code, or total..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <button type="submit" class="btn btn-primary" style="min-width:120px; font-weight:500; font-size:1.05rem;">Search</button>
        </form>
        <style>
        @media (max-width: 768px) {
            #quotationSearch {
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
                        <th>Name</th>
                        <th>Quotation No</th>
                        <th>Address</th>
                        <th>Date</th>
                        <th>Project Code</th>
                        <th>Total</th>
                        <th style="width:180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($quotations)): ?>
                        <?php foreach ($quotations as $quote): ?>
                            <tr>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"> <?php echo htmlspecialchars($quote['name']); ?> </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"> <?php echo htmlspecialchars($quote['quotation_no']); ?> </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"> <?php echo htmlspecialchars($quote['address']); ?> </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"> <?php echo htmlspecialchars($quote['quote_date']); ?> </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"> <?php echo htmlspecialchars($quote['project_code']); ?> </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line; font-weight:bold;"> <?php echo htmlspecialchars(number_format((float)$quote['amount'], 2)); ?> </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle w-100" type="button" id="manageDropdown<?php echo $quote['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                            Manage
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="manageDropdown<?php echo $quote['id']; ?>">
                                            <li><a class="dropdown-item" href="<?php echo site_url('quote/view/' . $quote['id']); ?>">View</a></li>
                                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                                <li><a class="dropdown-item" href="<?php echo site_url('quote/edit/' . $quote['id']); ?>">Edit</a></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="showDeleteModal(<?php echo $quote['id']; ?>); return false;">Delete</a></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item text-danger fw-bold d-flex align-items-center" href="<?php echo site_url('quote/pdf/' . $quote['id']); ?>" target="_blank">PDF</a></li>
                                        </ul>
                                    </div>
                                    <!-- Delete Confirmation Modal (one per row, unique id) -->
                                    <div class="modal fade" id="deleteModal<?php echo $quote['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $quote['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $quote['id']; ?>">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this quotation?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <a id="deleteConfirmBtn<?php echo $quote['id']; ?>" href="<?php echo site_url('quote/delete/' . $quote['id']); ?>" class="btn btn-danger">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                    function showDeleteModal(quoteId) {
                                        var modal = new bootstrap.Modal(document.getElementById('deleteModal' + quoteId));
                                        modal.show();
                                    }
                                    </script>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center">No quotations found.</td></tr>
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
