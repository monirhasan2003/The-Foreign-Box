<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<?php session_start(); ?>
<body>

<!-- GEMINI_EDIT_SECTION: header_start -->
<header>
    <div class="top-bar bg-light">
        <div class="container d-flex justify-content-between">
            <div class="contact-info">
                <span><i class="fas fa-phone"></i> +1 234 567 890</span>
                <span><i class="fas fa-envelope"></i> support@example.com</span>
            </div>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand" href="#"><b>YourLogo</b></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Shop
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <!-- Mega Menu Content Here -->
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <form class="d-flex me-3 position-relative" onsubmit="return false;">
                        <input class="form-control me-2" type="search" placeholder="Search products..." aria-label="Search" id="live-search-input" autocomplete="off">
                        <div id="search-results" class="list-group position-absolute" style="z-index: 1000; width: 100%; top: 100%;"></div>
                    </form>
                    <div class="user-icons">
                        <a href="#" class="me-2"><i class="fas fa-heart"></i></a>
                        <a href="#" class="me-2"><i class="fas fa-shopping-cart"></i> <span class="badge bg-danger">0</span></a>
                        <a href="#"><i class="fas fa-user"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
<!-- GEMINI_EDIT_SECTION: header_end -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('live-search-input');
        const searchResults = document.getElementById('search-results');
        let timeout = null;

        searchInput.addEventListener('keyup', function() {
            clearTimeout(timeout);
            const query = this.value;

            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            timeout = setTimeout(function() {
                fetch('actions/search_products.php?query=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(product => {
                                const item = document.createElement('a');
                                item.href = 'product.php?id=' + product.product_id;
                                item.classList.add('list-group-item', 'list-group-item-action', 'd-flex', 'align-items-center');
                                item.innerHTML = `
                                    <img src="${product.main_image_url}" alt="${product.name}" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                    <div>
                                        <h6 class="mb-0">${product.name}</h6>
                                        <small class="text-muted">${parseFloat(product.price).toFixed(2)}</small>
                                    </div>
                                `;
                                searchResults.appendChild(item);
                            });
                        } else {
                            searchResults.innerHTML = '<div class="list-group-item">No products found.</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching search results:', error);
                        searchResults.innerHTML = '<div class="list-group-item text-danger">Error loading results.</div>';
                    });
            }, 300); // Debounce time
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(event) {
            if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.innerHTML = '';
            }
        });
    });
</script>
