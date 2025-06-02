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
            <div class="nav-content">
                <!-- Logo -->
                <a href="index.php" class="navbar-brand">
                    <i class="fas fa-shopping-cart"></i>
                    <span>ABC SUPERMARKET</span>
                </a>

                <!-- Search Bar -->
                <div class="search-container">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search products...">
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="nav-links">
                    <a href="index.php" class="nav-item <?php echo ($_SERVER['PHP_SELF'] == '/index.php') ? 'active' : ''; ?>">
                        Home
                    </a>
                    <a href="products.php" class="nav-item <?php echo ($_SERVER['PHP_SELF'] == '/products.php') ? 'active' : ''; ?>">
                        Products
                    </a>
                    <a href="cart.php" class="nav-item <?php echo ($_SERVER['PHP_SELF'] == '/cart.php') ? 'active' : ''; ?>">
                        Cart
                    </a>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="user-menu">
                            <button class="user-trigger">
                                <i class="fas fa-user-circle"></i>
                                <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="user-dropdown">
                                <a href="profile.php">
                                    <i class="fas fa-user"></i> Profile
                                </a>
                                <a href="orders.php">
                                    <i class="fas fa-box"></i> Orders
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="logout.php" class="logout-link">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="auth-button">
                            <span>Sign In</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content"><!-- Main content wrapper -->
</body>
</html> 