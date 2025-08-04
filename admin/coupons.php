<?php include '../includes/header.php'; ?>

<!-- GEMINI_EDIT_SECTION: admin_coupons_start -->
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-2">
            <?php include 'admin_sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Coupon Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="add_coupon.php" class="btn btn-primary">Add New Coupon</a>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Coupon <?= htmlspecialchars($_GET['success']) ?> successfully!</div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Error: <?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Discount Type</th>
                            <th>Value</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include '../includes/database.php';

                        $sql = "SELECT * FROM coupons ORDER BY expiry_date DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $status = $row['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                                $discount_display = $row['discount_type'] == 'percentage' ? $row['discount_value'] . '%' : '$' . number_format($row['discount_value'], 2);
                                echo "<tr>";
                                echo "<td>" . $row['coupon_id'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['code']) . "</td>";
                                echo "<td>" . htmlspecialchars(ucfirst($row['discount_type'])) . "</td>";
                                echo "<td>" . $discount_display . "</td>";
                                echo "<td>" . htmlspecialchars($row['expiry_date']) . "</td>";
                                echo "<td>" . $status . "</td>";
                                echo "<td>";
                                echo "<a href='edit_coupon.php?id=" . $row['coupon_id'] . "' class='btn btn-sm btn-outline-secondary'>Edit</a> ";
                                echo "<a href='actions/delete_coupon_action.php?id=" . $row['coupon_id'] . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Are you sure you want to delete this coupon?\");'>Delete</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No coupons found.</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: admin_coupons_end -->

<?php include '../includes/footer.php'; ?>
