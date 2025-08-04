<?php include 'includes/header.php'; ?>

<!-- GEMINI_EDIT_SECTION: hero_section_start -->
<section class="hero-section">
    <div class="video-background">
        <video autoplay muted loop>
            <source src="images/sample-video.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content text-center">
        <h1 class="display-4">Your Headline Here</h1>
        <p class="lead">This is a short description of your offer.</p>
        <a href="#" class="btn btn-primary btn-lg">Shop Now</a>
    </div>
</section>
<!-- GEMINI_EDIT_SECTION: hero_section_end -->

<!-- GEMINI_EDIT_SECTION: key_features_start -->
<section class="key-features-bar bg-light p-3">
    <div class="container">
        <div class="row">
            <div class="col-md-4 text-center">
                <i class="fas fa-shipping-fast fa-2x"></i>
                <p>Free Shipping</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-headset fa-2x"></i>
                <p>24/7 Support</p>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-shield-alt fa-2x"></i>
                <p>Secure Payments</p>
            </div>
        </div>
    </div>
</section>
<!-- GEMINI_EDIT_SECTION: key_features_end -->

<!-- GEMINI_EDIT_SECTION: featured_categories_start -->
<section class="featured-categories container mt-5">
    <h2 class="text-center mb-4">Featured Categories</h2>
    <div class="row">
        <?php
        include 'includes/database.php';
        $sql = "SELECT * FROM categories WHERE parent_id IS NULL LIMIT 3"; // Assuming top-level categories are featured
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4"><div class="card"><a href="shop.php?category=' . $row['slug'] . '"><img src="images/placeholder.jpg" class="card-img-top" alt="' . htmlspecialchars($row["name"]) . '"></a><div class="card-body"><h5 class="card-title">' . htmlspecialchars($row["name"]) . '</h5></div></div></div>';
            }
        }
        ?>
    </div>
</section>
<!-- GEMINI_EDIT_SECTION: featured_categories_end -->

<!-- GEMINI_EDIT_SECTION: new_arrivals_start -->
<section class="new-arrivals container mt-5">
    <h2 class="text-center mb-4">Featured Products</h2>
    <div class="row">
        <?php
        $sql = "SELECT * FROM products WHERE is_featured = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT 4";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="col-md-3 mb-4"><div class="card"><a href="product.php?id=' . $row["product_id"] . '"><img src="' . htmlspecialchars($row["main_image_url"]) . '" class="card-img-top" alt="' . htmlspecialchars($row["name"]) . '"></a><div class="card-body"><h5 class="card-title">' . htmlspecialchars($row["name"]) . '</h5><p class="card-text">$' . number_format($row["price"], 2) . '</p><a href="product.php?id=' . $row["product_id"] . '" class="btn btn-primary">View Product</a></div></div></div>';
            }
        }
        ?>
    </div>
</section>
<!-- GEMINI_EDIT_SECTION: new_arrivals_end -->

<!-- GEMINI_EDIT_SECTION: trending_now_start -->
<section class="trending-now container mt-5">
    <h2 class="text-center mb-4">Trending Now / Best Sellers</h2>
    <div id="trendingProductsCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <!-- Carousel items will be dynamically loaded here -->
            <div class="carousel-item active">
                <div class="row">
                    <?php
                    // Fetch trending/best-selling products (e.g., is_featured or based on sales data)
                    $sql_trending = "SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC LIMIT 4"; // Placeholder query
                    $result_trending = $conn->query($sql_trending);
                    if ($result_trending->num_rows > 0) {
                        while($row = $result_trending->fetch_assoc()) {
                            echo '<div class="col-md-3 mb-4"><div class="card h-100"><a href="product.php?id=' . $row["product_id"] . '"><img src="' . htmlspecialchars($row["main_image_url"]) . '" class="card-img-top" alt="' . htmlspecialchars($row["name"]) . '"></a><div class="card-body"><h5 class="card-title">' . htmlspecialchars($row["name"]) . '</h5><p class="card-text">$' . number_format($row["price"], 2) . '</p><a href="product.php?id=' . $row["product_id"] . '" class="btn btn-primary">View Product</a></div></div></div>';
                        }
                    }
                    ?>
                </div>
            </div>
            <!-- Add more carousel-item divs for more products if needed -->
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#trendingProductsCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#trendingProductsCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
<!-- GEMINI_EDIT_SECTION: trending_now_end -->

<!-- GEMINI_EDIT_SECTION: deal_of_the_day_start -->
<section class="deal-of-the-day bg-light mt-5 p-5">
    <div class="container text-center">
        <h2 class="mb-4">Deal of the Day</h2>
        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="images/placeholder.jpg" class="img-fluid rounded" alt="Deal Product">
            </div>
            <div class="col-md-6 text-md-start mt-4 mt-md-0">
                <h3>Product Name on Sale!</h3>
                <p class="lead text-danger fs-3">$49.99 <del class="text-muted fs-5">$99.99</del></p>
                <p>Limited time offer! Grab it before it's gone.</p>
                <div id="countdown" class="d-flex justify-content-center justify-content-md-start mb-4">
                    <div class="countdown-item mx-2 p-3 bg-dark text-white rounded">Days <span id="days"></span></div>
                    <div class="countdown-item mx-2 p-3 bg-dark text-white rounded">Hours <span id="hours"></span></div>
                    <div class="countdown-item mx-2 p-3 bg-dark text-white rounded">Minutes <span id="minutes"></span></div>
                    <div class="countdown-item mx-2 p-3 bg-dark text-white rounded">Seconds <span id="seconds"></span></div>
                </div>
                <a href="#" class="btn btn-primary btn-lg">Shop Now</a>
            </div>
        </div>
    </div>
</section>
<!-- GEMINI_EDIT_SECTION: deal_of_the_day_end -->

<!-- GEMINI_EDIT_SECTION: shop_by_brand_start -->
<section class="shop-by-brand container mt-5">
    <h2 class="text-center mb-4">Shop by Brand</h2>
    <div id="brandCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="row justify-content-center">
                    <div class="col-md-2 col-4 text-center"><img src="images/brand1.png" class="img-fluid" alt="Brand 1"></div>
                    <div class="col-md-2 col-4 text-center"><img src="images/brand2.png" class="img-fluid" alt="Brand 2"></div>
                    <div class="col-md-2 col-4 text-center"><img src="images/brand3.png" class="img-fluid" alt="Brand 3"></div>
                    <div class="col-md-2 col-4 text-center"><img src="images/brand4.png" class="img-fluid" alt="Brand 4"></div>
                    <div class="col-md-2 col-4 text-center"><img src="images/brand5.png" class="img-fluid" alt="Brand 5"></div>
                </div>
            </div>
            <!-- Add more carousel-item divs for more brands if needed -->
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#brandCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#brandCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
<!-- GEMINI_EDIT_SECTION: shop_by_brand_end -->

<!-- GEMINI_EDIT_SECTION: customer_testimonials_start -->
<section class="customer-testimonials container mt-5">
    <h2 class="text-center mb-4">Customer Testimonials</h2>
    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active text-center">
                <img src="images/user1.png" class="rounded-circle mb-3" alt="Customer 1" style="width: 100px; height: 100px; object-fit: cover;">
                <p class="lead">"This is the best e-commerce site I've ever used! The products are high quality and the service is excellent."</p>
                <p class="fw-bold">- Jane Doe</p>
                <div><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i></div>
            </div>
            <div class="carousel-item text-center">
                <img src="images/user2.png" class="rounded-circle mb-3" alt="Customer 2" style="width: 100px; height: 100px; object-fit: cover;">
                <p class="lead">"Amazing selection and super fast shipping. Highly recommend!"</p>
                <p class="fw-bold">- John Smith</p>
                <div><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="far fa-star text-warning"></i></div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
<!-- GEMINI_EDIT_SECTION: customer_testimonials_end -->

<!-- GEMINI_EDIT_SECTION: instagram_feed_start -->
<section class="instagram-feed mt-5">
    <h2 class="text-center mb-4">Follow Us on Instagram</h2>
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6 mb-4"><img src="images/insta1.jpg" class="img-fluid rounded" alt="Instagram Post"></div>
            <div class="col-md-3 col-6 mb-4"><img src="images/insta2.jpg" class="img-fluid rounded" alt="Instagram Post"></div>
            <div class="col-md-3 col-6 mb-4"><img src="images/insta3.jpg" class="img-fluid rounded" alt="Instagram Post"></div>
            <div class="col-md-3 col-6 mb-4"><img src="images/insta4.jpg" class="img-fluid rounded" alt="Instagram Post"></div>
        </div>
    </div>
</section>
<!-- GEMINI_EDIT_SECTION: instagram_feed_end -->


<?php include 'includes/footer.php'; ?>

<script>
    // Countdown Timer for Deal of the Day
    const countdownDate = new Date().getTime() + (24 * 60 * 60 * 1000); // 24 hours from now

    const x = setInterval(function() {
        const now = new Date().getTime();
        const distance = countdownDate - now;

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("days").innerHTML = days;
        document.getElementById("hours").innerHTML = hours;
        document.getElementById("minutes").innerHTML = minutes;
        document.getElementById("seconds").innerHTML = seconds;

        if (distance < 0) {
            clearInterval(x);
            document.getElementById("countdown").innerHTML = "EXPIRED";
        }
    }, 1000);
</script>