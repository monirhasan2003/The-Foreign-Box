<?php include 'includes/header.php'; ?>

<!-- GEMINI_EDIT_SECTION: client_dashboard_start -->
<div class="container mt-5">
    <div class="row">
        <!-- GEMINI_EDIT_SECTION: client_sidebar_start -->
        <div class="col-md-3">
            <div class="list-group">
                <a href="client_dashboard.php" class="list-group-item list-group-item-action active">Dashboard</a>
                <a href="client_orders.php" class="list-group-item list-group-item-action">My Orders</a>
                <a href="client_wishlist.php" class="list-group-item list-group-item-action">My Wishlist</a>
                <a href="client_addresses.php" class="list-group-item list-group-item-action">My Addresses</a>
                <a href="client_account.php" class="list-group-item list-group-item-action">Account Details</a>
                <a href="#" class="list-group-item list-group-item-action">Logout</a>
            </div>
        </div>
        <!-- GEMINI_EDIT_SECTION: client_sidebar_end -->

        <!-- GEMINI_EDIT_SECTION: client_content_start -->
        <div class="col-md-9">
            <h3>Dashboard</h3>
            <p>Welcome back, [User]!</p>
            <!-- Overview of recent activity -->
        </div>
        <!-- GEMINI_EDIT_SECTION: client_content_end -->
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: client_dashboard_end -->

<?php include 'includes/footer.php'; ?>