<?php
include '../../includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $couponCode = $conn->real_escape_string($_POST['couponCode']);
    $discountType = $conn->real_escape_string($_POST['discountType']);
    $discountValue = floatval($_POST['discountValue']);
    $expiryDate = $conn->real_escape_string($_POST['expiryDate']);
    $isActive = isset($_POST['isActive']) ? 1 : 0;

    // Basic validation
    if (empty($couponCode) || empty($discountType) || empty($expiryDate)) {
        header("Location: ../add_coupon.php?error=empty_fields");
        exit();
    }

    // Check if coupon code already exists
    $sql_check_code = "SELECT coupon_id FROM coupons WHERE code = ?";
    $stmt_check_code = $conn->prepare($sql_check_code);
    $stmt_check_code->bind_param("s", $couponCode);
    $stmt_check_code->execute();
    $stmt_check_code->store_result();
    if ($stmt_check_code->num_rows > 0) {
        header("Location: ../add_coupon.php?error=code_exists");
        exit();
    }
    $stmt_check_code->close();

    // Insert new coupon
    $sql_insert = "INSERT INTO coupons (code, discount_type, discount_value, expiry_date, is_active) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssdss", $couponCode, $discountType, $discountValue, $expiryDate, $isActive);

    if ($stmt_insert->execute()) {
        header("Location: ../coupons.php?success=added");
    } else {
        error_log("Coupon insertion failed: " . $stmt_insert->error);
        header("Location: ../add_coupon.php?error=insertion_failed");
    }

    $stmt_insert->close();
    $conn->close();

} else {
    header("Location: ../add_coupon.php");
}
exit();
?>