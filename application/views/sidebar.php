<style>
    .sidebar {
        height: 100vh;
        background: #343a40;
        color: #fff;
        padding-top: 30px;
        width: 220px;
    }
    .sidebar a {
        color: #fff;
        text-decoration: none;
        display: block;
        padding: 12px 24px;
    }
    .sidebar a:hover {
        background: #495057;
    }
    .sidebar .active {
        background: #007bff;
    }
</style>
<div class="sidebar position-fixed">
    <h4 class="text-center mb-4">Admin Panel</h4>
    <a href="<?php echo site_url('home'); ?>" class="<?php echo (uri_string() == 'home') ? 'active' : ''; ?>">Home</a>
    <a href="<?php echo site_url('project/add'); ?>" class="<?php echo (uri_string() == 'project/add') ? 'active' : ''; ?>">Add Projects</a>
    <a href="<?php echo site_url('project/list'); ?>" class="<?php echo (uri_string() == 'project/list') ? 'active' : ''; ?>">List Projects</a>
    <a href="#">Add Invoice</a>
    <a href="#">List Invoice</a>
    <a href="#">Add Quotation</a>
    <a href="#">List Quotation</a>
    <a href="#">Add Expenses</a>
    <a href="#">List Expenses</a>
    <a href="<?php echo site_url('auth/logout'); ?>">Logout</a>
</div>
