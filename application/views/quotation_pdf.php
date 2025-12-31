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
            font-size: 30px;
            font-weight: bold;
            color: #000;
            margin: 0;
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
        
        .client-section {
            margin-bottom: 20px;
        }
        
        .client-section .label {
            font-weight: bold;
            margin-bottom: 3px;
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
        }
        
        .total-row {
            font-weight: bold;
            font-size: 14px;
            padding: 10px 0;
        }
        
        .total-underline {
            text-decoration: underline;
        }
        
        .signature-section {
            margin: 30px 0 20px 0;
            text-align: left;
            padding-left: 0;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin: 20px 0 8px 0;
            height: 1px;
        }
        
        .signature-name {
            font-size: 13px;
            font-weight: bold;
            margin: 3px 0;
        }
        
        /* .signature-title {
            font-size: 9px;
            margin: 1px 0;
        } */
        
        .footer-sections {
            margin-top: 15px;
            font-size: 10px;
            margin-bottom: 60px;
        }
        
        .note-section {
            margin: 8px 0;
			font-size: 12px;
        }
        
        .payment-terms {
            margin: 8px 0;
        }
        
        .payment-terms h4 {
            font-size: 12px;
            margin-bottom: 3px;
            font-weight: bold;
        }
        
        .payment-terms ul {
            margin: 3px 0;
            padding-left: 15px;
        }
        
        .payment-terms li {
            margin: 1px 0;
			font-size: 11px;
        }
        
        .declaration {
            margin: 8px 0;
            font-size: 12px;
        }
        
        .company-footer {
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
        
        .highlight {
            font-weight: bold;
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
            <h1 class="title">Quotation</h1>
        </div>
    </div>

    <div class="details-section">
        <div class="left-details">
            <div class="client-section">
                <div class="label">TO:</div>
                <div><?php echo htmlspecialchars($quote['name']); ?></div>
                <div><?php echo nl2br(htmlspecialchars($quote['address'])); ?></div>
            </div>
        </div>
        
        <div class="right-details">
            <div class="detail-row">
                <span class="detail-label">QUOTATION NO:</span>
                <?php echo htmlspecialchars($quote['quotation_no']); ?>
            </div>
            <div class="detail-row">
                <span class="detail-label">DATE OF ISSUE:</span>
                <?php echo date('jS F Y', strtotime($quote['quote_date'])); ?>
            </div>
            <div class="detail-row">
                <span class="detail-label">CONTRACT NO:</span>
                <?php echo htmlspecialchars($quote['project_code']); ?>
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
                foreach ($quote['items'] as $item): 
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

    <div class="footer-sections">
        <div class="note-section">
            <span class="highlight">Note:</span> The offer valid only 30 Days from the date. <span class="highlight">This Quotation did not include any statutory fees enforced by relevant organizations. </span>
        </div>

        <div class="payment-terms">
            <h4>Payment Terms:</h4>
            <ul>
                <li>Advance Payment for commencement of the assignment: 50%</li>
                <li>On submission of Draft Report: 30%</li>
                <li>After the Final Report: 20%</li>
            </ul>
        </div>
<br>
        <div class="declaration">
            <span class="highlight">Beneficiary: CANOPUS PVT LTD | A/C No: 284100190049754 | Bank: People’s Bank (Kannathiddy Branch)<br>
            Payment Reference:  Please include the Invoice Number as a reference for all bank transfers.</span>
        </div>

        <div class="company-footer">
            <span class="highlight">CANOPUS (PRIVATE) LIMITED</span> | Company No. PV177771<br>
            Sangaripillai Road, Manipay, Jaffna.<br>
            +94 21 22 6415 | info@canopus.lk | www.canopus.lk
        </div>
    </div>
</body>
</html>
