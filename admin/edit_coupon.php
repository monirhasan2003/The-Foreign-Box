<?php
include '../includes/header.php';
include '../includes/database.php';

$coupon_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$coupon = null;

if ($coupon_id > 0) {
    // Fetch coupon data
    $sql_coupon = "SELECT * FROM coupons WHERE coupon_id = ?";
    $stmt_coupon = $conn->prepare($sql_coupon);
    $stmt_coupon->bind_param("i", $coupon_id);
    $stmt_coupon->execute();
    $result_coupon = $stmt_coupon->get_result();
    $coupon = $result_coupon->fetch_assoc();
    $stmt_coupon->close();
}

?>

<!-- GEMINI_EDIT_SECTION: edit_coupon_form_start -->
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-2">
            <?php include 'admin_sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Coupon</h1>
            </div>

            <?php if ($coupon): ?>
            <form action="actions/edit_coupon_action.php" method="POST">
                <input type="hidden" name="couponId" value="<?= htmlspecialchars($coupon['coupon_id']) ?>">
                <div class="mb-3">
                    <label for="couponCode" class="form-label">Coupon Code</label>
                    <input type="text" class="form-control" id="couponCode" name="couponCode" value="<?= htmlspecialchars($coupon['code']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="discountType" class="form-label">Discount Type</label>
                    <select class="form-select" id="discountType" name="discountType" required>
                        <option value="percentage" <?= ($coupon['discount_type'] == 'percentage') ? 'selected' : '' ?>>Percentage (%)</option>
                        <option value="fixed" <?= ($coupon['discount_type'] == 'fixed') ? 'selected' : '' ?>>Fixed Amount ($)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="discountValue" class="form-label">Discount Value</label>
                    <input type="number" step="0.01" class="form-control" id="discountValue" name="discountValue" value="<?= htmlspecialchars($coupon['discount_value']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="expiryDate" class="form-label">Expiry Date</label>
                    <input type="date" class="form-control" id="expiryDate" name="expiryDate" value="<?= htmlspecialchars($coupon['expiry_date']) ?>" required>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="isActive" name="isActive" <?= $coupon['is_active'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
                <button type="submit" class="btn btn-primary">Update Coupon</button>
                <a href="coupons.php" class="btn btn-secondary">Cancel</a>
            </form>
            <?php else: ?>
                <div class="alert alert-danger">Coupon not found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: edit_coupon_form_end -->

<?php 
$conn->close();
include '../includes/footer.php'; 
?>