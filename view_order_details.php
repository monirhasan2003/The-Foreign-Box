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
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$order = null;
$order_items = [];

if ($order_id > 0) {
    // Fetch order details, ensuring it belongs to the logged-in user
    $sql_order = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) AS customer_name, u.email 
                  FROM orders o 
                  JOIN users u ON o.user_id = u.user_id 
                  WHERE o.order_id = ? AND o.user_id = ?";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("ii", $order_id, $user_id);
    $stmt_order->execute();
    $result_order = $stmt_order->get_result();
    $order = $result_order->fetch_assoc();
    $stmt_order->close();

    if ($order) {
        // Fetch order items
        $sql_items = "SELECT oi.*, p.name AS product_name 
                      FROM order_items oi 
                      JOIN products p ON oi.product_id = p.product_id 
                      WHERE oi.order_id = ?";
        $stmt_items = $conn->prepare($sql_items);
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
        while($row = $result_items->fetch_assoc()) {
            $order_items[] = $row;
        }
        $stmt_items->close();
    }
}

?>

<!-- GEMINI_EDIT_SECTION: view_order_details_page_start -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <?php include 'includes/client_sidebar.php'; ?>
        </div>
        <div class="col-md-9">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h3 class="h2">Order Details #<?= htmlspecialchars($order_id) ?></h3>
            </div>

            <?php if ($order): ?>
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Order Information</h4>
                    <p><strong>Order Date:</strong> <?= date("Y-m-d H:i:s", strtotime($order['created_at'])) ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-info"><?= htmlspecialchars(ucfirst($order['status'])) ?></span></p>
                    <p><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
                    <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                    <?php if ($order['transaction_id']): ?>
                        <p><strong>Transaction ID:</strong> <?= htmlspecialchars($order['transaction_id']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h4>Shipping Address</h4>
                    <p><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                    <h4>Billing Address</h4>
                    <p><?= nl2br(htmlspecialchars($order['billing_address'])) ?></p>
                </div>
            </div>

            <h4>Items Ordered:</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price Per Item</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($order_items)): ?>
                            <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                                    <td>$<?= number_format($item['price_per_item'], 2) ?></td>
                                    <td>$<?= number_format($item['quantity'] * $item['price_per_item'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No items found for this order.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="client_orders.php" class="btn btn-secondary">Back to My Orders</a>
            </div>

            <?php else: ?>
                <div class="alert alert-danger">Order not found or you do not have permission to view it.</div>
            <?php endif; ?>

        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: view_order_details_page_end -->

<?php 
$conn->close();
include 'includes/footer.php'; 
?>