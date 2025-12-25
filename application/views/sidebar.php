
<style>
    body {
        margin: 0;
        padding: 0;
    }

    /* Sidebar */
    .sidebar {
        height: 100vh;
        background: #343a40;
        color: #fff;
        padding-top: 30px;
        width: 220px;
        display: flex;
        flex-direction: column;
        position: fixed;
        left: 0;
        top: 0;
        transition: transform 0.3s ease;
        z-index: 1050;
        overflow-y: auto;
    }

    .sidebar a {
        color: #fff;
        text-decoration: none;
        display: block;
        padding: 12px 24px;
        font-size: 1rem;
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

    .sidebar h4 {
        padding: 0 24px;
        font-weight: 600;
    }

    /* Hamburger - made smaller and less intrusive */
    .hamburger {
        display: none;
        position: fixed;
        top: 12px;
        left: 12px;
        font-size: 20px;           /* Smaller icon */
        color: #fff;
        background: rgba(52, 58, 64, 0.85);
        border: none;
        width: 36px;               /* Fixed small width */
        height: 36px;              /* Fixed small height */
        padding: 0;
        border-radius: 6px;
        z-index: 1100;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        cursor: pointer;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .hamburger:hover {
        background: #343a40;
    }

    /* Overlay when sidebar is open on mobile */
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1040;
    }

    /* Mobile */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar.show ~ .sidebar-overlay {
            display: block;
        }

        .hamburger {
            display: flex; /* Show on mobile */
        }

        /* Add small left padding to main content so hamburger doesn't overlap text */
        .main-content {
            padding-left: 50px !important;
        }
    }

    @media (min-width: 769px) {
        .hamburger {
            display: none !important;
        }
    }

    /* Even smaller on very small screens */
    @media (max-width: 480px) {
        .hamburger {
            font-size: 19px;
            width: 34px;
            height: 34px;
            top: 10px;
            left: 10px;
        }

        .main-content {
            padding-left: 48px !important;
        }
    }
</style>

<button class="hamburger" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Toggle menu">☰</button>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<div class="sidebar" id="sidebar">
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

<script>

    function toggleSidebar() {
        var sidebar = document.getElementById('sidebar');
        var hamburger = document.getElementById('hamburgerBtn');
        sidebar.classList.toggle('show');
        // Hide hamburger when sidebar is open (on mobile)
        if (sidebar.classList.contains('show')) {
            hamburger.style.display = 'none';
        } else {
            hamburger.style.display = '';
        }
    }

    // Close sidebar when clicking a link on mobile
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.remove('show');
                document.getElementById('hamburgerBtn').style.display = '';
            }
        });
    });

	// Hide sidebar if clicking outside of sidebar or hamburger (on mobile)
	document.addEventListener('click', function(event) {
    var sidebar = document.getElementById('sidebar');
    var hamburger = document.getElementById('hamburgerBtn');
    var overlay = document.querySelector('.sidebar-overlay');
    // Only on mobile and only if sidebar is open
    if (window.innerWidth <= 768 && sidebar.classList.contains('show')) {
        // If click is NOT inside sidebar, NOT on hamburger, and NOT on overlay
        if (!sidebar.contains(event.target) && event.target !== hamburger && event.target !== overlay) {
            sidebar.classList.remove('show');
            hamburger.style.display = '';
        }
    }
});
</script>
<script>
    // Also close sidebar and show hamburger if overlay is clicked
    document.querySelector('.sidebar-overlay').addEventListener('click', function() {
        document.getElementById('sidebar').classList.remove('show');
        document.getElementById('hamburgerBtn').style.display = '';
    });
</script>
