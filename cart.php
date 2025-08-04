<?php include 'includes/header.php'; ?>

<!-- GEMINI_EDIT_SECTION: cart_page_start -->
<div class="container mt-5">
    <h2>Shopping Cart</h2>
    <div class="row">
        <!-- GEMINI_EDIT_SECTION: cart_items_start -->
        <div class="col-md-8">
            <?php if (!empty($_SESSION['cart'])): ?>
            <form action="actions/update_cart_action.php" method="POST">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subtotal = 0;
                        foreach ($_SESSION['cart'] as $item) {
                            $itemTotal = $item['price'] * $item['quantity'];
                            $subtotal += $itemTotal;
                            echo '<tr>';
                            echo '<td><img src="' . htmlspecialchars($item['main_image_url']) . '" alt="' . htmlspecialchars($item['name']) . '" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">' . htmlspecialchars($item['name']) . '</td>';
                            echo '<td>
        <!-- GEMINI_EDIT_SECTION: cart_summary_end -->
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: cart_page_end -->

<?php include 'includes/footer.php'; ?> . number_format($item['price'], 2) . '</td>';
                            echo '<td><input type="number" class="form-control" name="quantity[' . $item['product_id'] . ']" value="' . htmlspecialchars($item['quantity']) . '" min="1" style="width: 70px;"></td>';
                            echo '<td>
        <!-- GEMINI_EDIT_SECTION: cart_summary_end -->
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: cart_page_end -->

<?php include 'includes/footer.php'; ?> . number_format($itemTotal, 2) . '</td>';
                            echo '<td><a href="actions/remove_from_cart_action.php?id=' . $item['product_id'] . '" class="btn btn-danger btn-sm">Remove</a></td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-info">Update Cart</button>
            </form>
            <?php else: ?>
                <div class="alert alert-info">Your cart is empty.</div>
            <?php endif; ?>
        </div>
        <!-- GEMINI_EDIT_SECTION: cart_items_end -->

        <!-- GEMINI_EDIT_SECTION: cart_summary_start -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Cart Summary</h5>
                    <p>Subtotal: $<?= number_format($subtotal, 2) ?></p>
                    <p>Shipping: $0.00</p> <!-- Placeholder for dynamic shipping -->
                    <p>Total: $<?= number_format($subtotal, 2) ?></p>
                    <hr>
                    <form>
                        <div class="mb-3">
                            <label for="coupon" class="form-label">Coupon Code</label>
                            <input type="text" class="form-control" id="coupon">
                        </div>
                        <button type="submit" class="btn btn-secondary">Apply Coupon</button>
                    </form>
                    <hr>
                    <a href="checkout.php" class="btn btn-primary w-100">Proceed to Checkout</a>
                </div>
            </div>
        </div>
        <!-- GEMINI_EDIT_SECTION: cart_summary_end -->
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: cart_page_end -->

<?php include 'includes/footer.php'; ?>