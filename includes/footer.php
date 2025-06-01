    </div><!-- Close content container -->
    
    <footer class="footer">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem;">
                <div>
                    <h3>ABC Supermarket</h3>
                    <p>Your one-stop shop for groceries, fashion, and electronics.</p>
                </div>
                
                <div>
                    <h4>Quick Links</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="about.php" style="color: white; text-decoration: none;">About Us</a></li>
                        <li><a href="contact.php" style="color: white; text-decoration: none;">Contact</a></li>
                        <li><a href="privacy.php" style="color: white; text-decoration: none;">Privacy Policy</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4>Categories</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="products.php?category=1" style="color: white; text-decoration: none;">Grocery</a></li>
                        <li><a href="products.php?category=2" style="color: white; text-decoration: none;">Fashion</a></li>
                        <li><a href="products.php?category=3" style="color: white; text-decoration: none;">Electronics</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4>Contact Us</h4>
                    <p>Email: info@abcsupermarket.com</p>
                    <p>Phone: (123) 456-7890</p>
                </div>
            </div>
            
            <div style="margin-top: 2rem; text-align: center; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
                <p>&copy; 2024 ABC Supermarket. All rights reserved.</p>
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