<?php
// This page displays the full details of a single order.
session_start();
require_once '../../config/database.php';

// 1. Get Order ID and Validate
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid order ID.";
    header('Location: orders.php');
    exit();
}
$order_id = (int)$_GET['id'];

// 2. Fetch Order and Customer Data
$order = null;
$order_items = [];
$possible_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']; // Define possible statuses

try {
    // Fetch main order details and join with users table for customer info
    $stmt = $conn->prepare(
        "SELECT o.*, u.first_name, u.last_name, u.email, u.phone 
         FROM orders o 
         JOIN users u ON o.user_id = u.user_id 
         WHERE o.order_id = ?"
    );
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $order = $result->fetch_assoc();
    } else {
        throw new Exception("Order not found.");
    }
    $stmt->close();

    // Fetch all items associated with this order
    $stmt_items = $conn->prepare(
        "SELECT oi.quantity, oi.price_per_item, p.name as product_name, p.sku 
         FROM order_items oi 
         JOIN products p ON oi.product_id = p.product_id 
         WHERE oi.order_id = ?"
    );
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $order_items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_items->close();

} catch (Exception $e) {
    $_SESSION['error_message'] = "Error fetching order details: " . $e->getMessage();
    header('Location: orders.php');
    exit();
}

// Function to get a Bootstrap badge class based on order status
function get_status_badge($status) {
    switch (strtolower($status)) {
        case 'pending': return 'bg-warning text-dark';
        case 'processing': return 'bg-info text-dark';
        case 'shipped': return 'bg-primary';
        case 'delivered': return 'bg-success';
        case 'cancelled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $order['order_id']; ?> Details - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 1100px; }
        .card { border: none; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 1.5rem; }
        .status-badge { font-size: 1em; padding: 0.5em 0.8em; border-radius: 0.25rem; color: white; text-transform: capitalize; }
        .product-table th { background-color: #f8f9fa; }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <!-- GEMINI_EDIT_SECTION: order_details_start -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Order Details <span class="text-muted">#<?php echo $order['order_id']; ?></span></h3>
        <a href="orders.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Products in Order Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Products Ordered</h5>
                </div>
                <div class="card-body">
                    <table class="table product-table align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Price per Item</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                                    <small class="text-muted">SKU: <?php echo htmlspecialchars($item['sku']); ?></small>
                                </td>
                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                <td class="text-end">$<?php echo number_format($item['price_per_item'], 2); ?></td>
                                <td class="text-end">$<?php echo number_format($item['price_per_item'] * $item['quantity'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Grand Total:</td>
                                <td class="text-end">$<?php echo number_format($order['total_amount'], 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Shipping Information Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Shipping & Billing Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><strong>Shipping Address</strong></h6>
                            <address><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></address>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Billing Address</strong></h6>
                            <address><?php echo nl2br(htmlspecialchars($order['billing_address'])); ?></address>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Summary & Status Update Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <p><strong>Order Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($order['created_at'])); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($order['transaction_id'] ?? 'N/A'); ?></p>
                    <hr>
                    <h6><strong>Current Status:</strong> 
                        <span class="status-badge <?php echo get_status_badge($order['status']); ?>">
                            <?php echo htmlspecialchars($order['status']); ?>
                        </span>
                    </h6>
                    
                    <!-- Form to update status -->
                    <form action="actions/order_action.php" method="POST" class="mt-4">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                        <div class="mb-3">
                            <label for="status" class="form-label"><strong>Update Order Status</strong></label>
                            <select name="status" id="status" class="form-select">
                                <?php foreach ($possible_statuses as $status_option): ?>
                                    <option value="<?php echo $status_option; ?>" <?php echo ($order['status'] == $status_option) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($status_option); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Customer Details Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Customer Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                    <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>"><?php echo htmlspecialchars($order['email']); ?></a></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>
    </div>
    <!-- GEMINI_EDIT_SECTION: order_details_end -->
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
