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
        <h2>Welcome to the CanoAccounts</h2>
        <p>Select an option from the sidebar to get started.</p>
    </div>
</div>
</body>
</html>
