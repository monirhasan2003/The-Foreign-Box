<?php
include '../includes/header.php';
include '../includes/database.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$customer = null;
$orders = [];

if ($user_id > 0) {
    // Fetch customer details
    $sql_customer = "SELECT user_id, first_name, last_name, email, phone, created_at 
                     FROM users 
                     WHERE user_id = ? AND role = 'customer'";
    $stmt_customer = $conn->prepare($sql_customer);
    $stmt_customer->bind_param("i", $user_id);
    $stmt_customer->execute();
    $result_customer = $stmt_customer->get_result();
    $customer = $result_customer->fetch_assoc();
    $stmt_customer->close();

    if ($customer) {
        // Fetch customer's order history
        $sql_orders = "SELECT order_id, total_amount, status, created_at 
                       FROM orders 
                       WHERE user_id = ? 
                       ORDER BY created_at DESC";
        $stmt_orders = $conn->prepare($sql_orders);
        $stmt_orders->bind_param("i", $user_id);
        $stmt_orders->execute();
        $result_orders = $stmt_orders->get_result();
        while($row = $result_orders->fetch_assoc()) {
            $orders[] = $row;
        }
        $stmt_orders->close();
    }
}

?>

<!-- GEMINI_EDIT_SECTION: view_customer_start -->
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-2">
            <?php include 'admin_sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Customer Profile: <?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></h1>
            </div>

            <?php if ($customer): ?>
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Contact Information</h4>
                    <p><strong>Name:</strong> <?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone']) ?? 'N/A' ?></p>
                    <p><strong>Member Since:</strong> <?= date("Y-m-d", strtotime($customer['created_at'])) ?></p>
                </div>
            </div>

            <hr>

            <h4>Order History</h4>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['order_id']) ?></td>
                                    <td><?= date("Y-m-d", strtotime($order['created_at'])) ?></td>
                                    <td><span class="badge bg-info"><?= htmlspecialchars(ucfirst($order['status'])) ?></span></td>
                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                    <td><a href="view_order.php?id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-secondary">View Details</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No orders found for this customer.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="customers.php" class="btn btn-secondary">Back to Customers</a>
            </div>

            <?php else: ?>
                <div class="alert alert-danger">Customer not found.</div>
            <?php endif; ?>

        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: view_customer_end -->

<?php 
$conn->close();
include '../includes/footer.php'; 
?>