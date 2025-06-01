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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <a href="index.php" class="navbar-brand">ABC SUPERMARKET</a>
                
                <div class="search-bar" style="width: 40%; margin: 0 2rem;">
                    <input type="text" id="searchInput" placeholder="Search products...">
                </div>
                
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="products.php">Products</a>
                    <a href="cart.php">Cart</a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="profile.php">Profile</a>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                        <a href="register.php">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <div style="margin-top: 80px;"><!-- Content spacing from fixed navbar --> 