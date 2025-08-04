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
$user_data = null;

// Fetch user data
$sql_user = "SELECT first_name, last_name, email, phone FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user->num_rows > 0) {
    $user_data = $result_user->fetch_assoc();
} else {
    // User not found, destroy session and redirect to login
    session_unset();
    session_destroy();
    header("Location: login.php?error=user_not_found");
    exit();
}
$stmt_user->close();
$conn->close();

?>

<!-- GEMINI_EDIT_SECTION: client_account_start -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <?php include 'includes/client_sidebar.php'; ?>
        </div>
        <div class="col-md-9">
            <h3>Account Details</h3>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Account details updated successfully!</div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Error updating account details.</div>
            <?php endif; ?>
            <form action="actions/update_account_action.php" method="POST">
                <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?= htmlspecialchars($user_data['first_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?= htmlspecialchars($user_data['last_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" required disabled> <!-- Email usually not editable directly -->
                    <small class="form-text text-muted">Email cannot be changed here.</small>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user_data['phone']) ?>">
                </div>
                <button type="submit" class="btn btn-primary">Update Account</button>
            </form>

            <h4 class="mt-5">Change Password</h4>
            <?php if (isset($_GET['password_success'])): ?>
                <div class="alert alert-success">Password updated successfully!</div>
            <?php endif; ?>
            <?php if (isset($_GET['password_error'])): ?>
                <div class="alert alert-danger">Error updating password: <?= htmlspecialchars($_GET['password_error']) ?></div>
            <?php endif; ?>
            <form action="actions/change_password_action.php" method="POST">
                <div class="mb-3">
                    <label for="currentPassword" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                </div>
                <div class="mb-3">
                    <label for="newPassword" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                </div>
                <div class="mb-3">
                    <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirmNewPassword" name="confirmNewPassword" required>
                </div>
                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: client_account_end -->
<?php include 'includes/footer.php'; ?>