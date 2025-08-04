<?php
include '../../includes/database.php';

if (isset($_GET['id'])) {
    $couponId = intval($_GET['id']);

    // Delete coupon
    $sql_delete = "DELETE FROM coupons WHERE coupon_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $couponId);

    if ($stmt_delete->execute()) {
        header("Location: ../coupons.php?success=deleted");
    } else {
        error_log("Coupon deletion failed: " . $stmt_delete->error);
        header("Location: ../coupons.php?error=deletion_failed");
    }

    $stmt_delete->close();
    $conn->close();

} else {
    header("Location: ../coupons.php");
}
exit();
?>