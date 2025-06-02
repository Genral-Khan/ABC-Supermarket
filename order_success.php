<?php
session_start();

if (!isset($_SESSION['order_success'])) {
    header("Location: index.php");
    exit();
}

unset($_SESSION['order_success']);
include 'includes/header.php';
?>

<div class="container fade-in">
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Order Placed Successfully!</h1>
        <p>Thank you for shopping with us. Your order has been received and is being processed.</p>
        <div class="success-actions">
            <a href="products.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i>
                Continue Shopping
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 