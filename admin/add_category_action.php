<?php
include '../../includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryName = $conn->real_escape_string($_POST['categoryName']);
    $categorySlug = $conn->real_escape_string($_POST['categorySlug']);
    $parentCategory = !empty($_POST['parentCategory']) ? intval($_POST['parentCategory']) : NULL;

    // Basic validation
    if (empty($categoryName) || empty($categorySlug)) {
        header("Location: ../add_category.php?error=empty_fields");
        exit();
    }

    // Check if slug already exists
    $sql_check_slug = "SELECT category_id FROM categories WHERE slug = ?";
    $stmt_check_slug = $conn->prepare($sql_check_slug);
    $stmt_check_slug->bind_param("s", $categorySlug);
    $stmt_check_slug->execute();
    $stmt_check_slug->store_result();
    if ($stmt_check_slug->num_rows > 0) {
        header("Location: ../add_category.php?error=slug_exists");
        exit();
    }
    $stmt_check_slug->close();

    // Insert new category
    $sql_insert = "INSERT INTO categories (name, slug, parent_id) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssi", $categoryName, $categorySlug, $parentCategory);

    if ($stmt_insert->execute()) {
        header("Location: ../categories.php?success=added");
    } else {
        error_log("Category insertion failed: " . $stmt_insert->error);
        header("Location: ../add_category.php?error=insertion_failed");
    }

    $stmt_insert->close();
    $conn->close();

} else {
    header("Location: ../add_category.php");
}
exit();
?>