<?php
include '../includes/header.php';
include '../includes/database.php';

// Fetch total sales
$sql_total_sales = "SELECT SUM(total_amount) AS total_sales FROM orders WHERE status != 'cancelled'";
$result_total_sales = $conn->query($sql_total_sales);
$total_sales = $result_total_sales->fetch_assoc()['total_sales'] ?? 0;

// Fetch total orders
$sql_total_orders = "SELECT COUNT(order_id) AS total_orders FROM orders";
$result_total_orders = $conn->query($sql_total_orders);
$total_orders = $result_total_orders->fetch_assoc()['total_orders'] ?? 0;

// Fetch total customers
$sql_total_customers = "SELECT COUNT(user_id) AS total_customers FROM users WHERE role = 'customer'";
$result_total_customers = $conn->query($sql_total_customers);
$total_customers = $result_total_customers->fetch_assoc()['total_customers'] ?? 0;

// Fetch top 5 selling products (based on quantity sold)
$sql_top_products = "SELECT p.name, SUM(oi.quantity) AS total_quantity_sold 
                     FROM order_items oi 
                     JOIN products p ON oi.product_id = p.product_id 
                     GROUP BY p.product_id 
                     ORDER BY total_quantity_sold DESC 
                     LIMIT 5";
$result_top_products = $conn->query($sql_top_products);
$top_products = [];
while($row = $result_top_products->fetch_assoc()) {
    $top_products[] = $row;
}

$conn->close();

?>

<!-- GEMINI_EDIT_SECTION: admin_reports_start -->
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-2">
            <?php include 'admin_sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Reports & Analytics</h1>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Sales</h5>
                            <p class="card-text display-4">$<?= number_format($total_sales, 2) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Total Orders</h5>
                            <p class="card-text display-4"><?= $total_orders ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Total Customers</h5>
                            <p class="card-text display-4"><?= $total_customers ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">Top 5 Selling Products</div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($top_products)): ?>
                                    <?php foreach ($top_products as $product): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?= htmlspecialchars($product['name']) ?>
                                            <span class="badge bg-primary rounded-pill"><?= $product['total_quantity_sold'] ?> units</span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-center">No product sales data.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">Sales Over Time (Placeholder)</div>
                        <div class="card-body">
                            <canvas id="salesChart"></canvas>
                            <small class="text-muted">Integration with Chart.js or similar library would go here.</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: admin_reports_end -->

<?php include '../includes/footer.php'; ?>