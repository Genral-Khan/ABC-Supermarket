<?php
session_start();

if (!isset($_SESSION['order_success'])) {
    header("Location: index.php");
    exit();
}

unset($_SESSION['order_success']);
include 'includes/header.php';
?>

<div class="container fade-in" style="padding: 4rem 0; text-align: center;">
    <div class="card" style="max-width: 600px; margin: 0 auto; padding: 3rem;">
        <i class="fas fa-check-circle" style="font-size: 4rem; color: #28a745; margin-bottom: 1rem;"></i>
        
        <h1 style="margin-bottom: 1rem;">Order Placed Successfully!</h1>
        <p style="margin-bottom: 2rem;">Thank you for shopping with ABC Supermarket. Your order has been received and is being processed.</p>
        
        <div style="margin: 2rem 0;">
            <p>A confirmation email will be sent to your registered email address.</p>
            <p>You can track your order in your profile section.</p>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            <a href="profile.php" class="btn btn-primary" style="background-color: var(--accent-color);">View Orders</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 