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
        <div class="table-responsive">
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
                                    <div class="d-flex flex-column gap-2 align-items-start">
                                        <a href="<?php echo site_url('quote/view/' . $quote['id']); ?>" class="btn btn-primary w-100" style="min-width:70px;"><i class="bi bi-eye"></i> View</a>
                                        <?php if (function_exists('is_admin') && is_admin()): ?>
                                            <a href="<?php echo site_url('quote/edit/' . $quote['id']); ?>" class="btn btn-warning w-100" style="min-width:70px;"><i class="bi bi-pencil-square"></i> Edit</a>
                                            <button type="button" class="btn btn-danger w-100" style="min-width:70px;" onclick="showDeleteModal(<?php echo $quote['id']; ?>)"><i class="bi bi-trash"></i> Delete</button>
                                        <!-- Delete Confirmation Modal -->
                                        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete this quotation?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <a id="deleteConfirmBtn" href="#" class="btn btn-danger">Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                                        <script>
                                        function showDeleteModal(quoteId) {
                                                var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                                                var btn = document.getElementById('deleteConfirmBtn');
                                                btn.href = '<?php echo site_url('quote/delete/'); ?>' + quoteId;
                                                modal.show();
                                        }
                                        </script>
                                        <?php endif; ?>
                                        <a href="<?php echo site_url('quote/pdf/' . $quote['id']); ?>" class="btn btn-danger fw-bold d-inline-flex align-items-center justify-content-center w-100" style="gap:4px; min-width:90px; height:32px; font-size:0.9rem;" target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
                                                <path d="M5.5 7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H5v1h.5a.5.5 0 0 1 0 1H5v1a.5.5 0 0 1-1 0v-4a.5.5 0 0 1 .5-.5h1zm2.5.5a.5.5 0 0 1 .5-.5h.5v4a.5.5 0 0 1-1 0v-4zm2.5-.5a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 1 .5-.5z"/>
                                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h6.5L14 4.5zm-3-2.5H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V5h-3a1 1 0 0 1-1-1V2z"/>
                                            </svg>
                                            <span>PDF</span>
                                        </a>
                                    </div>
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
</html>
