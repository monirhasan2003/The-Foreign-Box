<?php
include '../../includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $couponId = intval($_POST['couponId']);
    $couponCode = $conn->real_escape_string($_POST['couponCode']);
    $discountType = $conn->real_escape_string($_POST['discountType']);
    $discountValue = floatval($_POST['discountValue']);
    $expiryDate = $conn->real_escape_string($_POST['expiryDate']);
    $isActive = isset($_POST['isActive']) ? 1 : 0;

    // Basic validation
    if (empty($couponCode) || empty($discountType) || empty($expiryDate)) {
        header("Location: ../edit_coupon.php?id=" . $couponId . "&error=empty_fields");
        exit();
    }

    // Check if coupon code already exists for another coupon
    $sql_check_code = "SELECT coupon_id FROM coupons WHERE code = ? AND coupon_id != ?";
    $stmt_check_code = $conn->prepare($sql_check_code);
    $stmt_check_code->bind_param("si", $couponCode, $couponId);
    $stmt_check_code->execute();
    $stmt_check_code->store_result();
    if ($stmt_check_code->num_rows > 0) {
        header("Location: ../edit_coupon.php?id=" . $couponId . "&error=code_exists");
        exit();
    }
    $stmt_check_code->close();

    // Update coupon
    $sql_update = "UPDATE coupons SET code = ?, discount_type = ?, discount_value = ?, expiry_date = ?, is_active = ? WHERE coupon_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssdssi", $couponCode, $discountType, $discountValue, $expiryDate, $isActive, $couponId);

    if ($stmt_update->execute()) {
        header("Location: ../coupons.php?success=updated");
    } else {
        error_log("Coupon update failed: " . $stmt_update->error);
        header("Location: ../edit_coupon.php?id=" . $couponId . "&error=update_failed");
    }

    $stmt_update->close();
    $conn->close();

} else {
    header("Location: ../coupons.php");
}
exit();
?>