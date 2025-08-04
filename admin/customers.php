<?php include '../includes/header.php'; ?>
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-2">
            <?php include 'admin_sidebar.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Customer Management</h1>
            </div>

            <!-- GEMINI_EDIT_SECTION: customers_table_start -->
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Date Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include '../includes/database.php';

                        $sql = "SELECT user_id, CONCAT(first_name, ' ', last_name) AS name, email, created_at FROM users WHERE role = 'customer' ORDER BY created_at DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['user_id'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . date("Y-m-d", strtotime($row['created_at'])) . "</td>";
                                echo "<td><a href='view_customer.php?id=" . $row['user_id'] . "' class='btn btn-sm btn-outline-secondary'>View Profile</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No customers found.</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- GEMINI_EDIT_SECTION: customers_table_end -->

        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>