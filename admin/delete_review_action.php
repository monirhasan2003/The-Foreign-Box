<?php
include '../../includes/database.php';

if (isset($_GET['id'])) {
    $reviewId = intval($_GET['id']);

    $sql_delete = "DELETE FROM reviews WHERE review_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $reviewId);

    if ($stmt_delete->execute()) {
        header("Location: ../reviews.php?success=deleted");
    } else {
        error_log("Review deletion failed: " . $stmt_delete->error);
        header("Location: ../reviews.php?error=deletion_failed");
    }

    $stmt_delete->close();
    $conn->close();

} else {
    header("Location: ../reviews.php");
}
exit();
?>