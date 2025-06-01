<?php
require_once 'config/database.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'cart.php';
    header("Location: login.php");
    exit();
}

$conn = connectDB();

// Fetch cart items with product details
$stmt = $conn->prepare("
    SELECT c.cart_id, c.quantity, p.product_id, p.name, p.price, p.image_url 
    FROM cart c 
    JOIN products p ON c.product_id = p.product_id 
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$cart_items = $stmt->get_result();

$total = 0;
?>

<div class="container fade-in" style="padding: 2rem 0;">
    <h1 style="margin-bottom: 2rem;">Shopping Cart</h1>
    
    <?php if ($cart_items->num_rows === 0): ?>
        <div class="card" style="text-align: center; padding: 3rem;">
            <h2>Your cart is empty</h2>
            <p style="margin: 1rem 0;">Start shopping to add items to your cart</p>
            <a href="products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Cart Items -->
            <div>
                <?php while ($item = $cart_items->fetch_assoc()):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <div class="card" style="margin-bottom: 1rem; display: flex; gap: 1rem; padding: 1rem;">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                             style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                        
                        <div style="flex-grow: 1;">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                            
                            <div style="display: flex; align-items: center; gap: 1rem; margin-top: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <button onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] - 1; ?>)"
                                            class="btn btn-primary" style="padding: 0.5rem 1rem;">-</button>
                                    <span style="min-width: 40px; text-align: center;"><?php echo $item['quantity']; ?></span>
                                    <button onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] + 1; ?>)"
                                            class="btn btn-primary" style="padding: 0.5rem 1rem;">+</button>
                                </div>
                                
                                <button onclick="removeFromCart(<?php echo $item['cart_id']; ?>)"
                                        class="btn btn-primary" style="background-color: #dc3545;">Remove</button>
                            </div>
                        </div>
                        
                        <div style="text-align: right;">
                            <p style="font-weight: bold;">Subtotal:</p>
                            <p>$<?php echo number_format($subtotal, 2); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Order Summary -->
            <div>
                <div class="card" style="position: sticky; top: 100px;">
                    <h2 style="margin-bottom: 1rem;">Order Summary</h2>
                    
                    <div style="margin: 1rem 0; padding: 1rem 0; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Shipping:</span>
                            <span>$<?php echo $total >= 50 ? '0.00' : '5.00'; ?></span>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="font-weight: bold;">Total:</span>
                        <span style="font-weight: bold;">$<?php echo number_format($total >= 50 ? $total : $total + 5, 2); ?></span>
                    </div>
                    
                    <button onclick="window.location.href='checkout.php'" class="btn btn-primary" style="width: 100%;">
                        Proceed to Checkout
                    </button>
                    
                    <?php if ($total < 50): ?>
                        <p style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
                            Add $<?php echo number_format(50 - $total, 2); ?> more to get free shipping!
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQuantity(cartId, newQuantity) {
    if (newQuantity < 1) return;
    
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `cart_id=${cartId}&quantity=${newQuantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Error updating cart');
        }
    });
}

function removeFromCart(cartId) {
    if (!confirm('Are you sure you want to remove this item?')) return;
    
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `cart_id=${cartId}&action=remove`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Error removing item');
        }
    });
}
</script>

<?php
$stmt->close();
$conn->close();
include 'includes/footer.php';
?> 