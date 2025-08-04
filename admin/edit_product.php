<?php
// This is the Edit Product page for the admin panel.
session_start();

// Include the database connection file.
require_once '../../config/database.php';

// --- 1. Get Product ID and Validate ---
// Check if an ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid product ID.";
    header('Location: products.php');
    exit();
}
$product_id = (int)$_GET['id'];

// --- 2. Fetch Product Data from Database ---
// We need to fetch data from multiple tables: products, product_categories, and inventory.

$product = null;
$product_categories = [];
$inventory = [];
$all_categories = [];

try {
    // Fetch main product details
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    } else {
        throw new Exception("Product not found.");
    }
    $stmt->close();

    // Fetch the categories this product belongs to
    $stmt = $conn->prepare("SELECT category_id FROM product_categories WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $product_categories[] = $row['category_id'];
    }
    $stmt->close();

    // Fetch inventory variations for this product
    $stmt = $conn->prepare("SELECT * FROM inventory WHERE product_id = ? ORDER BY inventory_id ASC");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $inventory = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch all available categories to display as checkboxes
    $all_categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
    $all_categories = $all_categories_result->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $_SESSION['error_message'] = "Error fetching product data: " . $e->getMessage();
    header('Location: products.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 900px; }
        .card { border: none; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .form-label { font-weight: 600; }
        .inventory-item { display: flex; gap: 15px; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px; }
        .inventory-item:last-child { border-bottom: none; }
        .current-image { max-width: 150px; border-radius: 8px; margin-top: 10px; }
    </style>
</head>
<body>

    <!-- GEMINI_EDIT_SECTION: edit_product_form_start -->
    <div class="container mt-5 mb-5">
        <div class="card p-4">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Edit Product</h2>

                <!-- The form will submit to the edit action file -->
                <form action="actions/edit_product_action.php" method="POST" enctype="multipart/form-data">
                    <!-- Hidden input to pass the product ID -->
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                    <!-- Product Information Section -->
                    <h5 class="mb-3">Product Information</h5>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <!-- Pricing Section -->
                    <h5 class="mb-3 mt-4">Pricing</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Regular Price ($)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sale_price" class="form-label">Sale Price ($) (Optional)</label>
                            <input type="number" class="form-control" id="sale_price" name="sale_price" step="0.01" value="<?php echo htmlspecialchars($product['sale_price']); ?>">
                        </div>
                    </div>

                    <!-- Categories Section -->
                    <h5 class="mb-3 mt-4">Categories</h5>
                    <div class="mb-3 p-3 bg-light rounded">
                        <label class="form-label">Select one or more categories:</label>
                        <div class="row">
                            <?php foreach ($all_categories as $category): ?>
                                <?php $is_checked = in_array($category['category_id'], $product_categories); ?>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $category['category_id']; ?>" id="cat_<?php echo $category['category_id']; ?>" <?php echo $is_checked ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="cat_<?php echo $category['category_id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Inventory Management Section -->
                    <h5 class="mb-3 mt-4">Inventory & Variations</h5>
                    <div id="inventory-container" class="mb-3 p-3 bg-light rounded">
                        <?php foreach ($inventory as $index => $item): ?>
                            <div class="inventory-item">
                                <!-- Hidden input for inventory ID to identify existing entries -->
                                <input type="hidden" name="inventory[<?php echo $index; ?>][id]" value="<?php echo $item['inventory_id']; ?>">
                                <div class="flex-grow-1">
                                    <label class="form-label">Size</label>
                                    <input type="text" class="form-control" name="inventory[<?php echo $index; ?>][size]" value="<?php echo htmlspecialchars($item['attribute_size']); ?>">
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label">Color</label>
                                    <input type="text" class="form-control" name="inventory[<?php echo $index; ?>][color]" value="<?php echo htmlspecialchars($item['attribute_color']); ?>">
                                </div>
                                <div>
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control" name="inventory[<?php echo $index; ?>][quantity]" value="<?php echo htmlspecialchars($item['quantity']); ?>" required>
                                </div>
                                <div class="align-self-end">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeInventoryItem(this)">Remove</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" onclick="addInventoryItem()">+ Add Another Variation</button>

                    <!-- Image and Status Section -->
                    <h5 class="mb-3 mt-4">Image & Status</h5>
                     <div class="mb-3">
                        <label for="main_image" class="form-label">Update Product Image (Optional)</label>
                        <input class="form-control" type="file" id="main_image" name="main_image">
                        <p class="form-text">Only choose a file if you want to replace the current image.</p>
                        <img src="../<?php echo htmlspecialchars($product['main_image_url']); ?>" alt="Current Image" class="current-image">
                        <input type="hidden" name="current_image_path" value="<?php echo htmlspecialchars($product['main_image_url']); ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                             <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1" <?php echo $product['is_featured'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_featured">Featured Product</label>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                             <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">Product is Active</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Update Product</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <!-- GEMINI_EDIT_SECTION: edit_product_form_end -->


    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript for Dynamic Inventory Fields -->
    <script>
        // Start index from the number of existing items to avoid conflicts
        let inventoryIndex = <?php echo count($inventory); ?>;

        function addInventoryItem() {
            const container = document.getElementById('inventory-container');
            const newItem = document.createElement('div');
            newItem.classList.add('inventory-item');
            // Note: New items won't have an inventory ID, so the hidden input is omitted.
            // The backend will know to INSERT instead of UPDATE for these.
            newItem.innerHTML = `
                <div class="flex-grow-1">
                    <label class="form-label">Size</label>
                    <input type="text" class="form-control" name="inventory[${inventoryIndex}][size]" placeholder="e.g., L">
                </div>
                <div class="flex-grow-1">
                    <label class="form-label">Color</label>
                    <input type="text" class="form-control" name="inventory[${inventoryIndex}][color]" placeholder="e.g., Green">
                </div>
                <div>
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" name="inventory[${inventoryIndex}][quantity]" required>
                </div>
                <div class="align-self-end">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeInventoryItem(this)">Remove</button>
                </div>
            `;
            container.appendChild(newItem);
            inventoryIndex++;
        }

        function removeInventoryItem(button) {
            const item = button.closest('.inventory-item');
            if (document.querySelectorAll('.inventory-item').length > 1) {
                item.remove();
            } else {
                alert("You must have at least one inventory entry.");
            }
        }
    </script>
</body>
</html>
