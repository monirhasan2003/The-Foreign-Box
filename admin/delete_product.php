<?php
// This script handles the deletion of a product.
session_start();
require_once '../../config/database.php';

// --- Main Logic ---

// 1. Check if a product ID is provided via GET method and is a number
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    // --- Database Transaction for safe deletion ---
    $conn->begin_transaction();

    try {
        // 2. First, get the path of the product image to delete the file from the server
        $stmt_get_image = $conn->prepare("SELECT main_image_url FROM products WHERE product_id = ?");
        $stmt_get_image->bind_param("i", $product_id);
        $stmt_get_image->execute();
        $result = $stmt_get_image->get_result();

        if ($result->num_rows === 1) {
            $product = $result->fetch_assoc();
            $image_path_to_delete = '../../' . $product['main_image_url'];
        } else {
            // If product not found, no need to proceed
            throw new Exception("Product not found.");
        }
        $stmt_get_image->close();

        // 3. Delete the product from the `products` table
        // Because of the 'ON DELETE CASCADE' constraint set in our database schema,
        // deleting a product here will automatically delete all related entries in:
        // - product_categories
        // - inventory
        // - order_items (Note: This is generally safe, but some businesses might prefer to keep order history)
        // - reviews
        // - wishlist
        $stmt_delete_product = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt_delete_product->bind_param("i", $product_id);
        $stmt_delete_product->execute();

        // Check if the deletion was successful
        if ($stmt_delete_product->affected_rows > 0) {
            // 4. If database deletion is successful, delete the image file
            if (isset($image_path_to_delete) && file_exists($image_path_to_delete)) {
                unlink($image_path_to_delete);
            }

            // 5. Commit the transaction
            $conn->commit();
            $_SESSION['success_message'] = "Product deleted successfully!";
        } else {
            // This case might happen if the ID was valid but already deleted
            throw new Exception("Could not delete the product. It may have already been removed.");
        }
        $stmt_delete_product->close();

    } catch (Exception $e) {
        // If any error occurs, roll back the transaction
        $conn->rollback();
        $_SESSION['error_message'] = "Error deleting product: " . $e->getMessage();
    } finally {
        $conn->close();
    }

} else {
    // If no valid ID is provided, set an error message
    $_SESSION['error_message'] = "Invalid request. No product ID specified.";
}

// 6. Redirect back to the products list page
header('Location: ../products.php');
exit();
?>
