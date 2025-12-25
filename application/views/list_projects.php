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
    <div class="container-fluid mt-4 px-4" style="margin-left:220px;">
        <h2>List Projects</h2>
        <!-- Date Range Filter Buttons as Form -->
        <form id="dateRangeForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <label class="me-2 fw-semibold">Filter by date:</label>
            <input type="hidden" name="range" id="rangeInput" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'today' ? ' active' : ''; ?>" data-range="today">Today</button>
            <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'last7' ? ' active' : ''; ?>" data-range="last7">Last 7 days</button>
            <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'month' ? ' active' : ''; ?>" data-range="month">This month</button>
            <button type="button" class="btn btn-outline-primary btn-sm filter-btn<?php echo ($selected_range ?? 'all') === 'all' ? ' active' : ''; ?>" data-range="all">All time</button>
        </form>


        <!-- Alphabetical Filter + Rows per page selector (combined) -->
        <form id="alphaForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>">
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
            <input type="text" name="search" id="projectSearch" class="form-control" style="max-width:1300px;" placeholder="Search by name, code, client, address, or status..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <div class="table-responsive bg-white rounded shadow-sm p-4" style="min-height:500px;">
        <table class="table table-bordered table-striped align-middle mb-0">
            <thead>
                <tr>
                    <!-- <th>ID</th> -->
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
                            <!-- <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['id']); ?></td> -->
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['name']); ?></td>
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['project_code']); ?></td>
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['client']); ?></td>
                            <td style="word-break:break-word;max-width:250px;white-space:pre-line;"><?php echo htmlspecialchars($project['address']); ?></td>
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars(number_format((float)$project['paysheet_value'], 2)); ?></td>
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['start_date']); ?></td>
                            <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['status']); ?></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle w-100" type="button" id="manageDropdown<?php echo $project['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-gear"></i> Manage
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="manageDropdown<?php echo $project['id']; ?>">
                                        <li><a class="dropdown-item" href="<?php echo site_url('project/view/' . $project['id']); ?>"><i class="bi bi-eye"></i> View</a></li>
                                        <?php if (function_exists('is_admin') && is_admin()): ?>
                                            <li><a class="dropdown-item" href="<?php echo site_url('project/edit/' . $project['id']); ?>"><i class="bi bi-pencil-square"></i> Edit</a></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="showDeleteModal(<?php echo $project['id']; ?>); return false;"><i class="bi bi-trash"></i> Delete</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <!-- Delete Confirmation Modal (one per row, unique id) -->
                                <div class="modal fade" id="deleteModal<?php echo $project['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $project['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel<?php echo $project['id']; ?>">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this project?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <a id="deleteConfirmBtn<?php echo $project['id']; ?>" href="<?php echo site_url('project/delete/' . $project['id']); ?>" class="btn btn-danger">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                function showDeleteModal(projectId) {
                                    var modal = new bootstrap.Modal(document.getElementById('deleteModal' + projectId));
                                    modal.show();
                                }
                                </script>
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
                <?php
                // Build base query string for pagination links, preserving filters
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
.dropdown-menu .dropdown-item:hover, .dropdown-menu .dropdown-item:focus {
    background: #f5f5f7;
    box-shadow: 0 4px 18px 0 rgba(100,100,100,0.25), 0 1.5px 4px 0 rgba(0,0,0,0.10);
    z-index: 2;
}
.dropdown-menu .dropdown-item i {
    margin-right: 8px;
    font-size: 1.2em;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Date range filter: submit form on button click, preserving search
document.querySelectorAll('.filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const range = btn.getAttribute('data-range');
        // If search form exists, submit with search value
        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.querySelector('input[name="range"]').value = range;
            searchForm.submit();
        } else {
            document.getElementById('rangeInput').value = range;
            document.getElementById('dateRangeForm').submit();
        }
    });
});
</script>
</html>
