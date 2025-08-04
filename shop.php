<?php include 'includes/header.php'; ?>

<!-- GEMINI_EDIT_SECTION: shop_page_start -->
<div class="container mt-5">
    <div class="row">
        <!-- GEMINI_EDIT_SECTION: filters_sidebar_start -->
        <div class="col-lg-3">
            <h4>Filters</h4>
            <hr>
            <!-- Brand Filter -->
            <h5>Brand</h5>
            <ul class="list-unstyled">
                <li><input class="form-check-input" type="checkbox" value="" id="brand1"><label class="form-check-label" for="brand1"> Brand 1</label></li>
                <li><input class="form-check-input" type="checkbox" value="" id="brand2"><label class="form-check-label" for="brand2"> Brand 2</label></li>
            </ul>
            <hr>
            <!-- Color Filter -->
            <h5>Color</h5>
            <ul class="list-unstyled">
                <li><input class="form-check-input" type="checkbox" value="" id="color1"><label class="form-check-label" for="color1"> Color 1</label></li>
                <li><input class="form-check-input" type="checkbox" value="" id="color2"><label class="form-check-label" for="color2"> Color 2</label></li>
            </ul>
            <hr>
            <!-- Rating Filter -->
            <h5>Rating</h5>
            <!-- ... -->
        </div>
        <!-- GEMINI_EDIT_SECTION: filters_sidebar_end -->

        <!-- GEMINI_EDIT_SECTION: product_grid_start -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Shop</h3>
                <div class="view-options">
                    <button class="btn btn-light"><i class="fas fa-th"></i></button>
                    <button class="btn btn-light"><i class="fas fa-list"></i></button>
                </div>
            </div>
            <div class="row">
                <?php
                include 'includes/database.php';

                // Base SQL query
                $sql = "SELECT p.* FROM products p";

                // Check for category filter
                if (isset($_GET['category']) && !empty($_GET['category'])) {
                    $category_slug = $conn->real_escape_string($_GET['category']);
                    $sql .= " JOIN product_categories pc ON p.product_id = pc.product_id JOIN categories c ON pc.category_id = c.category_id WHERE c.slug = ? AND p.is_active = 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $category_slug);
                } else {
                    $sql .= " WHERE p.is_active = 1";
                    $stmt = $conn->prepare($sql);
                }

                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="col-md-4 mb-4"><div class="card h-100"><a href="product.php?id=' . $row["product_id"] . '"><img src="' . htmlspecialchars($row["main_image_url"]) . '" class="card-img-top" alt="' . htmlspecialchars($row["name"]) . '"></a><div class="card-body"><h5 class="card-title"><a href="product.php?id=' . $row["product_id"] . '" class="text-dark text-decoration-none">' . htmlspecialchars($row["name"]) . '</a></h5><p class="card-text">
    </div>
</div>

<!-- GEMINI_EDIT_SECTION: quick_view_modal_start -->
<div class="modal fade" id="quickViewModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Product Quick View</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Quick view content loaded via Ajax -->
      </div>
    </div>
  </div>
</div>
<!-- GEMINI_EDIT_SECTION: quick_view_modal_end -->
<!-- GEMINI_EDIT_SECTION: shop_page_end -->

<?php include 'includes/footer.php'; ?> . number_format($row["price"], 2) . '</p></div><div class="card-footer bg-transparent border-top-0"><a href="product.php?id=' . $row["product_id"] . '" class="btn btn-primary">View Product</a></div></div></div>';
                    }
                } else {
                    echo "<p>No products found in this category.</p>";
                }
                $stmt->close();
                ?>
            </div>
        </div>
        <!-- GEMINI_EDIT_SECTION: product_grid_end -->
    </div>
</div>

<!-- GEMINI_EDIT_SECTION: quick_view_modal_start -->
<div class="modal fade" id="quickViewModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Product Quick View</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Quick view content loaded via Ajax -->
      </div>
    </div>
  </div>
</div>
<!-- GEMINI_EDIT_SECTION: quick_view_modal_end -->
<!-- GEMINI_EDIT_SECTION: shop_page_end -->

<?php include 'includes/footer.php'; ?>