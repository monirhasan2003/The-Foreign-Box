<?php include '../includes/header.php'; ?>

<!-- GEMINI_EDIT_SECTION: admin_dashboard_start -->
<div class="container-fluid mt-5">
    <div class="row">
        <!-- GEMINI_EDIT_SECTION: admin_sidebar_start -->
        <div class="col-md-2">
            <?php include 'admin_sidebar.php'; ?>
        </div>
        <!-- GEMINI_EDIT_SECTION: admin_sidebar_end -->

        <!-- GEMINI_EDIT_SECTION: admin_content_start -->
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>

            <!-- GEMINI_EDIT_SECTION: summary_cards_start -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Total Sales</div>
                        <div class="card-body">
                            <h5 class="card-title">$12,345</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">New Orders</div>
                        <div class="card-body">
                            <h5 class="card-title">67</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">Unique Visitors</div>
                        <div class="card-body">
                            <h5 class="card-title">1,234</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">Pending Issues</div>
                        <div class="card-body">
                            <h5 class="card-title">5</h5>
                        </div>
                    </div>
                </div>
            </div>
            <!-- GEMINI_EDIT_SECTION: summary_cards_end -->

            <!-- GEMINI_EDIT_SECTION: sales_chart_start -->
            <div class="card mb-4">
                <div class="card-header">
                    Sales Analytics
                </div>
                <div class="card-body">
                    <!-- Placeholder for a chart, e.g., using Chart.js -->
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            <!-- GEMINI_EDIT_SECTION: sales_chart_end -->

            <!-- GEMINI_EDIT_SECTION: recent_orders_start -->
            <h4>Recent Orders</h4>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Placeholder for recent orders -->
                        <tr>
                            <td>1001</td>
                            <td>John Doe</td>
                            <td>Shipped</td>
                            <td>$150.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- GEMINI_EDIT_SECTION: recent_orders_end -->

        </div>
        <!-- GEMINI_EDIT_SECTION: admin_content_end -->
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: admin_dashboard_end -->

<?php include '../includes/footer.php'; ?>