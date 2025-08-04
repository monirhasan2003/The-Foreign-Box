<?php
session_start();
include 'includes/header.php';
include 'includes/database.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order = null;
$order_items = [];

if ($order_id > 0) {
    $sql_order = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) AS customer_name, u.email 
                  FROM orders o 
                  JOIN users u ON o.user_id = u.user_id 
                  WHERE o.order_id = ?";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("i", $order_id);
    $stmt_order->execute();
    $result_order = $stmt_order->get_result();
    $order = $result_order->fetch_assoc();
    $stmt_order->close();

    if ($order) {
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

<!-- GEMINI_EDIT_SECTION: order_confirmation_page_start -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white text-center">
                    <h3 class="mb-0">Order Confirmed!</h3>
                    <p class="mb-0">Thank you for your purchase.</p>
                </div>
                <div class="card-body">
                    <?php if ($order): ?>
                        <h5 class="card-title">Order #<?= htmlspecialchars($order['order_id']) ?> Details</h5>
                        <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                        <p><strong>Order Date:</strong> <?= date("Y-m-d H:i:s", strtotime($order['created_at'])) ?></p>
                        <p><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
                        <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                        <?php if ($order['transaction_id']): ?>
                            <p><strong>Transaction ID:</strong> <?= htmlspecialchars($order['transaction_id']) ?></p>
                        <?php endif; ?>

                        <hr>

                        <h6>Shipping Address:</h6>
                        <p><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>

                        <h6>Billing Address:</h6>
                        <p><?= nl2br(htmlspecialchars($order['billing_address'])) ?></p>

                        <hr>

                        <h6>Items Ordered:</h6>
                        <ul class="list-group mb-3">
                            <?php foreach ($order_items as $item): ?>
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0"><?= htmlspecialchars($item['product_name']) ?></h6>
                                        <small class="text-muted">Quantity: <?= htmlspecialchars($item['quantity']) ?></small>
                                    </div>
                                    <span class="text-muted">$<?= number_format($item['price_per_item'] * $item['quantity'], 2) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                            <a href="client_dashboard.php" class="btn btn-secondary">View My Orders</a>
                        </div>

                    <?php else: ?>
                        <div class="alert alert-danger">Order details not found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: order_confirmation_page_end -->

<?php 
$conn->close();
include 'includes/footer.php'; 
?>