<?php
session_start();
include 'includes/header.php';
include 'includes/database.php';

$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['email'] ?? '';
$user_first_name = $_SESSION['first_name'] ?? '';
$user_last_name = $_SESSION['last_name'] ?? '';

$billing_address = '';
$shipping_address = '';

if ($user_id) {
    $sql_user = "SELECT * FROM users WHERE user_id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    if ($user_data = $result_user->fetch_assoc()) {
        // Assuming user table might have address fields, or we'd fetch from a separate address table
        // For now, just pre-fill name and email
        $user_email = $user_data['email'];
        $user_first_name = $user_data['first_name'];
        $user_last_name = $user_data['last_name'];
    }
    $stmt_user->close();
}

$cart_items = $_SESSION['cart'] ?? [];
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$total_amount = $subtotal; // For now, no shipping/taxes added

?>

<!-- GEMINI_EDIT_SECTION: checkout_page_start -->
<div class="container mt-5">
    <h2>Checkout</h2>
    <div class="row">
        <!-- GEMINI_EDIT_SECTION: billing_shipping_start -->
        <div class="col-md-8">
            <h4>Billing & Shipping Information</h4>
            <form action="actions/process_order_action.php" method="POST">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?= htmlspecialchars($user_first_name) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?= htmlspecialchars($user_last_name) ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user_email) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="billingAddress" class="form-label">Billing Address</label>
                    <textarea class="form-control" id="billingAddress" name="billingAddress" rows="3" required><?= htmlspecialchars($billing_address) ?></textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="sameAsBilling" name="sameAsBilling" checked>
                    <label class="form-check-label" for="sameAsBilling">
                        Shipping address same as billing
                    </label>
                </div>
                <div id="shippingAddressFields" style="display: none;">
                    <div class="mb-3">
                        <label for="shippingAddress" class="form-label">Shipping Address</label>
                        <textarea class="form-control" id="shippingAddress" name="shippingAddress" rows="3"><?= htmlspecialchars($shipping_address) ?></textarea>
                    </div>
                </div>

                <hr>
                <h4>Payment Information</h4>
                <div class="mb-3">
                    <label for="paymentMethod" class="form-label">Select Payment Method</label>
                    <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                        <option value="">Choose...</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="PayPal">PayPal</option>
                        <option value="bKash">bKash</option>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                    </select>
                </div>

                <div id="bkashDetails" class="mb-3" style="display: none;">
                    <p>Please send your payment to the bKash Merchant Account:</p>
                    <p><strong>+8801713708145</strong></p>
                    <label for="bKashTransactionId" class="form-label">bKash Transaction ID</label>
                    <input type="text" class="form-control" id="bKashTransactionId" name="bKashTransactionId" placeholder="Enter bKash Transaction ID">
                </div>

                <hr>
                <button type="submit" class="btn btn-primary w-100">Place Order</button>
            </form>
        </div>
        <!-- GEMINI_EDIT_SECTION: billing_shipping_end -->

        <!-- GEMINI_EDIT_SECTION: order_summary_start -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order Summary</h5>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($cart_items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0"><?= htmlspecialchars($item['name']) ?></h6>
                                    <small class="text-muted">Quantity: <?= htmlspecialchars($item['quantity']) ?></small>
                                </div>
                                <span class="text-muted">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                            </li>
                        <?php endforeach; ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Subtotal (USD)</span>
                            <strong>$<?= number_format($subtotal, 2) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Shipping (USD)</span>
                            <strong>$0.00</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total (USD)</span>
                            <strong>$<?= number_format($total_amount, 2) ?></strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- GEMINI_EDIT_SECTION: order_summary_end -->
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: checkout_page_end -->

<script>
    document.getElementById('sameAsBilling').addEventListener('change', function() {
        const shippingAddressFields = document.getElementById('shippingAddressFields');
        if (this.checked) {
            shippingAddressFields.style.display = 'none';
            document.getElementById('shippingAddress').removeAttribute('required');
        } else {
            shippingAddressFields.style.display = 'block';
            document.getElementById('shippingAddress').setAttribute('required', 'required');
        }
    });

    document.getElementById('paymentMethod').addEventListener('change', function() {
        const bkashDetails = document.getElementById('bkashDetails');
        if (this.value === 'bKash') {
            bkashDetails.style.display = 'block';
            document.getElementById('bKashTransactionId').setAttribute('required', 'required');
        } else {
            bkashDetails.style.display = 'none';
            document.getElementById('bKashTransactionId').removeAttribute('required');
        }
    });
</script>

<?php 
$conn->close();
include 'includes/footer.php'; 
?>