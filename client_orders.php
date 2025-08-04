<?php
session_start();
include 'includes/header.php';
include 'includes/database.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the logged-in user
$sql_orders = "SELECT order_id, total_amount, status, created_at 
               FROM orders 
               WHERE user_id = ? 
               ORDER BY created_at DESC";
$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
$orders = [];
while($row = $result_orders->fetch_assoc()) {
    $orders[] = $row;
}
$stmt_orders->close();
$conn->close();

?>

<!-- GEMINI_EDIT_SECTION: client_orders_start -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <?php include 'includes/client_sidebar.php'; ?>
        </div>
        <div class="col-md-9">
            <h3>My Orders</h3>
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
                                    <td><a href="view_order_details.php?id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-secondary">View Details</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">You have no orders yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: client_orders_end -->
<?php include 'includes/footer.php'; ?>