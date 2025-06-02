<?php
require_once 'config/database.php';
include 'includes/header.php';

$conn = connectDB();

// Get categories
$categories = $conn->query("SELECT * FROM categories");

// Get selected categories from URL
$selected_categories = isset($_GET['categories']) ? explode(',', $_GET['categories']) : [];

// Get search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query based on filters and search
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.category_id
          WHERE 1=1";

$params = array();
$types = "";

if (!empty($search)) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

if (!empty($selected_categories)) {
    $placeholders = str_repeat('?,', count($selected_categories) - 1) . '?';
    $query .= " AND p.category_id IN ($placeholders)";
    foreach ($selected_categories as $cat) {
        $params[] = $cat;
        $types .= "i";
    }
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();
$total_results = $products->num_rows;
?>

<div class="container fade-in" style="padding: 2rem 0;">
    <?php if (!empty($search)): ?>
        <div class="search-results-header">
            <div class="results-info">
                <i class="fas fa-search"></i>
                <h2>Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>
                <span class="results-count"><?php echo $total_results; ?> products found</span>
            </div>
            <?php if (!empty($selected_categories)): ?>
                <div class="filter-tags">
                    <span>Filtered by:</span>
                    <?php 
                    $categories->data_seek(0);
                    while($category = $categories->fetch_assoc()):
                        if (in_array($category['category_id'], $selected_categories)):
                    ?>
                        <span class="filter-tag">
                            <?php echo htmlspecialchars($category['name']); ?>
                            <a href="<?php 
                                $new_cats = array_diff($selected_categories, [$category['category_id']]);
                                echo 'products.php?search=' . urlencode($search) . 
                                     (!empty($new_cats) ? '&categories=' . implode(',', $new_cats) : '');
                            ?>" class="remove-filter">&times;</a>
                        </span>
                    <?php 
                        endif;
                    endwhile;
                    ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

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
                    <?php 
                    $categories->data_seek(0);
                    while($category = $categories->fetch_assoc()): 
                    ?>
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
                <div class="no-results-card">
                    <div class="no-results-content">
                        <i class="fas fa-search"></i>
                        <h2>No Products Found</h2>
                        <?php if (!empty($search)): ?>
                            <p>We couldn't find any products matching "<?php echo htmlspecialchars($search); ?>"</p>
                        <?php else: ?>
                            <p>Try adjusting your filters or search criteria.</p>
                        <?php endif; ?>
                        <div class="no-results-actions">
                            <?php if (!empty($search)): ?>
                                <a href="products.php" class="btn btn-primary">View All Products</a>
                            <?php endif; ?>
                            <?php if (!empty($selected_categories)): ?>
                                <button onclick="clearFilters()" class="btn btn-secondary">Clear Filters</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php while($product = $products->fetch_assoc()): ?>
                        <div class="card product-card">
                            <div class="product-image">
                                <?php 
                                $image_path = !empty($product['image_url']) ? "uploads/" . $product['image_url'] : "";
                                if (!empty($image_path) && file_exists($image_path)): 
                                ?>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <div class="placeholder-image">
                                        <i class="fas fa-image"></i>
                                        <span>No Image Available</span>
                                    </div>
                                <?php endif; ?>
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
    const searchParams = new URLSearchParams(window.location.search);
    const searchQuery = searchParams.get('search');
    if (searchQuery) {
        window.location.href = 'products.php?search=' + encodeURIComponent(searchQuery);
    } else {
        window.location.href = 'products.php';
    }
}

function applyFilters() {
    const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked'))
        .map(checkbox => checkbox.value);
    
    const searchParams = new URLSearchParams(window.location.search);
    const searchQuery = searchParams.get('search');
    
    let url = 'products.php';
    const params = [];
    
    if (searchQuery) {
        params.push('search=' + encodeURIComponent(searchQuery));
    }
    
    if (selectedCategories.length > 0) {
        params.push('categories=' + selectedCategories.join(','));
    }
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    window.location.href = url;
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
include 'includes/footer.php';
$conn->close();
?> 