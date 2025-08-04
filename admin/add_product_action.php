<?php
// This script handles the logic for adding a new product to the database.

// Include the database connection file.
// IMPORTANT: Make sure the path to your database connection file is correct.
require_once '../../config/database.php'; // Assuming config is in the root directory

// Start a session to store messages (e.g., success or error messages)
session_start();

// --- Main Logic ---

// 1. Check if the form was submitted using the POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 2. Sanitize and retrieve form data
    $sku = trim($_POST['sku']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? $_POST['sale_price'] : NULL;
    $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
    $inventory_data = isset($_POST['inventory']) ? $_POST['inventory'] : [];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // --- 3. Handle File Upload ---
    $main_image_url = '';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $upload_dir = '../../uploads/products/'; // Create this directory in your project root
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        // Create a unique filename to prevent overwriting
        $unique_filename = uniqid('product_', true) . '.' . $file_extension;
        $target_file = $upload_dir . $unique_filename;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target_file)) {
            // Store the relative path to be saved in the database
            $main_image_url = 'uploads/products/' . $unique_filename;
        } else {
            $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
            header('Location: ../add_product.php');
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Product image is required.";
        header('Location: ../add_product.php');
        exit();
    }


    // --- 4. Database Transaction ---
    // A transaction ensures that all database operations succeed. If any one fails,
    // all previous operations are rolled back, keeping the database consistent.

    $conn->begin_transaction();

    try {
        // Step A: Insert into the `products` table
        $stmt_product = $conn->prepare(
            "INSERT INTO products (sku, name, description, price, sale_price, main_image_url, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt_product->bind_param("sssddssi", $sku, $name, $description, $price, $sale_price, $main_image_url, $is_featured, $is_active);
        $stmt_product->execute();

        // Get the ID of the product we just inserted
        $product_id = $conn->insert_id;

        // Step B: Insert into the `product_categories` table
        if (!empty($categories)) {
            $stmt_category = $conn->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
            foreach ($categories as $category_id) {
                $stmt_category->bind_param("ii", $product_id, $category_id);
                $stmt_category->execute();
            }
        }

        // Step C: Insert into the `inventory` table
        if (!empty($inventory_data)) {
            $stmt_inventory = $conn->prepare("INSERT INTO inventory (product_id, attribute_size, attribute_color, quantity) VALUES (?, ?, ?, ?)");
            foreach ($inventory_data as $item) {
                $size = !empty($item['size']) ? trim($item['size']) : NULL;
                $color = !empty($item['color']) ? trim($item['color']) : NULL;
                $quantity = (int)$item['quantity'];

                $stmt_inventory->bind_param("issi", $product_id, $size, $color, $quantity);
                $stmt_inventory->execute();
            }
        }

        // If all queries were successful, commit the transaction
        $conn->commit();

        // Set success message and redirect
        $_SESSION['success_message'] = "Product added successfully!";
        header('Location: ../products.php'); // Redirect to the product list page
        exit();

    } catch (mysqli_sql_exception $exception) {
        // If any query fails, roll back the transaction
        $conn->rollback();

        // Set error message and redirect back to the form
        // In a production environment, you should log the detailed error.
        $_SESSION['error_message'] = "Error adding product: " . $exception->getMessage();
        header('Location: ../add_product.php');
        exit();
    } finally {
        // Close the prepared statements
        if (isset($stmt_product)) $stmt_product->close();
        if (isset($stmt_category)) $stmt_category->close();
        if (isset($stmt_inventory)) $stmt_inventory->close();
        // Close the database connection
        $conn->close();
    }

} else {
    // If the form was not submitted via POST, redirect to the form page
    header('Location: ../add_product.php');
    exit();
}
?>
