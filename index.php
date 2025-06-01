<?php
require_once 'config/database.php';
include 'includes/header.php';

// Fetch featured products
$conn = connectDB();
$featured_products = $conn->query("SELECT * FROM products ORDER BY RAND() LIMIT 6");
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
        <h1 style="font-size: 3rem; margin-bottom: 1rem;">Fresh Grocery & Clothes</h1>
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">Your one-stop shop for quality products</p>
        <a href="products.php" class="btn btn-primary">Shop Now</a>
    </div>
    
    <!-- Categories Section -->
    <h2 style="text-align: center; margin: 3rem 0;">Our Categories</h2>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-bottom: 4rem;">
        <!-- Grocery Category -->
        <div class="card" style="text-align: center;">
            <img src="assets/images/grocery.jpg" alt="Grocery" style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px;">
            <h3 style="margin: 1rem 0;">Grocery</h3>
            <p style="margin-bottom: 1rem;">Fresh fruits, vegetables, and daily essentials</p>
            <a href="products.php?category=1" class="btn btn-primary">Browse Grocery</a>
        </div>
        
        <!-- Fashion Category -->
        <div class="card" style="text-align: center;">
            <img src="assets/images/fashion.jpg" alt="Fashion" style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px;">
            <h3 style="margin: 1rem 0;">Fashion</h3>
            <p style="margin-bottom: 1rem;">Trendy clothes and accessories</p>
            <a href="products.php?category=2" class="btn btn-primary">Browse Fashion</a>
        </div>
        
        <!-- Electronics Category -->
        <div class="card" style="text-align: center;">
            <img src="assets/images/electronics.jpg" alt="Electronics" style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px;">
            <h3 style="margin: 1rem 0;">Electronics</h3>
            <p style="margin-bottom: 1rem;">Latest gadgets and accessories</p>
            <a href="products.php?category=3" class="btn btn-primary">Browse Electronics</a>
        </div>
    </div>
    
    <!-- Featured Products Section -->
    <h2 style="text-align: center; margin: 3rem 0;">Featured Products</h2>
    <div class="product-grid">
        <?php while($product = $featured_products->fetch_assoc()): ?>
            <div class="card">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     style="width: 100%; height: 200px; object-fit: cover; border-radius: 5px;">
                <h3 style="margin: 1rem 0;"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p style="margin-bottom: 1rem;"><?php echo htmlspecialchars($product['description']); ?></p>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 1.2rem; font-weight: bold;">$<?php echo number_format($product['price'], 2); ?></span>
                    <button onclick="addToCart(<?php echo $product['product_id']; ?>)" class="btn btn-primary">Add to Cart</button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    
    <!-- Services Section -->
    <div style="margin: 4rem 0;">
        <h2 style="text-align: center; margin-bottom: 3rem;">Our Services</h2>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem;">
            <div class="card" style="text-align: center;">
                <i class="fas fa-truck" style="font-size: 2rem; color: var(--primary-color);"></i>
                <h3 style="margin: 1rem 0;">Free Delivery</h3>
                <p>On orders above $50</p>
            </div>
            
            <div class="card" style="text-align: center;">
                <i class="fas fa-undo" style="font-size: 2rem; color: var(--primary-color);"></i>
                <h3 style="margin: 1rem 0;">Easy Returns</h3>
                <p>30-day return policy</p>
            </div>
            
            <div class="card" style="text-align: center;">
                <i class="fas fa-headset" style="font-size: 2rem; color: var(--primary-color);"></i>
                <h3 style="margin: 1rem 0;">24/7 Support</h3>
                <p>Round the clock assistance</p>
            </div>
            
            <div class="card" style="text-align: center;">
                <i class="fas fa-shield-alt" style="font-size: 2rem; color: var(--primary-color);"></i>
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
$conn->close();
include 'includes/footer.php'; 
?> 