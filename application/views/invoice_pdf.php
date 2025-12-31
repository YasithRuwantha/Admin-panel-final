<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
            color: #000;
            font-size: 11px;
            line-height: 1.2;
            box-sizing: border-box;
        }
        
        @page {
            size: A4;
            margin: 15mm;
        }
        
        * {
            box-sizing: border-box;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .keep-together {
            page-break-inside: avoid;
        }
        

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .title {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin: 0;
        }
        
        .details-section {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        
        .left-details {
            width: 60%;
        }
        
        .right-details {
            position: absolute;
            right: 0;
            top: 120px;
            width: 350px;
            text-align: left;
        }
        
        .client-section {
            margin-bottom: 20px;
        }
        
        .client-section .label {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .detail-row {
            margin: 6px 0;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .detail-label {
            font-weight: bold;
            margin-right: 20px;
        }
        
        .logo {
            width: 220px;
            height: auto;
            margin-left: auto;
        }
        
        .logo-container {
            text-align: right;
        }
        
        .main-content {
            margin: 20px 0;
        }
        
        .items-section {
            margin: 20px 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }
        
        .items-table thead {
            page-break-inside: avoid;
            page-break-after: avoid;
        }
        
        .items-table tr {
            page-break-inside: avoid;
        }
        
        .items-table th {
            background-color: #f0f0f0;
            padding: 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ccc;
            font-size: 11px;
        }
        
        .items-table td {
            padding: 4px 6px;
            border: 1px solid #ccc;
            font-size: 10px;
            vertical-align: top;
        }
        
        .amount-column {
            text-align: right;
            width: 100px;
        }
        
        .total-section {
            margin: 20px 0;
            text-align: right;
            page-break-inside: avoid;
        }
        
        .total-row {
            font-weight: bold;
            font-size: 14px;
            padding: 10px 0;
        }
        
        .highlight {
            font-weight: bold;
        }
        
        .total-underline {
            text-decoration: underline;
        }
        
        .payment-section {
            margin: 15px 0;
            background-color: #f8f9fa;
            padding: 12px;
            left: 5;
			text-align: left;
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        
        .payment-section h3 {
			font-size: 12px;
            color: #000000ff;
			font-weight: bold;
            margin: 0 0 8px 0;
        }
        
        .payment-details {
            margin: 6px 0;
            padding: 6px;
            background-color: #ffffff;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
            page-break-inside: avoid;
        }
        
        .info-row {
            margin: 4px 0;
            display: flex;
            font-size: 10px;
        }
        
        .info-row label {
            font-weight: bold;
            width: 80px;
            margin-right: 8px;
        }
        
        .info-row span {
            flex: 1;
        }


		.declaration {
            margin: 8px 0;
            font-size: 12px;
			text-align: center;
        }


		.signature-section {
            margin: 15px 0 10px 0;
            text-align: right;
            padding-left: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            page-break-inside: avoid;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin: 15px 0 6px 0;
            height: 1px;
        }
        
        .signature-name {
            font-size: 11px;
            font-weight: bold;
            margin: 2px 0;
            text-align: center;
            width: 200px;
        }
        
        .footer {
            position: absolute;
            bottom: 5mm;
            left: 5mm;
            font-size: 13px;
            color: #000000ff;
            text-align: left;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            width: auto;
        }
        
        .amount {
            text-align: right;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-partial {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-container">
            <?php
            $logo_path = FCPATH . 'assets/images/logo.png';
            if (file_exists($logo_path)) {
                $logo_data = base64_encode(file_get_contents($logo_path));
                echo '<img src="data:image/png;base64,' . $logo_data . '" alt="Canopus Logo" class="logo">';
            } else {
                echo '<div style="width:220px; height:70px; background:#4a9b8e; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:20px;">canopus</div>';
            }
            ?>
        </div>
		<div>
            <h1 class="title">Invoice</h1>
        </div>
    </div>

    <div class="details-section">
        <div class="left-details">
            <div class="client-section">
                <div class="label">TO:</div>
                <div><?php echo htmlspecialchars($invoice['name']); ?></div>
                <div><?php echo nl2br(htmlspecialchars($invoice['address'])); ?></div>
            </div>
        </div>
        
        <div class="right-details">
            <div class="detail-row">
                <span class="detail-label">INVOICE NO:</span>
                <?php echo htmlspecialchars($invoice['invoice_no']); ?>
            </div>
            <div class="detail-row">
                <span class="detail-label">DATE OF ISSUE:</span>
                <?php echo date('jS F Y', strtotime($invoice['invoice_date'])); ?>
            </div>
            <div class="detail-row">
                <span class="detail-label">PROJECT CODE:</span>
                <?php echo htmlspecialchars($invoice['project_code']); ?>
            </div>
        </div>
    </div>

    <div class="items-section">
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 70%;">DESCRIPTION</th>
                    <th class="amount-column">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                $counter = 1;
                foreach ($invoice['items'] as $item): 
                    $total += $item['amount'];
                ?>
                <tr>
                    <td>
                        <?php echo $counter . '. ' . htmlspecialchars($item['description']); ?>
                    </td>
                    <td class="amount-column">
                        <?php echo number_format($item['amount'], 2); ?>
                    </td>
                </tr>
                <?php 
                    $counter++;
                endforeach; 
                ?>
            </tbody>
        </table>
        
        <div class="total-section">
            <div class="total-row">
                <span style="margin-right: 50px;">TOTAL</span>
                <span class="highlight total-underline">Rs. <?php echo number_format($total, 2); ?></span>
            </div>
        </div>
    </div>


    <?php 
    $total_paid = 0;
    if (!empty($invoice['payments'])) {
        foreach ($invoice['payments'] as $payment) {
            $total_paid += $payment['payment_amount'];
        }
    }
    ?>
    <div class="payment-section">
        <h3>Payment History</h3>
        <?php if (!empty($invoice['payments'])): ?>
            <?php foreach ($invoice['payments'] as $payment): ?>
            <div class="payment-details">
                <div class="info-row">
                    <label>Amount:</label>
                    <span><?php echo number_format($payment['payment_amount'], 2); ?></span>
                </div>
                <div class="info-row">
                    <label>Date:</label>
                    <span><?php echo date('d/m/Y', strtotime($payment['payment_date'])); ?></span>
                </div>
                <div class="info-row">
                    <label>Method:</label>
                    <span><?php echo htmlspecialchars($payment['payment_mode']); ?></span>
                </div>
                <?php if (!empty($payment['reference_no'])): ?>
                <div class="info-row">
                    <label>Reference:</label>
                    <span><?php echo htmlspecialchars($payment['reference_no']); ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($payment['remarks'])): ?>
                <div class="info-row">
                    <label>Remarks:</label>
                    <span><?php echo htmlspecialchars($payment['remarks']); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="payment-details">
                <div class="info-row">
                    <label>Status:</label>
                    <span><span class="status-badge status-pending">Pending</span></span>
                </div>
                <div class="info-row">
                    <label>Total Paid:</label>
                    <span>Rs. 0.00</span>
                </div>
                <div class="info-row">
                    <label>Balance Due:</label>
                    <span>Rs. <?php echo number_format($total, 2); ?></span>
                </div>
            </div>
        <?php endif; ?>
        <div style="margin-top: 12px; font-size: 12px; margin-bottom: 20px;">
            <strong>Total Paid: Rs. <?php echo number_format($total_paid, 2); ?></strong>
            <br>
            <strong>Balance Due: Rs. <?php echo number_format($total - $total_paid, 2); ?></strong>
            <?php 
            if ($total_paid == 0) {
                $status = '<span class="status-badge status-pending">Pending</span>';
            } elseif ($total_paid < $total) {
                $status = '<span class="status-badge status-partial">Partially Paid</span>';
            } else {
                $status = '<span class="status-badge status-paid">Paid</span>';
            }
            ?>
            <br>
            Status: <?php echo $status; ?>
			<br><br>
            <div class="declaration">
            <span class="highlight">Beneficiary: CANOPUS PVT LTD | A/C No: 284100190049754 | Bank: People’s Bank (Kannathiddy Branch)<br>
            Payment Reference:  Please include the Invoice Number as a reference for all bank transfers.</span>
        </div>
    </div>
	<br><br>
    <div class="signature-section">
        <?php 
        // Show signature image if $show_signature is true (set from controller based on tick box)
        if (isset($show_signature) && $show_signature) {
            $signature_path = FCPATH . 'assets/images/Signature.png';
            if (file_exists($signature_path)) {
                $signature_data = base64_encode(file_get_contents($signature_path));
                echo '<div style="text-align:left; margin-left:20px; margin-bottom:-10px;">'
                    . '<img src="data:image/png;base64,' . $signature_data . '" alt="Signature" style="width:190px;">'
                    . '</div>';
            }
        }
        ?>
        <div class="signature-line"></div>
        <div class="signature-name">Stamp & Signature</div>
    </div>

    <div class="footer">
        <span class="highlight">CANOPUS (PRIVATE) LIMITED</span> | Company No. PV177771<br>
        Sangaripillai Road, Manipay, Jaffna.<br>
        +94 21 22 6415 | info@canopus.lk | www.canopus.lk
    </div>
</body>
</html>
