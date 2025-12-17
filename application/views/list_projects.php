<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Projects</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container mt-5" style="margin-left:220px;">
        <h2>List Projects</h2>
        <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Project Name</th>
                    <th>Project Code</th>
                    <th>Client Name</th>
                    <th>Address</th>
                    <th>Project Value</th>
                    <th>Start Date</th>
                    <th>Status</th>
                    <th style="width:160px;">Actions</th>                   
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['id']); ?></td>
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['name']); ?></td>
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['project_code']); ?></td>
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['client']); ?></td>
                            <td style="word-break:break-word;max-width:250px;white-space:pre-line;"><?php echo htmlspecialchars($project['address']); ?></td>
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars(number_format((float)$project['paysheet_value'], 2)); ?></td>
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['start_date']); ?></td>
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['status']); ?></td>
                            <td style="white-space:nowrap;">
                                <a href="<?php echo site_url('project/view/' . $project['id']); ?>" class="btn btn-sm btn-primary me-1"><i class="bi bi-eye"></i> View</a>
                                <?php if (function_exists('is_admin') && is_admin()): ?>
                                    <a href="<?php echo site_url('project/edit/' . $project['id']); ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center">No projects found.</td></tr>
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
        </nav>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
