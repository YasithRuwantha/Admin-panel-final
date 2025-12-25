<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - CanoAccounts</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #f8f9fa;
            overflow-x: hidden;
        }

        /* Sidebar is fixed on desktop */
        .main-content {
            margin-left: 220px;
            padding: 40px;
            transition: margin-left 0.3s ease;
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
                padding: 20px 15px;
            }

            /* Search input full width on mobile */
            #liveSearchInput {
                max-width: 100% !important;
                width: 100%;
            }

            /* Table improvements for mobile */
            .table-responsive {
                font-size: 0.875rem;
            }

            th:nth-child(1), td:nth-child(1) { min-width: 140px; }
            th, td { white-space: nowrap; }



            /* On mobile: keep sort and rows on same line if possible, export goes to right or below */
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
        }

        @media (min-width: 769px) {
            /* Desktop only: larger gap */
            .filters-upper-row {
                gap: 2rem;
            }

            /* Desktop: Export button immediately after "Number of rows" */
            .export-desktop {
                margin-left: 1.5rem; /* Space after the rows dropdown */
            }
        }

        /* Container adjustments */
        .container-fluid {
            transition: margin-left 0.3s ease;
        }

        @media (max-width: 768px) {
            .container-fluid {
                margin-left: 0 !important;
                padding-left: 15px;
                padding-right: 15px;
            }
        }

        /* Date filter buttons - smaller and wrap naturally */
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

        /* Align label with buttons */
        #dateRangeForm {
            align-items: flex-start;
        }

        #dateRangeForm label {
            margin-bottom: 0;
            margin-top: 0.35rem;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container-fluid mt-4 px-4 main-content">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
            <h2 class="mb-0">Project Financial Report</h2>
        </div>

        <!-- Date Range Filter Buttons -->
        <form id="dateRangeForm" method="get" class="mb-4 d-flex flex-column flex-sm-row flex-wrap align-items-start gap-3">
            <label class="fw-semibold nowrap me-3">Filter by date:</label>
            <input type="hidden" name="range" id="rangeInput" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <div class="date-btn-group">
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'today' ? ' active' : ''; ?>" data-range="today">Today</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'last7' ? ' active' : ''; ?>" data-range="last7">Last 7 days</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'month' ? ' active' : ''; ?>" data-range="month">This month</button>
                <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'all' ? ' active' : ''; ?>" data-range="all">All time</button>
            </div>
        </form>

        <!-- Sort, Rows per page, and Export Button -->
        <form id="alphaForm" method="get" class="mb-3">
            <div class="d-flex align-items-center filters-upper-row flex-wrap">
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
                        <option value="all"<?php echo (!isset($per_page) || $per_page === 'all') ? ' selected' : ''; ?>>All</option>
                        <option value="20"<?php echo (isset($per_page) && $per_page == 20) ? ' selected' : ''; ?>>20</option>
                        <option value="50"<?php echo (isset($per_page) && $per_page == 50) ? ' selected' : ''; ?>>50</option>
                        <option value="100"<?php echo (isset($per_page) && $per_page == 100) ? ' selected' : ''; ?>>100</option>
                    </select>

                    <!-- Export button placed right after Number of rows on desktop -->
                    <a href="<?php echo site_url('home/export_excel?range=' . urlencode($selected_range ?? 'all') . '&alpha=' . urlencode($alpha ?? 'recent') . '&per_page=' . urlencode($per_page ?? 'all')); ?>"
                       class="btn btn-success btn-sm export-desktop d-none d-lg-inline-flex align-items-center">
                        Export Projects
                    </a>
                </div>

                <!-- Fallback for mobile/small screens: export at the far right -->
                <div class="ms-auto d-lg-none">
                    <a href="<?php echo site_url('home/export_excel?range=' . urlencode($selected_range ?? 'all') . '&alpha=' . urlencode($alpha ?? 'recent') . '&per_page=' . urlencode($per_page ?? 'all')); ?>"
                       class="btn btn-success btn-sm">
                        Export Projects
                    </a>
                </div>
				
            </div>
        </form>

        <!-- Search Bar (Live Search) -->
        <div class="mb-3">
            <input type="text" id="liveSearchInput" class="form-control" placeholder="Live search projects by name, code, or status...">
        </div>

        <div class="table-responsive bg-white rounded shadow-sm p-4" style="min-height:500px;">
            <table class="table table-bordered table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th class="text-end">Total Budget</th>
                        <th class="text-end">Total Expenses</th>
                        <th class="text-end">Total Income</th>
                        <th class="text-end">Cash in Hand</th>
                        <th class="text-end">Cash In Project</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($report_rows)): ?>
                    <?php foreach ($report_rows as $row):
                        $budget = (float)$row['project_value'];
                        $expenses = (float)$row['total_expenses'];
                        $income = (float)$row['total_income'];
                        $balance_in_hand = (float)$row['cash_in_hand'];
                        $cash_in_project = (float)$row['cash_in_project'];
                        $row_class = ($balance_in_hand < 0 || $cash_in_project < 0) ? 'table-danger' : '';
                    ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td>
                            <div class="fw-semibold"><?php echo htmlspecialchars($row['project_name'] ?: $row['project_code']); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars($row['project_code']); ?></div>
                        </td>
                        <td class="text-end"><?php echo number_format($budget, 2); ?></td>
                        <td class="text-end"><?php echo number_format($expenses, 2); ?></td>
                        <td class="text-end"><?php echo number_format($income, 2); ?></td>
                        <td class="text-end"><?php echo number_format($balance_in_hand, 2); ?></td>
                        <td class="text-end"><?php echo number_format($cash_in_project, 2); ?></td>
                        <td><?php echo htmlspecialchars($row['status'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No projects found.</td>
                    </tr>
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
                    'per_page' => htmlspecialchars($per_page ?? 'all')
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

<style>
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
.dropdown-menu .dropdown-item:hover, .dropdown-menu .dropdown-item:focus {
    background: #f5f5f7;
    box-shadow: 0 4px 18px 0 rgba(100,100,100,0.25), 0 1.5px 4px 0 rgba(0,0,0,0.10);
    z-index: 2;
}
.dropdown-menu .dropdown-item i {
    margin-right: 8px;
    font-size: 1.2em;
}

.nowrap { white-space: nowrap; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Date range filter
document.querySelectorAll('.filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const range = btn.getAttribute('data-range');
        document.getElementById('rangeInput').value = range;
        document.getElementById('dateRangeForm').submit();
    });
});

// Live search
document.getElementById('liveSearchInput').addEventListener('input', function () {
    const search = this.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(row => {
        const project = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
        const status = row.querySelector('td:nth-child(7)')?.textContent.toLowerCase() || '';
        row.style.display = (project.includes(search) || status.includes(search)) ? '' : 'none';
    });
});
</script>

</body>
</html>
