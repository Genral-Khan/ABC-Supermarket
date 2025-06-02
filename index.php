<?php
require_once 'config/database.php';
include 'includes/header.php';

// Fetch featured products
$conn = connectDB();
$featured_products = $conn->query("SELECT * FROM products ORDER BY RAND() LIMIT 6");
?>

<div class="container">
    <div class="category-grid">
        <!-- Grocery Category -->
        <div class="category-card card hover-lift">
            <img src="assets/images/grocery.jpg" alt="Grocery">
            <div class="category-overlay">
                <h3 class="category-title">Grocery</h3>
            </div>
        </div>

        <!-- Fashion Category -->
        <div class="category-card card hover-lift">
            <img src="assets/images/fashion.jpg" alt="Fashion">
            <div class="category-overlay">
                <h3 class="category-title">Fashion</h3>
            </div>
        </div>

        <!-- Electronics Category -->
        <div class="category-card card hover-lift">
            <img src="assets/images/electronics.jpg" alt="Electronics">
            <div class="category-overlay">
                <h3 class="category-title">Electronics</h3>
            </div>
        </div>
    </div>
</div>

<script>
function addToCart(productId) {
    // Check if user is logged in
    <?php if(!isset($_SESSION['user_id'])): ?>
        window.location.href = 'login.php';
        return;
    <?php endif; ?>
    
    // Add to cart using AJAX
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Product added to cart!');
        } else {
            alert('Error adding product to cart');
        }
    });
}
</script>

<?php 
$conn->close();
include 'includes/footer.php'; 
?> 