<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - CanoAccounts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .sidebar { height: 100vh; background: #343a40; color: #fff; padding-top: 30px; }
        .sidebar a { color: #fff; text-decoration: none; display: block; padding: 12px 24px; }
        .sidebar a:hover { background: #495057; }
        .main-content { margin-left: 220px; padding: 40px; }
        .sidebar .active { background: #007bff; }
    </style>
</head>
<body>
<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="main-content flex-grow-1">
        <h2>Project Financial Report</h2>
        <p class="text-muted">Overview of income, expenses, and balances.</p>

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
            <label class="fw-semibold me-2">Sort by Project Name:</label>
            <select name="alpha" class="form-select form-select-sm" style="width:auto;" onchange="document.getElementById('alphaForm').submit();">
                <option value="recent"<?php echo (!isset($alpha) || $alpha === 'recent') ? ' selected' : ''; ?>>Recent</option>
                <option value="az"<?php echo (isset($alpha) && $alpha === 'az') ? ' selected' : ''; ?>>A-Z</option>
                <option value="za"<?php echo (isset($alpha) && $alpha === 'za') ? ' selected' : ''; ?>>Z-A</option>
            </select>
        </form>

        <!-- Live Search Bar -->
        <div class="mb-3">
            <input type="text" id="reportSearch" class="form-control" placeholder="Search projects...">
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Project</th>
                        <th class="text-end">Total Budget</th>
                        <th class="text-end">Total Expenses</th>
                        <th class="text-end">Total Income</th>
                        <th class="text-end">Cash in Hand</th>
                        <th class="text-end">Cash In Project</th>
                        <th>Status</th>
                        <th>Export</th>
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
                        // simple highlighting similar to the screenshot
                        $row_class = '';
                        if ($balance_in_hand < 0 || $cash_in_project < 0) { $row_class = 'table-danger'; }
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
                        <td>
                            <a href="<?php echo site_url('home/export_excel?range=' . urlencode($selected_range ?? 'all') . '&alpha=' . urlencode($alpha ?? 'recent') . '&project_code=' . urlencode($row['project_code'])); ?>" class="btn btn-outline-primary btn-sm">Export</a>
                        </td>
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
    </div>
</div>
</div>
<script>
// Live search filter (project name and status only)
document.getElementById('reportSearch').addEventListener('input', function() {
    const search = this.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(function(row) {
        // Get project name and status columns
        const projectCell = row.querySelector('td:nth-child(1)');
        const statusCell = row.querySelector('td:nth-child(7)');
        if (!projectCell || !statusCell) return;
        const projectText = projectCell.textContent.toLowerCase();
        const statusText = statusCell.textContent.toLowerCase();
        if (projectText.includes(search) || statusText.includes(search)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Date range filter
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
