<?php
// This is the Order Management page for the admin panel.
session_start();
require_once '../../config/database.php';

// --- Fetch all orders from the database ---
// We use a JOIN to get the customer's name from the `users` table.
$orders = [];
try {
    $sql = "SELECT 
                o.order_id, 
                o.total_amount, 
                o.status, 
                o.created_at, 
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name 
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            ORDER BY o.created_at DESC"; // Show the most recent orders first
            
    $result = $conn->query($sql);
    if ($result) {
        $orders = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = "Error fetching orders: " . $e->getMessage();
}

// Function to get a Bootstrap badge class based on order status
function get_status_badge($status) {
    switch (strtolower($status)) {
        case 'pending':
            return 'bg-warning text-dark';
        case 'processing':
            return 'bg-info text-dark';
        case 'shipped':
            return 'bg-primary';
        case 'delivered':
            return 'bg-success';
        case 'cancelled':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container-fluid { max-width: 1400px; }
        .card { border: none; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .status-badge {
            font-size: 0.85em;
            padding: 0.4em 0.7em;
            border-radius: 0.25rem;
            color: white;
            text-transform: capitalize;
        }
    </style>
</head>
<body>

<div class="container-fluid mt-5 mb-5">
    <!-- GEMINI_EDIT_SECTION: order_list_start -->
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Order Management</h4>

            <!-- Display Success/Error Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No orders found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo get_status_badge($order['status']); ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date("M d, Y, h:i A", strtotime($order['created_at'])); ?></td>
                                    <td class="text-end">
                                        <a href="order_details.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- GEMINI_EDIT_SECTION: order_list_end -->
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
