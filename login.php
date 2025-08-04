<?php include 'includes/header.php'; ?>

<!-- GEMINI_EDIT_SECTION: login_page_start -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Login</h3>
                </div>
                <div class="card-body">
                    <form action="actions/login_action.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="mt-3 text-center">
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
                        <hr>
                        <!-- Google Login Button -->
                        <a href="actions/google_oauth_callback.php" class="btn btn-outline-danger w-100"><i class="fab fa-google me-2"></i>Login with Google</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- GEMINI_EDIT_SECTION: login_page_end -->

<?php include 'includes/footer.php'; ?>