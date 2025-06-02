<?php
require_once 'config/database.php';
include 'includes/header.php';

$conn = connectDB();

// Get categories
$categories = $conn->query("SELECT * FROM categories");

// Get selected categories from URL
$selected_categories = isset($_GET['categories']) ? explode(',', $_GET['categories']) : [];

// Build query based on filters
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.category_id";

if (!empty($selected_categories)) {
    $categories_str = implode(',', array_map('intval', $selected_categories));
    $query .= " WHERE p.category_id IN ($categories_str)";
}

$products = $conn->query($query);
?>

<div class="container fade-in" style="padding: 2rem 0;">
    <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
        <!-- Filters Sidebar -->
        <div class="card filter-sidebar">
            <div class="filter-header">
                <h3><i class="fas fa-filter"></i> Filters</h3>
                <button onclick="clearFilters()" class="btn-clear">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>
            
            <div class="filter-section">
                <h4>Categories</h4>
                <div class="category-filters">
                    <?php while($category = $categories->fetch_assoc()): ?>
                        <label class="filter-checkbox">
                            <input type="checkbox" 
                                   value="<?php echo $category['category_id']; ?>"
                                   class="category-filter"
                                   <?php echo in_array($category['category_id'], $selected_categories) ? 'checked' : ''; ?>>
                            <span class="checkmark"></span>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </label>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <button onclick="applyFilters()" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                Apply Filters
            </button>
        </div>
        
        <!-- Products Grid -->
        <div>
            <?php if ($products->num_rows === 0): ?>
                <div class="card" style="text-align: center; padding: 3rem;">
                    <i class="fas fa-box-open" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                    <h2>No Products Found</h2>
                    <p style="margin: 1rem 0;">Try adjusting your filters or search criteria.</p>
                    <button onclick="clearFilters()" class="btn btn-primary">Reset Filters</button>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php while($product = $products->fetch_assoc()): ?>
                        <div class="card product-card">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     onerror="this.src='assets/images/placeholder.jpg'">
                                <div class="category-tag">
                                    <?php echo htmlspecialchars($product['category_name']); ?>
                                </div>
                            </div>
                            
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                                
                                <div class="product-footer">
                                    <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
                                    <button onclick="addToCart(<?php echo $product['product_id']; ?>)" 
                                            class="btn btn-primary">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function clearFilters() {
    window.location.href = 'products.php';
}

function applyFilters() {
    const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked'))
        .map(checkbox => checkbox.value);
    
    if (selectedCategories.length > 0) {
        window.location.href = 'products.php?categories=' + selectedCategories.join(',');
    } else {
        window.location.href = 'products.php';
    }
}

function addToCart(productId) {
    <?php if(!isset($_SESSION['user_id'])): ?>
        window.location.href = 'login.php';
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
            alert(data.message || 'Error adding product to cart');
        }
    });
}
</script>

<?php
$conn->close();
include 'includes/footer.php';
?> 