<?php
session_start();
include 'includes/header.php';
include 'includes/database.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch wishlist items for the logged-in user
$sql_wishlist = "SELECT w.wishlist_id, p.product_id, p.name, p.main_image_url, p.price, p.sale_price 
                 FROM wishlist w 
                 JOIN products p ON w.product_id = p.product_id 
                 WHERE w.user_id = ?";
$stmt_wishlist = $conn->prepare($sql_wishlist);
$stmt_wishlist->bind_param("i", $user_id);
$stmt_wishlist->execute();
$result_wishlist = $stmt_wishlist->get_result();
$wishlist_items = [];
while($row = $result_wishlist->fetch_assoc()) {
    $wishlist_items[] = $row;
}
$stmt_wishlist->close();
$conn->close();

?>

<!-- GEMINI_EDIT_SECTION: client_wishlist_start -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <?php include 'includes/client_sidebar.php'; ?>
        </div>
        <div class="col-md-9">
            <h3>My Wishlist</h3>
            <div class="row">
                <?php if (!empty($wishlist_items)): ?>
                    <?php foreach ($wishlist_items as $item): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <a href="product.php?id=<?= htmlspecialchars($item['product_id']) ?>">
                                    <img src="<?= htmlspecialchars($item['main_image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title"><a href="product.php?id=<?= htmlspecialchars($item['product_id']) ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($item['name']) ?></a></h5>
                                    <p class="card-text">
                                        <?php if ($item['sale_price'] > 0): ?>
                                            <del class="text-muted">$<?= number_format($item['price'], 2) ?></del>
                                            <span class="text-danger">$<?= number_format($item['sale_price'], 2) ?></span>
                                        <?php else: ?>
                                            $<?= number_format($item['price'], 2) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent border-top-0">
                                    <a href="actions/remove_from_wishlist_action.php?id=<?= htmlspecialchars($item['wishlist_id']) ?>" class="btn btn-danger btn-sm">Remove from Wishlist</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12"><div class="alert alert-info">Your wishlist is empty.</div></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: client_wishlist_end -->
<?php include 'includes/footer.php'; ?>