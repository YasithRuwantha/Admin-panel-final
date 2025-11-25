<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - Admin Panel</title>
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
    <div class="sidebar position-fixed" style="width:220px;">
        <h4 class="text-center mb-4">Admin Panel</h4>
        <a href="<?php echo site_url('home'); ?>" class="active">Home</a>
        <a href="#">Add Projects</a>
        <a href="#">List Projects</a>
        <a href="#">Add Invoice</a>
        <a href="#">List Invoice</a>
        <a href="#">Add Quotation</a>
        <a href="#">List Quotation</a>
        <a href="#">Add Expenses</a>
        <a href="#">List Expenses</a>
        <a href="<?php echo site_url('auth/logout'); ?>">Logout</a>
    </div>
    <div class="main-content flex-grow-1">
        <h2>Welcome to the Admin Panel</h2>
        <p>Select an option from the sidebar to get started.</p>
    </div>
</div>
</body>
</html>
