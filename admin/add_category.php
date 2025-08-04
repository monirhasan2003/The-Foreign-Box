<?php
include '../includes/header.php';
include '../includes/database.php';

// Fetch existing categories to populate the parent category dropdown
$sql_categories = "SELECT category_id, name FROM categories ORDER BY name ASC";
$result_categories = $conn->query($sql_categories);

?>

<!-- GEMINI_EDIT_SECTION: add_category_form_start -->
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-2">
            <?php include 'admin_sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Add New Category</h1>
            </div>

            <form action="actions/add_category_action.php" method="POST">
                <div class="mb-3">
                    <label for="categoryName" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="categoryName" name="categoryName" required>
                </div>
                <div class="mb-3">
                    <label for="categorySlug" class="form-label">Category Slug</label>
                    <input type="text" class="form-control" id="categorySlug" name="categorySlug" placeholder="e.g., electronics-gadgets" required>
                    <small class="form-text text-muted">A URL-friendly version of the category name. Lowercase, no spaces (use hyphens).</small>
                </div>
                <div class="mb-3">
                    <label for="parentCategory" class="form-label">Parent Category (Optional)</label>
                    <select class="form-select" id="parentCategory" name="parentCategory">
                        <option value="">None</option>
                        <?php
                        if ($result_categories->num_rows > 0) {
                            while($row = $result_categories->fetch_assoc()) {
                                echo '<option value="' . $row['category_id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                            }
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Category</button>
                <a href="categories.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: add_category_form_end -->

<?php include '../includes/footer.php'; ?>