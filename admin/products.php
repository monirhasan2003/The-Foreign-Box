<?php include '../includes/header.php'; ?>
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-2">
            <?php include 'admin_sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Product Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="add_product.php" class="btn btn-primary">Add Product</a>
                </div>
            </div>

            <!-- GEMINI_EDIT_SECTION: product_table_start -->
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include '../includes/database.php';

                        $sql = "SELECT p.product_id, p.sku, p.name, p.price, p.is_active, SUM(i.quantity) as total_stock
                                FROM products p
                                LEFT JOIN inventory i ON p.product_id = i.product_id
                                GROUP BY p.product_id
                                ORDER BY p.product_id DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $status = $row['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                                echo "<tr>";
                                echo "<td>" . $row['product_id'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['sku']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>$" . number_format($row['price'], 2) . "</td>";
                                echo "<td>" . ($row['total_stock'] ?? 0) . "</td>";
                                echo "<td>" . $status . "</td>";
                                echo "<td>";
                                echo "<a href='edit_product.php?id=" . $row['product_id'] . "' class='btn btn-sm btn-outline-secondary'>Edit</a> ";
                                echo "<a href='actions/delete_product.php?id=" . $row['product_id'] . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Are you sure?\");'>Delete</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No products found</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- GEMINI_EDIT_SECTION: product_table_end -->

        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>