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
    <div class="hero-section">
        <div class="hero-content">
            <h1>Welcome to ABC Supermarket</h1>
            <p class="hero-subtitle">Your Premier Shopping Destination</p>
            <div class="hero-features">
                <div class="hero-feature">
                    <i class="fas fa-check-circle"></i>
                    <span>Fresh Groceries</span>
                </div>
                <div class="hero-feature">
                    <i class="fas fa-check-circle"></i>
                    <span>Fashionable Clothes</span>
                </div>
                <div class="hero-feature">
                    <i class="fas fa-check-circle"></i>
                    <span>Advance Electronics</span>
                </div>
            </div>
            <div class="hero-cta">
                <a href="products.php" class="btn btn-primary">
                    <span>Shop Now</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="hero-image">
            <img src="assets/images/hero.png" alt="Shopping Experience">
        </div>
    </div>
    
    <!-- Categories Section -->
    <section id="categories">
        <h2 class="section-title">Our Categories</h2>
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
    </section>
    
    <!-- Services Section -->
    <section class="services-section">
        <h2 class="section-title">Why Choose Us</h2>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Free Delivery</h3>
                <p>Free shipping on orders above AED 50</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <h3>Easy Returns</h3>
                <p>Hassle-free 30-day return policy</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Round the clock customer assistance</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure Payment</h3>
                <p>100% secure payment gateway</p>
            </div>
        </div>
    </section>
</div>

<script>
function addToCart(productId) {
    <?php if(!isset($_SESSION['user_id'])): ?>
        openModal('loginModal');
        return;
    <?php endif; ?>
    
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