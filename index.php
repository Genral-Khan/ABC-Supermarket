<?php
require_once 'config/database.php';
include 'includes/header.php';

// Fetch featured products
$conn = connectDB();
$featured_products = $conn->query("SELECT * FROM products ORDER BY RAND() LIMIT 6");

// Fetch categories
$categories = $conn->query("SELECT * FROM categories");
?>

<div class="container fade-in">
    <!-- Hero Section -->
    <div style="
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/images/hero-bg.jpg');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 4rem 2rem;
        text-align: center;
        border-radius: 10px;
        margin: 2rem 0;
    ">
        <h1 style="font-size: 3rem; margin-bottom: 1rem; color: var(--text-color)">Fresh Grocery & Clothes</h1>
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">Your one-stop shop for quality products</p>
        <a href="products.php" class="btn btn-primary">Shop Now</a>
    </div>
    
    <!-- Categories Section -->
    <h2 style="text-align: center; margin: 3rem 0; color: var(--text-color)">Our Categories</h2>
    <div class="category-grid">
        <?php while($category = $categories->fetch_assoc()): ?>
            <a href="products.php?categories=<?php echo $category['category_id']; ?>" class="category-card">
                <img src="<?php echo !empty($category['image_url']) ? 'uploads/' . $category['image_url'] : 'assets/images/category-placeholder.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($category['name']); ?>">
                <div class="category-overlay">
                    <h3 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h3>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
    
    <!-- Services Section -->
    <div style="margin: 4rem 0;">
        <h2 style="text-align: center; margin-bottom: 3rem; color: var(--text-color)">Our Services</h2>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem;">
            <div class="card" style="text-align: center;">
                <i class="fas fa-truck" style="font-size: 2rem; color: var(--text-color);"></i>
                <h3 style="margin: 1rem 0;">Free Delivery</h3>
                <p>On orders above $50</p>
            </div>
            
            <div class="card" style="text-align: center;">
                <i class="fas fa-undo" style="font-size: 2rem; color: var(--text-color);"></i>
                <h3 style="margin: 1rem 0;">Easy Returns</h3>
                <p>30-day return policy</p>
            </div>
            
            <div class="card" style="text-align: center;">
                <i class="fas fa-headset" style="font-size: 2rem; color: var(--text-color);"></i>
                <h3 style="margin: 1rem 0;">24/7 Support</h3>
                <p>Round the clock assistance</p>
            </div>
            
            <div class="card" style="text-align: center;">
                <i class="fas fa-shield-alt" style="font-size: 2rem; color: var(--text-color);"></i>
                <h3 style="margin: 1rem 0;">Secure Payment</h3>
                <p>100% secure checkout</p>
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
include 'includes/footer.php';
$conn->close(); 
?> 