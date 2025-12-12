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
</body>
</html>
