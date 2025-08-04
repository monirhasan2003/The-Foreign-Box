<?php
// This script handles the complex logic for updating an existing product.
session_start();
require_once '../../config/database.php';

// --- Main Logic ---

// 1. Check if the form was submitted using the POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 2. Sanitize and retrieve form data
    $product_id = (int)$_POST['product_id'];
    $sku = trim($_POST['sku']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? $_POST['sale_price'] : NULL;
    $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
    $inventory_data = isset($_POST['inventory']) ? $_POST['inventory'] : [];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $current_image_path = $_POST['current_image_path'];

    // --- 3. Handle File Upload (if a new image is provided) ---
    $main_image_url = $current_image_path; // Default to the current image
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        // A new file was uploaded, so process it
        $upload_dir = '../../uploads/products/';
        $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid('product_', true) . '.' . $file_extension;
        $target_file = $upload_dir . $unique_filename;

        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target_file)) {
            // New image uploaded successfully, update the path
            $main_image_url = 'uploads/products/' . $unique_filename;
            // Delete the old image file
            if (file_exists('../../' . $current_image_path)) {
                unlink('../../' . $current_image_path);
            }
        } else {
            $_SESSION['error_message'] = "Sorry, there was an error uploading the new file.";
            header('Location: ../edit_product.php?id=' . $product_id);
            exit();
        }
    }

    // --- 4. Database Transaction for safe updates ---
    $conn->begin_transaction();

    try {
        // Step A: Update the `products` table
        $stmt_product = $conn->prepare(
            "UPDATE products SET sku = ?, name = ?, description = ?, price = ?, sale_price = ?, main_image_url = ?, is_featured = ?, is_active = ? WHERE product_id = ?"
        );
        $stmt_product->bind_param("sssddssii", $sku, $name, $description, $price, $sale_price, $main_image_url, $is_featured, $is_active, $product_id);
        $stmt_product->execute();
        $stmt_product->close();

        // Step B: Update `product_categories` (easiest way is to delete all and re-insert)
        $stmt_delete_cats = $conn->prepare("DELETE FROM product_categories WHERE product_id = ?");
        $stmt_delete_cats->bind_param("i", $product_id);
        $stmt_delete_cats->execute();
        $stmt_delete_cats->close();

        if (!empty($categories)) {
            $stmt_insert_cats = $conn->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
            foreach ($categories as $category_id) {
                $stmt_insert_cats->bind_param("ii", $product_id, $category_id);
                $stmt_insert_cats->execute();
            }
            $stmt_insert_cats->close();
        }

        // Step C: Update `inventory` (The most complex part)
        $submitted_inventory_ids = [];
        // Prepare statements for updating and inserting inventory
        $stmt_update_inv = $conn->prepare("UPDATE inventory SET attribute_size = ?, attribute_color = ?, quantity = ? WHERE inventory_id = ?");
        $stmt_insert_inv = $conn->prepare("INSERT INTO inventory (product_id, attribute_size, attribute_color, quantity) VALUES (?, ?, ?, ?)");

        foreach ($inventory_data as $item) {
            $size = !empty($item['size']) ? trim($item['size']) : NULL;
            $color = !empty($item['color']) ? trim($item['color']) : NULL;
            $quantity = (int)$item['quantity'];

            if (!empty($item['id'])) {
                // This is an existing inventory item, so UPDATE it
                $inventory_id = (int)$item['id'];
                $stmt_update_inv->bind_param("ssii", $size, $color, $quantity, $inventory_id);
                $stmt_update_inv->execute();
                $submitted_inventory_ids[] = $inventory_id;
            } else {
                // This is a new inventory item, so INSERT it
                $stmt_insert_inv->bind_param("issi", $product_id, $size, $color, $quantity);
                $stmt_insert_inv->execute();
            }
        }
        $stmt_update_inv->close();
        $stmt_insert_inv->close();

        // Now, delete any inventory items that were removed from the form
        // First, get all current inventory IDs for this product from the DB
        $stmt_get_ids = $conn->prepare("SELECT inventory_id FROM inventory WHERE product_id = ?");
        $stmt_get_ids->bind_param("i", $product_id);
        $stmt_get_ids->execute();
        $result_ids = $stmt_get_ids->get_result();
        $db_inventory_ids = [];
        while($row = $result_ids->fetch_assoc()) {
            $db_inventory_ids[] = $row['inventory_id'];
        }
        $stmt_get_ids->close();

        // Find which IDs to delete (in DB but not in submission)
        $ids_to_delete = array_diff($db_inventory_ids, $submitted_inventory_ids);
        if (!empty($ids_to_delete)) {
            $stmt_delete_inv = $conn->prepare("DELETE FROM inventory WHERE inventory_id = ?");
            foreach ($ids_to_delete as $id_to_delete) {
                $stmt_delete_inv->bind_param("i", $id_to_delete);
                $stmt_delete_inv->execute();
            }
            $stmt_delete_inv->close();
        }

        // If all queries were successful, commit the transaction
        $conn->commit();

        $_SESSION['success_message'] = "Product updated successfully!";
        header('Location: ../products.php');
        exit();

    } catch (mysqli_sql_exception $exception) {
        // If any query fails, roll back the transaction
        $conn->rollback();
        $_SESSION['error_message'] = "Error updating product: " . $exception->getMessage();
        header('Location: ../edit_product.php?id=' . $product_id);
        exit();
    } finally {
        $conn->close();
    }

} else {
    header('Location: ../products.php');
    exit();
}
?>
