<?php include 'includes/header.php'; ?>

<!-- GEMINI_EDIT_SECTION: single_product_page_start -->
<div class="container mt-5">
    <?php
    include 'includes/database.php';
    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Fetch product details and total stock from inventory
    $sql = "SELECT p.*, SUM(i.quantity) AS total_stock 
            FROM products p 
            LEFT JOIN inventory i ON p.product_id = i.product_id 
            WHERE p.product_id = ? AND p.is_active = 1
            GROUP BY p.product_id";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    ?>
    <div class="row">
        <!-- GEMINI_EDIT_SECTION: product_gallery_start -->
        <div class="col-md-6">
            <img src="<?= htmlspecialchars($product['main_image_url']) ?>" class="img-fluid" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <!-- GEMINI_EDIT_SECTION: product_gallery_end -->

        <!-- GEMINI_EDIT_SECTION: product_info_start -->
        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p class="text-muted">SKU: <?= htmlspecialchars($product['sku']) ?></p>
            <h3>
                <?php if ($product['sale_price'] > 0): ?>
                    <del class="text-muted">$<?= number_format($product['price'], 2) ?></del>
                    <span class="text-danger">$<?= number_format($product['sale_price'], 2) ?></span>
                <?php else: ?>
                    $<?= number_format($product['price'], 2) ?>
                <?php endif; ?>
            </h3>
            <p>Stock Status: <span class="text-success"><?= ($product['total_stock'] > 0) ? 'In Stock (' . $product['total_stock'] . ')' : 'Out of Stock'; ?></span></p>
            
            <?php
            // Fetch unique sizes and colors for this product
            $sql_variations = "SELECT DISTINCT attribute_size, attribute_color FROM inventory WHERE product_id = ? AND quantity > 0";
            $stmt_variations = $conn->prepare($sql_variations);
            $stmt_variations->bind_param("i", $product['product_id']);
            $stmt_variations->execute();
            $result_variations = $stmt_variations->get_result();

            $sizes = [];
            $colors = [];
            while($v = $result_variations->fetch_assoc()) {
                if (!empty($v['attribute_size'])) $sizes[] = $v['attribute_size'];
                if (!empty($v['attribute_color'])) $colors[] = $v['attribute_color'];
            }
            $sizes = array_unique($sizes);
            $colors = array_unique($colors);
            $stmt_variations->close();
            ?>

            <form action="actions/add_to_cart_action.php" method="POST">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                
                <?php if (!empty($sizes)): ?>
                <div class="mb-3">
                    <label for="productSize" class="form-label">Size</label>
                    <select class="form-select" id="productSize" name="product_size">
                        <option value="">Select Size</option>
                        <?php foreach ($sizes as $size): ?>
                            <option value="<?= htmlspecialchars($size) ?>"><?= htmlspecialchars($size) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if (!empty($colors)): ?>
                <div class="mb-3">
                    <label for="productColor" class="form-label">Color</label>
                    <select class="form-select" id="productColor" name="product_color">
                        <option value="">Select Color</option>
                        <?php foreach ($colors as $color): ?>
                            <option value="<?= htmlspecialchars($color) ?>"><?= htmlspecialchars($color) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="d-flex mb-3">
                    <input type="number" class="form-control me-2" name="quantity" value="1" min="1" style="width: 70px;">
                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                    <a href="actions/add_to_wishlist_action.php?product_id=<?= htmlspecialchars($product['product_id']) ?>" class="btn btn-outline-secondary ms-2"><i class="far fa-heart"></i> Add to Wishlist</a>
                </div>
            </form>
        </div>
        <!-- GEMINI_EDIT_SECTION: product_info_end -->
    </div>

    <!-- GEMINI_EDIT_SECTION: product_details_tabs_start -->
    <div class="mt-5">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">Description</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reviews</button>
            </li>
        </ul>
        <div class="tab-content p-3 border-top-0">
            <div class="tab-pane fade show active" id="description" role="tabpanel">
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>
            <div class="tab-pane fade" id="reviews" role="tabpanel">
                <h4>Customer Reviews</h4>
                <?php
                // Fetch approved reviews for this product
                $sql_reviews = "SELECT r.rating, r.comment, r.created_at, CONCAT(u.first_name, ' ', u.last_name) AS reviewer_name 
                                FROM reviews r 
                                JOIN users u ON r.user_id = u.user_id 
                                WHERE r.product_id = ? AND r.is_approved = 1 
                                ORDER BY r.created_at DESC";
                $stmt_reviews = $conn->prepare($sql_reviews);
                $stmt_reviews->bind_param("i", $product['product_id']);
                $stmt_reviews->execute();
                $result_reviews = $stmt_reviews->get_result();

                if ($result_reviews->num_rows > 0) {
                    while($review = $result_reviews->fetch_assoc()) {
                        echo '<div class="card mb-3"><div class="card-body"><h5 class="card-title">' . htmlspecialchars($review['reviewer_name']) . ' - ';
                        for ($i = 0; $i < $review['rating']; $i++) { echo '<i class="fas fa-star text-warning"></i>'; } 
                        echo '</h5><h6 class="card-subtitle mb-2 text-muted">' . date("Y-m-d", strtotime($review['created_at'])) . '</h6><p class="card-text">' . nl2br(htmlspecialchars($review['comment'])) . '</p></div></div>';
                    }
                } else {
                    echo '<div class="alert alert-info">No reviews yet. Be the first to review this product!</div>';
                }
                $stmt_reviews->close();
                ?>

                <?php if (isset($_SESSION['user_id'])): // Only show form if logged in ?>
                <h5 class="mt-4">Submit Your Review</h5>
                <?php if (isset($_GET['review_success'])): ?>
                    <div class="alert alert-success">Your review has been submitted and is awaiting approval.</div>
                <?php endif; ?>
                <?php if (isset($_GET['review_error'])): ?>
                    <div class="alert alert-danger">Error submitting review: <?= htmlspecialchars($_GET['review_error']) ?></div>
                <?php endif; ?>
                <form action="actions/submit_review_action.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <select class="form-select" id="rating" name="rating" required>
                            <option value="">Select a rating</option>
                            <option value="5">5 Stars - Excellent</option>
                            <option value="4">4 Stars - Very Good</option>
                            <option value="3">3 Stars - Good</option>
                            <option value="2">2 Stars - Fair</option>
                            <option value="1">1 Star - Poor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
                <?php else: ?>
                    <div class="alert alert-warning mt-4">Please <a href="login.php">log in</a> to submit a review.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- GEMINI_EDIT_SECTION: product_details_tabs_end -->
    <?php
    } else {
        echo "<div class='alert alert-danger'>Product not found or is no longer available.</div>";
    }
    $stmt->close();
    $conn->close();
    ?>
</div>
<!-- GEMINI_EDIT_SECTION: single_product_page_end -->

<?php include 'includes/footer.php'; ?>