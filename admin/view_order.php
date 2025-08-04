<?php
include '../includes/header.php';
include '../includes/database.php';

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$order = null;
$order_items = [];

if ($order_id > 0) {
    // Fetch order details
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

<!-- GEMINI_EDIT_SECTION: view_order_start -->
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-2">
            <?php include 'admin_sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Order Details #<?= htmlspecialchars($order_id) ?></h1>
            </div>

            <?php if ($order): ?>
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Customer Information</h4>
                    <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    <p><strong>Order Date:</strong> <?= date("Y-m-d H:i:s", strtotime($order['created_at'])) ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-info"><?= htmlspecialchars(ucfirst($order['status'])) ?></span></p>
                </div>
                <div class="col-md-6">
                    <h4>Order Summary</h4>
                    <p><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
                    <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                    <p><strong>Transaction ID:</strong> <?= htmlspecialchars($order['transaction_id']) ?? 'N/A' ?></p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Billing Address</h4>
                    <p><?= nl2br(htmlspecialchars($order['billing_address'])) ?></p>
                </div>
                <div class="col-md-6">
                    <h4>Shipping Address</h4>
                    <p><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                </div>
            </div>

            <h4>Order Items</h4>
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
                <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
            </div>

            <?php else: ?>
                <div class="alert alert-danger">Order not found.</div>
            <?php endif; ?>

        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: view_order_end -->

<?php 
$conn->close();
include '../includes/footer.php'; 
?>