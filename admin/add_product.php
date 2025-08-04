<?php
// This is the Add Product page for the admin panel.

// We will need to include the database connection file here
// For now, we are assuming it's in a config folder.
// require_once '../config/database.php';

// --- MOCK DATABASE CONNECTION & DATA FOR DEMONSTRATION ---
// In a real scenario, this data would come from your database.
// This is a placeholder until we write the logic to fetch categories.
$categories = [
    (object)['category_id' => 1, 'name' => 'Men\'s Fashion'],
    (object)['category_id' => 2, 'name' => 'Women\'s Fashion'],
    (object)['category_id' => 3, 'name' => 'Electronics'],
    (object)['category_id' => 4, 'name' => 'Groceries']
];
// --- END MOCK DATA ---

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 900px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 600;
        }
        .inventory-item {
            display: flex;
            gap: 15px;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .inventory-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

    <!-- GEMINI_EDIT_SECTION: add_product_form_start -->
    <div class="container mt-5 mb-5">
        <div class="card p-4">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Add a New Product</h2>

                <!-- The form will submit to the action file -->
                <form action="actions/add_product_action.php" method="POST" enctype="multipart/form-data">

                    <!-- Product Information Section -->
                    <h5 class="mb-3">Product Information</h5>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sku" class="form-label">SKU (Stock Keeping Unit)</label>
                            <input type="text" class="form-control" id="sku" name="sku" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>

                    <!-- Pricing Section -->
                    <h5 class="mb-3 mt-4">Pricing</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Regular Price ($)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sale_price" class="form-label">Sale Price ($) (Optional)</label>
                            <input type="number" class="form-control" id="sale_price" name="sale_price" step="0.01">
                        </div>
                    </div>

                    <!-- Categories Section -->
                    <h5 class="mb-3 mt-4">Categories</h5>
                    <div class="mb-3 p-3 bg-light rounded">
                        <label class="form-label">Select one or more categories:</label>
                        <div class="row">
                            <?php foreach ($categories as $category): ?>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="categories[]" value="<?php echo $category->category_id; ?>" id="cat_<?php echo $category->category_id; ?>">
                                        <label class="form-check-label" for="cat_<?php echo $category->category_id; ?>">
                                            <?php echo htmlspecialchars($category->name); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Inventory Management Section -->
                    <h5 class="mb-3 mt-4">Inventory & Variations</h5>
                    <div id="inventory-container" class="mb-3 p-3 bg-light rounded">
                        <!-- Initial Inventory Item -->
                        <div class="inventory-item">
                            <div class="flex-grow-1">
                                <label class="form-label">Size (e.g., S, M, XL)</label>
                                <input type="text" class="form-control" name="inventory[0][size]" placeholder="Leave blank if not applicable">
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label">Color (e.g., Red, Blue)</label>
                                <input type="text" class="form-control" name="inventory[0][color]" placeholder="Leave blank if not applicable">
                            </div>
                            <div>
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" name="inventory[0][quantity]" required>
                            </div>
                            <div class="align-self-end">
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeInventoryItem(this)">Remove</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" onclick="addInventoryItem()">+ Add Another Variation</button>

                    <!-- Image and Status Section -->
                    <h5 class="mb-3 mt-4">Image & Status</h5>
                     <div class="mb-3">
                        <label for="main_image" class="form-label">Main Product Image</label>
                        <input class="form-control" type="file" id="main_image" name="main_image" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                             <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1">
                                <label class="form-check-label" for="is_featured">Featured Product (Show on Homepage)</label>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                             <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">Product is Active (Visible on site)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Add Product</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <!-- GEMINI_EDIT_SECTION: add_product_form_end -->


    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript for Dynamic Inventory Fields -->
    <script>
        let inventoryIndex = 1;

        function addInventoryItem() {
            const container = document.getElementById('inventory-container');
            const newItem = document.createElement('div');
            newItem.classList.add('inventory-item');
            newItem.innerHTML = `
                <div class="flex-grow-1">
                    <label class="form-label">Size</label>
                    <input type="text" class="form-control" name="inventory[${inventoryIndex}][size]" placeholder="Leave blank if not applicable">
                </div>
                <div class="flex-grow-1">
                    <label class="form-label">Color</label>
                    <input type="text" class="form-control" name="inventory[${inventoryIndex}][color]" placeholder="Leave blank if not applicable">
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
            // Do not remove the last item
            if (document.querySelectorAll('.inventory-item').length > 1) {
                item.remove();
            } else {
                alert("You must have at least one inventory entry.");
            }
        }
    </script>
</body>
</html>
