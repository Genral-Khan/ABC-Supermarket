<?php
session_start();
require_once 'config/database.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit();
}

$conn = connectDB();

// Fetch cart items for order summary
$stmt = $conn->prepare("
    SELECT c.quantity, p.name, p.price 
    FROM cart c 
    JOIN products p ON c.product_id = p.product_id 
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$cart_items = $stmt->get_result();

if ($cart_items->num_rows === 0) {
    header("Location: cart.php");
    exit();
}

$total = 0;
$items = [];
while ($item = $cart_items->fetch_assoc()) {
    $subtotal = $item['price'] * $item['quantity'];
    $total += $subtotal;
    $items[] = $item;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $final_total = $total >= 50 ? $total : $total + 5;
    $stmt->bind_param("id", $_SESSION['user_id'], $final_total);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        
        // Move cart items to order_items
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price_at_time)
            SELECT ?, c.product_id, c.quantity, p.price
            FROM cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            // Clear cart
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            
            // Show success message
            $_SESSION['order_success'] = true;
            header("Location: order_success.php");
            exit();
        }
    }
}
?>

<div class="container fade-in" style="padding: 2rem 0;">
    <h1 style="margin-bottom: 2rem; color: var(--text-color)">Checkout</h1>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Checkout Form -->
        <div class="card">
            <form method="POST" action="" id="checkoutForm">
                <h2 style="margin-bottom: 1.5rem;">Shipping Information</h2>
                
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" class="form-control" required>
                    </div>
                </div>
                
                <h2 style="margin: 2rem 0 1.5rem;">Payment Information</h2>
                
                <div class="form-group">
                    <label for="card_number">Card Number</label>
                    <input type="text" id="card_number" name="card_number" class="form-control" required 
                           pattern="\d{16}" title="Please enter a valid 16-digit card number">
                </div>
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="expiry">Expiry Date (MM/YY)</label>
                        <input type="text" id="expiry" name="expiry" class="form-control" required 
                               pattern="\d{2}/\d{2}" title="Please enter date in MM/YY format">
                    </div>
                    
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" class="form-control" required 
                               pattern="\d{3}" title="Please enter a valid 3-digit CVV">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    Place Order
                </button>
            </form>
        </div>
        
        <!-- Order Summary -->
        <div>
            <div class="card" style="position: sticky; top: 100px;">
                <h2 style="margin-bottom: 1rem;">Order Summary</h2>
                
                <?php foreach ($items as $item): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span><?php echo htmlspecialchars($item['name']); ?> (×<?php echo $item['quantity']; ?>)</span>
                        <span>AED <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div style="margin: 1rem 0; padding: 1rem 0; border-top: 1px solid #eee; border-bottom: 1px solid #eee;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Subtotal:</span>
                        <span>AED <?php echo number_format($total, 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Shipping:</span>
                        <span>AED <?php echo $total >= 50 ? '0.00' : '5.00'; ?></span>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span style="font-weight: bold;">Total:</span>
                    <span style="font-weight: bold;">AED <?php echo number_format($total >= 50 ? $total : $total + 5, 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // In a real application, you would validate and process the payment here
    // For this demo, we'll just submit the form
    this.submit();
});

// Format card number input
document.getElementById('card_number').addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '').substring(0, 16);
});

// Format expiry date input
document.getElementById('expiry').addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    this.value = value;
});

// Format CVV input
document.getElementById('cvv').addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '').substring(0, 3);
});
</script>

<?php
$stmt->close();
include 'includes/footer.php';
?> 