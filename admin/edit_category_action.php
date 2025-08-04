<?php
include '../../includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryId = intval($_POST['categoryId']);
    $categoryName = $conn->real_escape_string($_POST['categoryName']);
    $categorySlug = $conn->real_escape_string($_POST['categorySlug']);
    $parentCategory = !empty($_POST['parentCategory']) ? intval($_POST['parentCategory']) : NULL;

    // Basic validation
    if (empty($categoryName) || empty($categorySlug)) {
        header("Location: ../edit_category.php?id=" . $categoryId . "&error=empty_fields");
        exit();
    }

    // Check if slug already exists for another category
    $sql_check_slug = "SELECT category_id FROM categories WHERE slug = ? AND category_id != ?";
    $stmt_check_slug = $conn->prepare($sql_check_slug);
    $stmt_check_slug->bind_param("si", $categorySlug, $categoryId);
    $stmt_check_slug->execute();
    $stmt_check_slug->store_result();
    if ($stmt_check_slug->num_rows > 0) {
        header("Location: ../edit_category.php?id=" . $categoryId . "&error=slug_exists");
        exit();
    }
    $stmt_check_slug->close();

    // Update category
    $sql_update = "UPDATE categories SET name = ?, slug = ?, parent_id = ? WHERE category_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssii", $categoryName, $categorySlug, $parentCategory, $categoryId);

    if ($stmt_update->execute()) {
        header("Location: ../categories.php?success=updated");
    } else {
        error_log("Category update failed: " . $stmt_update->error);
        header("Location: ../edit_category.php?id=" . $categoryId . "&error=update_failed");
    }

    $stmt_update->close();
    $conn->close();

} else {
    header("Location: ../categories.php");
}
exit();
?>