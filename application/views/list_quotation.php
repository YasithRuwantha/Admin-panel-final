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
                        <th></th>
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
                                            <?php echo htmlspecialchars(number_format($item['amount'], 2)); ?><br>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars(number_format($quote['amount'], 2)); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="word-break:break-word;max-width:180px;white-space:pre-line; font-weight:bold;"> <?php echo htmlspecialchars(number_format($quote['amount'], 2)); ?> </td>
                                <td style="word-break:break-word;max-width:120px;white-space:pre-line;">
                                    <a href="<?php echo site_url('quote/edit/' . $quote['id']); ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Show Quotation</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center">No quotations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
