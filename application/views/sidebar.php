<style>
    .sidebar {
        height: 100vh;
        background: #343a40;
        color: #fff;
        padding-top: 30px;
        width: 220px;
        display: flex;
        flex-direction: column;
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
    .sidebar .logout-link {
        margin-top: auto;
        margin-bottom: 20px;
    }
</style>
<div class="sidebar position-fixed">
    <h4 class="text-center mb-4">CanoAccounts</h4>
    <a href="<?php echo site_url('home'); ?>" class="<?php echo (uri_string() == 'home') ? 'active' : ''; ?>">Home</a>
    <a href="<?php echo site_url('project/add'); ?>" class="<?php echo (uri_string() == 'project/add') ? 'active' : ''; ?>">Add Projects</a>
    <a href="<?php echo site_url('project/list'); ?>" class="<?php echo (uri_string() == 'project/list') ? 'active' : ''; ?>">List Projects</a>
    <a href="<?php echo site_url('invoice/add_invoice'); ?>" class="<?php echo (uri_string() == 'invoice/add_invoice') ? 'active' : ''; ?>">Add Invoice</a>
    <a href="<?php echo site_url('invoice/list'); ?>" class="<?php echo (uri_string() == 'invoice/list') ? 'active' : ''; ?>">List Invoice</a>
    <a href="<?php echo site_url('quote/add'); ?>" class="<?php echo (uri_string() == 'quote/add') ? 'active' : ''; ?>">Add Quotation</a>
    <a href="<?php echo site_url('quote/list'); ?>" class="<?php echo (uri_string() == 'quote/list') ? 'active' : ''; ?>">List Quotation</a>
    <a href="<?php echo site_url('expense/add'); ?>" class="<?php echo (uri_string() == 'expense/add') ? 'active' : ''; ?>">Add Expenses</a>
    <a href="<?php echo site_url('expense/list_expenses'); ?>" class="<?php echo (uri_string() == 'expense/list_expenses') ? 'active' : ''; ?>">List Expenses</a>
    <a href="<?php echo site_url('auth/logout'); ?>" class="logout-link">Logout</a>
</div>
