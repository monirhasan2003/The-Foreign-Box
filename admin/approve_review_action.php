<?php
include '../../includes/database.php';

if (isset($_GET['id'])) {
    $reviewId = intval($_GET['id']);

    $sql_approve = "UPDATE reviews SET is_approved = 1 WHERE review_id = ?";
    $stmt_approve = $conn->prepare($sql_approve);
    $stmt_approve->bind_param("i", $reviewId);

    if ($stmt_approve->execute()) {
        header("Location: ../reviews.php?success=approved");
    } else {
        error_log("Review approval failed: " . $stmt_approve->error);
        header("Location: ../reviews.php?error=approval_failed");
    }

    $stmt_approve->close();
    $conn->close();

} else {
    header("Location: ../reviews.php");
}
exit();
?>