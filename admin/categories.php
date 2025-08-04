<?php
// This is the Category Management page for the admin panel.
session_start();
require_once '../../config/database.php';

// --- Fetch all categories from the database ---
// We fetch them all to build a hierarchical view and to populate the 'Parent Category' dropdown.
$categories = [];
try {
    $sql = "SELECT c1.category_id, c1.name, c1.slug, c2.name AS parent_name 
            FROM categories c1 
            LEFT JOIN categories c2 ON c1.parent_id = c2.category_id 
            ORDER BY c1.name ASC";
    $result = $conn->query($sql);
    if ($result) {
        $categories = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    // In a real application, you should log this error.
    $_SESSION['error_message'] = "Error fetching categories: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container-fluid { max-width: 1200px; }
        .card { border: none; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container-fluid mt-5 mb-5">
    <div class="row">

        <!-- Add Category Form Column -->
        <div class="col-md-4">
            <!-- GEMINI_EDIT_SECTION: add_category_form_start -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add New Category</h4>
                    <form action="actions/category_action.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug" required>
                            <div class="form-text">A URL-friendly version of the name (e.g., "mens-fashion").</div>
                        </div>
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Parent Category</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">None (Top-level Category)</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['category_id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- GEMINI_EDIT_SECTION: add_category_form_end -->
        </div>

        <!-- Category List Column -->
        <div class="col-md-8">
            <!-- GEMINI_EDIT_SECTION: category_list_start -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">All Categories</h4>
                    
                    <!-- Display Success/Error Messages -->
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Parent</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No categories found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                                            <td><?php echo htmlspecialchars($category['slug']); ?></td>
                                            <td><?php echo htmlspecialchars($category['parent_name'] ?? '—'); ?></td>
                                            <td class="text-end">
                                                <a href="edit_category.php?id=<?php echo $category['category_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="actions/category_action.php?action=delete&id=<?php echo $category['category_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this category?');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- GEMINI_EDIT_SECTION: category_list_end -->
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
