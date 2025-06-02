<?php
session_start();
require_once 'config/database.php';

$conn = connectDB();
$total = 0;

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

include 'includes/header.php';
?>

<div class="container fade-in" style="padding: 2rem 0;">
    <h1 style="margin-bottom: 2rem;">Shopping Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="card" style="text-align: center; padding: 3rem;">
            <h2>Your cart is empty</h2>
            <p style="margin: 1rem 0;">Start shopping to add items to your cart</p>
            <a href="products.php" class="btn btn-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Cart Items -->
            <div>
                <?php foreach ($_SESSION['cart'] as $index => $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <div class="card" style="margin-bottom: 1rem; display: flex; gap: 1rem; padding: 1rem;">
                        <?php if (!empty($item['image_url'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($item['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                        <?php else: ?>
                            <div class="placeholder-image" style="width: 100px; height: 100px; border-radius: 5px;">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div style="flex-grow: 1;">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                            
                            <div style="display: flex; align-items: center; gap: 1rem; margin-top: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <button onclick="updateCartQuantity(<?php echo $index; ?>, 'decrease')"
                                            class="btn btn-primary" style="padding: 0.5rem 1rem;">-</button>
                                    <input type="number" 
                                           id="qty-<?php echo $index; ?>" 
                                           value="<?php echo $item['quantity']; ?>"
                                           min="1"
                                           max="99"
                                           style="width: 50px; text-align: center; padding: 0.5rem;"
                                           onchange="validateAndUpdateCart(this, <?php echo $index; ?>)">
                                    <button onclick="updateCartQuantity(<?php echo $index; ?>, 'increase')"
                                            class="btn btn-primary" style="padding: 0.5rem 1rem;">+</button>
                                </div>
                                
                                <button onclick="removeFromCart(<?php echo $index; ?>)"
                                        class="btn btn-primary" style="background-color: #dc3545;">Remove</button>
                            </div>
                        </div>
                        
                        <div style="text-align: right;">
                            <p style="font-weight: bold;">Subtotal:</p>
                            <p>$<?php echo number_format($subtotal, 2); ?></p>
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
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Shipping:</span>
                            <span><?php echo $total >= 50 ? 'Free' : '$5.00'; ?></span>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="font-weight: bold;">Total:</span>
                        <span style="font-weight: bold;">$<?php echo number_format($total >= 50 ? $total : $total + 5, 2); ?></span>
                    </div>
                    
                    <a href="<?php echo isset($_SESSION['user_id']) ? 'checkout.php' : 'login.php?redirect=checkout.php'; ?>" 
                       class="btn btn-primary" style="width: 100%; text-align: center;">
                        <i class="fas fa-lock"></i>
                        <?php echo isset($_SESSION['user_id']) ? 'Proceed to Checkout' : 'Login to Checkout'; ?>
                    </a>
                    
                    <?php if ($total < 50): ?>
                        <p style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.8;">
                            <i class="fas fa-truck"></i>
                            Add $<?php echo number_format(50 - $total, 2); ?> more to get free shipping!
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