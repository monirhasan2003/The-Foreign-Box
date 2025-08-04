<?php
include '../includes/header.php';
include '../includes/database.php';

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$category = null;

if ($category_id > 0) {
    // Fetch category data
    $sql_category = "SELECT category_id, name, slug, parent_id FROM categories WHERE category_id = ?";
    $stmt_category = $conn->prepare($sql_category);
    $stmt_category->bind_param("i", $category_id);
    $stmt_category->execute();
    $result_category = $stmt_category->get_result();
    $category = $result_category->fetch_assoc();
    $stmt_category->close();
}

// Fetch existing categories to populate the parent category dropdown
$sql_categories = "SELECT category_id, name FROM categories WHERE category_id != ? ORDER BY name ASC";
$stmt_categories = $conn->prepare($sql_categories);
$stmt_categories->bind_param("i", $category_id);
$stmt_categories->execute();
$result_categories = $stmt_categories->get_result();

?>

<!-- GEMINI_EDIT_SECTION: edit_category_form_start -->
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-2">
            <?php include 'admin_sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Category</h1>
            </div>

            <?php if ($category): ?>
            <form action="actions/edit_category_action.php" method="POST">
                <input type="hidden" name="categoryId" value="<?= htmlspecialchars($category['category_id']) ?>">
                <div class="mb-3">
                    <label for="categoryName" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="categoryName" name="categoryName" value="<?= htmlspecialchars($category['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="categorySlug" class="form-label">Category Slug</label>
                    <input type="text" class="form-control" id="categorySlug" name="categorySlug" value="<?= htmlspecialchars($category['slug']) ?>" placeholder="e.g., electronics-gadgets" required>
                    <small class="form-text text-muted">A URL-friendly version of the category name. Lowercase, no spaces (use hyphens).</small>
                </div>
                <div class="mb-3">
                    <label for="parentCategory" class="form-label">Parent Category (Optional)</label>
                    <select class="form-select" id="parentCategory" name="parentCategory">
                        <option value="">None</option>
                        <?php
                        if ($result_categories->num_rows > 0) {
                            while($row = $result_categories->fetch_assoc()) {
                                $selected = ($row['category_id'] == $category['parent_id']) ? 'selected' : '';
                                echo '<option value="' . $row['category_id'] . '" ' . $selected . '>' . htmlspecialchars($row['name']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="categories.php" class="btn btn-secondary">Cancel</a>
            </form>
            <?php else: ?>
                <div class="alert alert-danger">Category not found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: edit_category_form_end -->

<?php 
$conn->close();
include '../includes/footer.php'; 
?>