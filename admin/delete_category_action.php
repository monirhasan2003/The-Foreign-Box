<?php
include '../../includes/database.php';

if (isset($_GET['id'])) {
    $categoryId = intval($_GET['id']);

    // Check if category has children
    $sql_check_children = "SELECT COUNT(*) FROM categories WHERE parent_id = ?";
    $stmt_check_children = $conn->prepare($sql_check_children);
    $stmt_check_children->bind_param("i", $categoryId);
    $stmt_check_children->execute();
    $stmt_check_children->bind_result($child_count);
    $stmt_check_children->fetch();
    $stmt_check_children->close();

    if ($child_count > 0) {
        header("Location: ../categories.php?error=category_has_children");
        exit();
    }

    // Check if category is associated with any products
    $sql_check_products = "SELECT COUNT(*) FROM product_categories WHERE category_id = ?";
    $stmt_check_products = $conn->prepare($sql_check_products);
    $stmt_check_products->bind_param("i", $categoryId);
    $stmt_check_products->execute();
    $stmt_check_products->bind_result($product_count);
    $stmt_check_products->fetch();
    $stmt_check_products->close();

    if ($product_count > 0) {
        header("Location: ../categories.php?error=category_has_products");
        exit();
    }

    // Delete category
    $sql_delete = "DELETE FROM categories WHERE category_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $categoryId);

    if ($stmt_delete->execute()) {
        header("Location: ../categories.php?success=deleted");
    } else {
        error_log("Category deletion failed: " . $stmt_delete->error);
        header("Location: ../categories.php?error=deletion_failed");
    }

    $stmt_delete->close();
    $conn->close();

} else {
    header("Location: ../categories.php");
}
exit();
?>