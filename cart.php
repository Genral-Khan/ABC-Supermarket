<?php
session_start();
require_once 'config/database.php';

$conn = connectDB();
$total = 0;
$cart_items = [];

// Get items from database if user is logged in
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("
        SELECT c.cart_id, c.quantity, p.* 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?
        ORDER BY c.cart_id
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($item = $result->fetch_assoc()) {
        $cart_items[] = [
            'product_id' => $item['product_id'],
            'name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'image_url' => $item['image_url']
        ];
    }
} else {
    // Initialize session cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $cart_items = $_SESSION['cart'];
}

include 'includes/header.php';
?>

<div class="container fade-in" style="padding: 2rem 0;">
    <h1 style="margin-bottom: 2rem; color: var(--text-color)">Shopping Cart</h1>

    <?php if (empty($cart_items)): ?>
        <div class="card" style="text-align: center; padding: 3rem;">
            <h2>Your cart is empty</h2>
            <p style="margin: 1rem 0;">Start shopping to add items to your cart</p>
            <a href="products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Cart Items -->
            <div>
                <?php foreach ($cart_items as $index => $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <div class="card" style="margin-bottom: 1rem; display: flex; gap: 1rem; padding: 1rem;">
                        <div style="width: 100px; height: 100px; border-radius: 5px; overflow: hidden;">
                            <?php if (!empty($item['image_url']) && file_exists("uploads/" . $item['image_url'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($item['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div class="placeholder-image" style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; background: rgba(255, 255, 255, 0.05);">
                                    <i class="fas fa-image" style="font-size: 2rem; opacity: 0.7;"></i>
                                    <span style="font-size: 0.8rem; margin-top: 0.5rem; opacity: 0.7;">No Image</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div style="flex-grow: 1;">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p>Price: AED <?php echo number_format($item['price'], 2); ?></p>
                            
                            <div style="display: flex; align-items: center; gap: 1rem; margin-top: 1rem;">
                                <div class="quantity-selector">
                                    <button class="qty-btn" onclick="updateCartQuantity(<?php echo $index; ?>, 'decrease')">-</button>
                                    <input type="number" 
                                           id="qty-<?php echo $index; ?>" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" 
                                           max="99"
                                           onchange="validateAndUpdateCart(this, <?php echo $index; ?>)">
                                    <button class="qty-btn" onclick="updateCartQuantity(<?php echo $index; ?>, 'increase')">+</button>
                                </div>
                                
                                <button onclick="removeFromCart(<?php echo $index; ?>)"
                                        class="btn btn-primary" style="background-color: #dc3545;">Remove</button>
                            </div>
                        </div>
                        
                        <div style="text-align: right;">
                            <p style="font-weight: bold;">Subtotal:</p>
                            <p>AED <?php echo number_format($subtotal, 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="card" style="position: sticky; top: 100px;">
                    <h2 style="margin-bottom: 1rem;">Order Summary</h2>
                    
                    <div style="margin: 1rem 0; padding: 1rem 0; border-top: 1px solid rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Subtotal:</span>
                            <span>AED <?php echo number_format($total, 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Shipping:</span>
                            <span><?php echo $total >= 50 ? 'Free' : 'AED 5.00'; ?></span>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="font-weight: bold;">Total:</span>
                        <span style="font-weight: bold;">AED <?php echo number_format($total >= 50 ? $total : $total + 5, 2); ?></span>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="checkout.php" class="btn btn-primary" style="width: 100%; text-align: center; display: block; text-decoration: none;">
                            <i class="fas fa-lock"></i>
                            Proceed to Checkout
                        </a>
                    <?php else: ?>
                        <a href="login.php?redirect=checkout.php" class="btn btn-primary" style="width: 100%; text-align: center; display: block; text-decoration: none;">
                            <i class="fas fa-lock"></i>
                            Login to Checkout
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($total < 50): ?>
                        <p style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.8;">
                            <i class="fas fa-truck"></i>
                            Add AED <?php echo number_format(50 - $total, 2); ?> more to get free shipping!
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function updateCartQuantity(index, action) {
    const input = document.getElementById(`qty-${index}`);
    let value = parseInt(input.value);
    
    if (action === 'increase') {
        value = Math.min(value + 1, 99);
    } else {
        value = Math.max(value - 1, 1);
    }
    
    input.value = value;
    updateCart(index, value);
}

function validateAndUpdateCart(input, index) {
    let value = parseInt(input.value);
    if (isNaN(value) || value < 1) value = 1;
    if (value > 99) value = 99;
    input.value = value;
    updateCart(index, value);
}

function updateCart(index, quantity) {
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `index=${index}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating cart');
        }
    });
}

function removeFromCart(index) {
    if (confirm('Are you sure you want to remove this item?')) {
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `index=${index}&remove=1`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error removing item');
            }
        });
    }
}
</script>

<?php 
include 'includes/footer.php';
$conn->close();
?> 