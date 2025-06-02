<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABC Supermarket</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <a href="index.php" class="navbar-brand">
                    <i class="fas fa-shopping-cart" style="margin-right: 0.5rem;"></i>
                    ABC SUPERMARKET
                </a>
                
                <div class="search-bar" style="width: 40%; margin: 0 2rem;">
                    <i class="fas fa-search" style="color: rgba(255,255,255,0.6); margin-right: 0.5rem;"></i>
                    <input type="text" id="searchInput" placeholder="Search products...">
                </div>
                
                <div class="nav-links">
                    <a href="index.php"><i class="fas fa-home"></i> Home</a>
                    <a href="products.php"><i class="fas fa-box"></i> Products</a>
                    <a href="cart.php"><i class="fas fa-shopping-basket"></i> Cart</a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <div style="margin-top: 80px;"><!-- Content spacing from fixed navbar -->
</body>
</html> 