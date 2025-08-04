<?php
include '../includes/header.php';
include '../includes/database.php';

// Fetch reviews
$sql_reviews = "SELECT r.review_id, r.rating, r.comment, r.is_approved, r.created_at, 
                        p.name AS product_name, 
                        CONCAT(u.first_name, ' ', u.last_name) AS reviewer_name 
                FROM reviews r 
                JOIN products p ON r.product_id = p.product_id 
                JOIN users u ON r.user_id = u.user_id 
                ORDER BY r.created_at DESC";
$result_reviews = $conn->query($sql_reviews);

?>

<!-- GEMINI_EDIT_SECTION: admin_reviews_start -->
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-2">
            <?php include 'admin_sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Review Management</h1>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Review <?= htmlspecialchars($_GET['success']) ?> successfully!</div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Error: <?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_reviews->num_rows > 0): ?>
                            <?php while($row = $result_reviews->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['review_id']) ?></td>
                                    <td><a href="../product.php?id=<?= htmlspecialchars($row['product_id']) ?>" target="_blank"><?= htmlspecialchars($row['product_name']) ?></a></td>
                                    <td><?= htmlspecialchars($row['reviewer_name']) ?></td>
                                    <td>
                                        <?php for ($i = 0; $i < $row['rating']; $i++): ?>
                                            <i class="fas fa-star text-warning"></i>
                                        <?php endfor; ?>
                                    </td>
                                    <td><?= nl2br(htmlspecialchars(substr($row['comment'], 0, 100))) ?>...</td>
                                    <td><?= date("Y-m-d", strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <?php if ($row['is_approved']): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!$row['is_approved']): ?>
                                            <a href="actions/approve_review_action.php?id=<?= htmlspecialchars($row['review_id']) ?>" class="btn btn-sm btn-success">Approve</a>
                                        <?php endif; ?>
                                        <a href="actions/delete_review_action.php?id=<?= htmlspecialchars($row['review_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center">No reviews found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: admin_reviews_end -->

<?php 
$conn->close();
include '../includes/footer.php'; 
?>