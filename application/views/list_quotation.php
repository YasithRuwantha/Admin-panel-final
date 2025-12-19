<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Quotation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="d-flex">
    <?php $this->load->view('sidebar'); ?>
    <div class="container mt-5" style="margin-left:220px;">
        <h2>List Quotation</h2>
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
            <label class="fw-semibold me-2">Sort by Name:</label>
            <select name="alpha" class="form-select form-select-sm" style="width:auto;" onchange="document.getElementById('alphaForm').submit();">
                <option value="recent"<?php echo (!isset($alpha) || $alpha === 'recent') ? ' selected' : ''; ?>>Recent</option>
                <option value="az"<?php echo (isset($alpha) && $alpha === 'az') ? ' selected' : ''; ?>>A-Z</option>
                <option value="za"<?php echo (isset($alpha) && $alpha === 'za') ? ' selected' : ''; ?>>Z-A</option>
            </select>
        </form>

        <!-- Search Bar -->
        <form id="searchForm" method="get" class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="range" value="<?php echo htmlspecialchars($selected_range ?? 'all'); ?>">
            <input type="text" name="search" id="quotationSearch" class="form-control" style="max-width:1212px;" placeholder="Search by name, quotation no, address, project code, or total..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
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
                        <th>Name</th>
                        <th>Quotation No</th>
                        <th>Address</th>
                        <th>Date</th>
                        <th>Project Code</th>
                        <!-- <th>Service Description</th> -->
                        <th>Amount</th>
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
                                <!-- <td style="word-break:break-word;max-width:180px;white-space:pre-line;">
                                    <?php if (!empty($quote['items'])): ?>
                                        <?php foreach ($quote['items'] as $item): ?>
                                            <?php echo htmlspecialchars($item['description']); ?><br>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($quote['description']); ?>
                                    <?php endif; ?>
                                </td> -->
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line;">
                                    <?php if (!empty($quote['items'])): ?>
                                        <?php foreach ($quote['items'] as $item): ?>
                                            <?php echo htmlspecialchars(number_format((float)$item['amount'], 2)); ?><br>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars(number_format((float)$quote['amount'], 2)); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line; font-weight:bold;"> <?php echo htmlspecialchars(number_format((float)$quote['amount'], 2)); ?> </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle w-100" type="button" id="manageDropdown<?php echo $quote['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-gear"></i> Manage
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="manageDropdown<?php echo $quote['id']; ?>">
                                            <li><a class="dropdown-item" href="<?php echo site_url('quote/view/' . $quote['id']); ?>"><i class="bi bi-eye"></i> View</a></li>
                                            <li><a class="dropdown-item" href="<?php echo site_url('quote/export_quote/' . $quote['id']); ?>"><i class="bi bi-download"></i> Export</a></li>
                                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                                <li><a class="dropdown-item" href="<?php echo site_url('quote/edit/' . $quote['id']); ?>"><i class="bi bi-pencil-square"></i> Edit</a></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="showDeleteModal(<?php echo $quote['id']; ?>); return false;"><i class="bi bi-trash"></i> Delete</a></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item text-danger fw-bold d-flex align-items-center" href="<?php echo site_url('quote/pdf/' . $quote['id']); ?>" target="_blank"><i class="bi bi-file-earmark-pdf"></i> PDF</a></li>
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
                        <tr><td colspan="8" class="text-center">No quotations found.</td></tr>
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
