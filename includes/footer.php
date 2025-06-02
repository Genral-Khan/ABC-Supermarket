<?php
if (!isset($conn)) {
    require_once 'config/database.php';
    $conn = connectDB();
}

// Fetch categories for footer
$footerCategories = $conn->query("SELECT * FROM categories");
?>

    </div><!-- Close content container -->
    
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <!-- Company Info -->
                <div class="footer-section">
                    <h3 class="footer-title">ABC Supermarket</h3>
                    <p class="footer-description">Your one-stop shop for quality products at great prices.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Categories -->
                <div class="footer-section">
                    <h3 class="footer-title">Categories</h3>
                    <ul class="footer-links">
                        <?php while($category = $footerCategories->fetch_assoc()): ?>
                            <li>
                                <a href="products.php?categories=<?php echo $category['category_id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div class="footer-section">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">All Products</a></li>
                        <li><a href="cart.php">Cart</a></li>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="profile.php">My Account</a></li>
                            <li><a href="orders.php">My Orders</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Sign In</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-section">
                    <h3 class="footer-title">Contact Us</h3>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Shopping Street, City, Country</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>+1 234 567 8900</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>contact@abcsupermarket.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> ABC Supermarket. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            // You can implement the search logic here
            // For example, make an AJAX call to search.php
        });
    </script>
</body>
</html> 