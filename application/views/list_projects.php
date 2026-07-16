<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Projects</title>

    <!-- Essential viewport for mobile responsiveness -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #f8f9fa;
            overflow-x: hidden;
        }

        /* Main content has sidebar margin on desktop */
        .container-fluid {
            margin-left: 220px;
            padding: 40px 20px;
            transition: margin-left 0.3s ease;
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .container-fluid {
                margin-left: 0 !important;
                padding: 20px 15px;
            }

            /* Make search input full width on mobile */
            #projectSearch {
                max-width: 100% !important;
                width: 100%;
            }

            /* Table improvements for mobile */
            .table-responsive {
                font-size: 0.875rem;
            }

            /* Give columns enough space and prevent wrapping issues */
            th:nth-child(1), td:nth-child(1) { min-width: 160px; } /* Project Name */
            th:nth-child(2), td:nth-child(2) { min-width: 120px; } /* Code */
            th:nth-child(3), td:nth-child(3) { min-width: 140px; } /* Client */
            th:nth-child(4), td:nth-child(4) { min-width: 200px; } /* Address */
            th:nth-child(5), td:nth-child(5) { min-width: 120px; } /* Value */
            th:nth-child(6), td:nth-child(6) { min-width: 110px; } /* Start Date */
            th, td { white-space: nowrap; }




            /* Stack filters and controls vertically on mobile */
            .d-flex.flex-wrap {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 1rem !important;
            }

            /* Make dropdowns and selects full width */
            .form-select {
                width: 100% !important;
            }

            /* Add buttons full width on mobile */
            .btn {
                width: 100%;
                justify-content: center;
            }

            /* Date buttons wrap naturally */
            #dateRangeForm {
                flex-direction: column !important;
                align-items: flex-start !important;
            }

            #dateRangeForm .btn {
                width: auto;
                min-width: 100px;
            }
        }

        @media (min-width: 769px) {
            /* Desktop: keep horizontal layout for filters */
            .d-flex.flex-wrap {
                flex-direction: row !important;
            }
        }

        /* Manage dropdown — compact & responsive */
        .manage-btn {
            font-size: 1rem;
            padding: 0.3rem 0.7rem;
            white-space: nowrap;
        }
        .dropdown-menu {
            font-size: 1rem;
            padding: 0.35rem 0;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            min-width: 160px;
        }
        .dropdown-menu .dropdown-item {
            padding: 0.4rem 1rem;
            font-weight: 500;
            transition: box-shadow 0.15s, background 0.15s;
        }
        .dropdown-menu .dropdown-item:hover, .dropdown-menu .dropdown-item:focus {
            background: #f5f5f7;
            box-shadow: 0 2px 10px 0 rgba(100,100,100,0.18);
            z-index: 2;
        }
        .dropdown-menu .dropdown-item i {
            margin-right: 6px;
            font-size: 1em;
        }
        @media (max-width: 768px) {
            .manage-btn { width: 100%; }
        }

        /* Allow dropdown to escape the table-responsive container on desktop */
        @media (min-width: 769px) {
            .table-responsive {
                overflow: visible !important;
            }
        }

        /* 2-column grid panel inside dropdown */
        .manage-dropdown-panel {
            min-width: 330px;
            padding: 0.5rem 0.25rem;
        }
        .manage-dropdown-panel .panel-top-row {
            display: flex;
            flex-direction: column;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 0.25rem;
            padding-bottom: 0.25rem;
        }
        .manage-dropdown-panel .panel-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }
        .manage-dropdown-panel .panel-col-header {
            font-size: 0.7rem;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.3rem 1rem 0.1rem;
        }
        .manage-dropdown-panel .panel-col:first-child {
            border-right: 1px solid #e9ecef;
        }
        .manage-dropdown-panel .panel-admin-row {
            border-top: 1px solid #e9ecef;
            margin-top: 0.25rem;
            padding-top: 0.25rem;
            display: flex;
            flex-direction: column;
        }

        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>

<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container-fluid mt-4 px-4">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
            <h2 class="mb-0">List Projects</h2>
            <a href="<?php echo site_url('project/add'); ?>" class="btn btn-primary" style="min-width:150px; font-weight:500; font-size:1.1rem;">+ Add Project</a>
        </div>

        <!-- Date Range Filter Buttons as Form (Home page style) -->
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
        <style>
        /* Date filter buttons - smaller and wrap naturally (from home.php) */
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

        <!-- Sort by Project Name and Number of Rows (side by side, mobile responsive) -->
        <form id="alphaForm" method="get" class="mb-3">
            <div class="filters-upper-row-vertical">
                <div class="mb-2">
                    <label class="fw-semibold me-2 mb-0">Status:</label>
                    <select name="status_filter" class="form-select form-select-sm" style="width:auto;min-width:120px;max-width:300px;display:inline-block;" onchange="this.form.submit();">
                        <option value=""<?php echo (!isset($status_filter) || $status_filter === '') ? ' selected' : ''; ?>>All</option>
                        <option value="Planned"<?php echo (isset($status_filter) && $status_filter === 'Planned') ? ' selected' : ''; ?>>Planned</option>
                        <option value="Ongoing"<?php echo (isset($status_filter) && $status_filter === 'Ongoing') ? ' selected' : ''; ?>>Ongoing</option>
                        <option value="Completed"<?php echo (isset($status_filter) && $status_filter === 'Completed') ? ' selected' : ''; ?>>Completed</option>
                        <option value="On Hold"<?php echo (isset($status_filter) && $status_filter === 'On Hold') ? ' selected' : ''; ?>>On Hold</option>
                        <option value="Cancelled"<?php echo (isset($status_filter) && $status_filter === 'Cancelled') ? ' selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label for="perPageSelect" class="fw-semibold me-2 mb-0">Number of rows:</label>
                    <select name="per_page" id="perPageSelect" class="form-select form-select-sm" style="width:auto;min-width:100px;max-width:200px;display:inline-block;" onchange="this.form.submit();">
                        <option value="10"<?php echo (!isset($per_page) || $per_page == 10) ? ' selected' : ''; ?>>10</option>
                        <option value="25"<?php echo (isset($per_page) && $per_page == 25) ? ' selected' : ''; ?>>25</option>
                        <option value="50"<?php echo (isset($per_page) && $per_page == 50) ? ' selected' : ''; ?>>50</option>
                        <option value="100"<?php echo (isset($per_page) && $per_page == 100) ? ' selected' : ''; ?>>100</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="fw-semibold me-2 mb-0">Sort by Project Name:</label>
                    <select name="alpha" class="form-select form-select-sm" style="width:auto;min-width:120px;max-width:300px;display:inline-block;" onchange="this.form.submit();">
                        <option value="recent"<?php echo (!isset($alpha) || $alpha === 'recent') ? ' selected' : ''; ?>>Recent</option>
                        <option value="az"<?php echo (isset($alpha) && $alpha === 'az') ? ' selected' : ''; ?>>A-Z</option>
                        <option value="za"<?php echo (isset($alpha) && $alpha === 'za') ? ' selected' : ''; ?>>Z-A</option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>">
        </form>
        <style>
        .filters-upper-row-vertical > div {
            width: 100%;
            max-width: 400px;
        }
        .filters-upper-row-vertical {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        @media (max-width: 768px) {
            .filters-upper-row-vertical {
                flex-direction: column;
                align-items: stretch;
            }
            .filters-upper-row-vertical > div {
                max-width: 100%;
            }
        }
        </style>

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
                        <th>Project Name</th>
                        <th>Project Code</th>
                        <th>Client Name</th>
                        <th>Address</th>
                        <th>Project Value</th>
                        <th>Start Date</th>
                        <th>Status</th>
                        <th style="width:110px;">Actions</th>                   
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($projects)): ?>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['name']); ?></td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['project_code']); ?></td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['client']); ?></td>
                                <td style="word-break:break-word;max-width:250px;white-space:pre-line;"><?php echo htmlspecialchars($project['address']); ?></td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars(number_format((float)$project['paysheet_value'], 2)); ?></td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['start_date']); ?></td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;"><?php echo htmlspecialchars($project['status']); ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle manage-btn" type="button" id="manageDropdown<?php echo $project['id']; ?>" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-flip="true" aria-expanded="false">
                                            <i class="bi bi-gear"></i> Manage
                                        </button>
                                        <ul class="dropdown-menu manage-dropdown-panel" aria-labelledby="manageDropdown<?php echo $project['id']; ?>">
                                            <!-- Top row: View + Upload -->
                                            <div class="panel-top-row">
                                                <a class="dropdown-item" href="<?php echo site_url('project/view/' . $project['id']); ?>"><i class="bi bi-eye"></i> View Project</a>
                                                <a class="dropdown-item" href="#" onclick="showUploadModal(<?php echo $project['id']; ?>); return false;"><i class="bi bi-upload"></i> Upload Docs</a>
                                            </div>
                                            <!-- 2-column grid: Add | List -->
                                            <div class="panel-grid">
                                                <div class="panel-col">
                                                    <div class="panel-col-header">Add</div>
                                                    <a class="dropdown-item" href="<?php echo site_url('invoice/add_invoice') . '?project_name=' . urlencode($project['name']) . '&project_code=' . urlencode($project['project_code']) . '&client=' . urlencode($project['client']) . '&address=' . urlencode($project['address']); ?>"><i class="bi bi-file-earmark-plus"></i> Invoice</a>
                                                    <a class="dropdown-item" href="<?php echo site_url('quote/add') . '?project_name=' . urlencode($project['name']) . '&project_code=' . urlencode($project['project_code']) . '&client=' . urlencode($project['client']) . '&address=' . urlencode($project['address']); ?>"><i class="bi bi-file-earmark-text"></i> Quotation</a>
                                                    <a class="dropdown-item" href="<?php echo site_url('expense/add') . '?project_name=' . urlencode($project['name']) . '&project_code=' . urlencode($project['project_code']); ?>"><i class="bi bi-cash-coin"></i> Expense</a>
                                                </div>
                                                <div class="panel-col">
                                                    <div class="panel-col-header">View List</div>
                                                    <a class="dropdown-item" href="<?php echo site_url('invoice/list') . '?exact_project_code=' . urlencode($project['project_code']); ?>"><i class="bi bi-list-ul"></i> Invoices</a>
                                                    <a class="dropdown-item" href="<?php echo site_url('quote/list') . '?exact_project_code=' . urlencode($project['project_code']); ?>"><i class="bi bi-list-ul"></i> Quotations</a>
                                                    <a class="dropdown-item" href="<?php echo site_url('expense/list_expenses') . '?exact_project_code=' . urlencode($project['project_code']); ?>"><i class="bi bi-list-ul"></i> Expenses</a>
                                                </div>
                                            </div>
                                            <!-- Admin row: Edit + Delete -->
                                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                            <div class="panel-admin-row">
                                                <a class="dropdown-item" href="<?php echo site_url('project/edit/' . $project['id']); ?>"><i class="bi bi-pencil-square"></i> Edit</a>
                                                <a class="dropdown-item text-danger" href="#" onclick="showDeleteModal(<?php echo $project['id']; ?>); return false;"><i class="bi bi-trash"></i> Delete</a>
                                            </div>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <!-- Upload Documents Modal -->
                                    <div class="modal fade" id="uploadModal<?php echo $project['id']; ?>" tabindex="-1" aria-labelledby="uploadModalLabel<?php echo $project['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form id="uploadForm<?php echo $project['id']; ?>" action="<?php echo site_url('project/upload_documents/' . $project['id']); ?>" method="post" enctype="multipart/form-data">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="uploadModalLabel<?php echo $project['id']; ?>">Upload Documents</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="fileInputs<?php echo $project['id']; ?>">
                                                            <div class="mb-3 file-input-group">
                                                                <label class="form-label">Select Document</label>
                                                                <input class="form-control" type="file" name="documents[]" multiple required>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addFileInput<?php echo $project['id']; ?>()">+ Add Another Document</button>
                                                        <small class="text-muted d-block mt-2">You can add multiple files. Any file type is allowed.</small>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Upload</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                    // Add another file input dynamically, keep previous selections
                                    function addFileInput<?php echo $project['id']; ?>() {
                                        var container = document.getElementById('fileInputs<?php echo $project['id']; ?>');
                                        var div = document.createElement('div');
                                        div.className = 'mb-3 file-input-group';
                                        div.innerHTML = '<input class="form-control" type="file" name="documents[]" multiple required>';
                                        container.appendChild(div);
                                    }
                                    </script>
                                    <!-- Delete Confirmation Modal -->
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
                                    function showUploadModal(projectId) {
                                        var modal = new bootstrap.Modal(document.getElementById('uploadModal' + projectId));
                                        modal.show();
                                    }
                                    function showDeleteModal(projectId) {
                                        var modal = new bootstrap.Modal(document.getElementById('deleteModal' + projectId));
                                        modal.show();
                                    }
                                    </script>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center">No projects found.</td></tr>
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
                    'per_page' => htmlspecialchars($per_page ?? 10),
                    'status_filter' => isset($status_filter) ? htmlspecialchars($status_filter) : ''
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
// Date range filter: submit form on button click, preserving search
document.querySelectorAll('.filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const range = btn.getAttribute('data-range');
        // Submit the search form if it exists (to preserve search term)
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
</body>
</html>
