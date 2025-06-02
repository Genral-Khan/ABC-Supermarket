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
                        <button onclick="openModal()" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content card">
            <div class="modal-header">
                <h2 class="gradient-text">Welcome Back</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            
            <form id="loginForm" method="POST" action="login.php" class="modal-body">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
                
                <p style="text-align: center; margin-top: 1rem; color: var(--light-color);">
                    Don't have an account? <a href="register.php" style="color: var(--primary-color);">Register here</a>
                </p>
            </form>
        </div>
    </div>
    
    <div style="margin-top: 120px;"><!-- Content spacing from fixed navbar --> 

    <script>
    // Modal functionality
    const modal = document.getElementById('loginModal');
    
    function openModal() {
        modal.style.display = "flex";
        setTimeout(() => {
            modal.style.opacity = "1";
            modal.querySelector('.modal-content').style.transform = "translateY(0)";
        }, 10);
    }
    
    function closeModal() {
        modal.style.opacity = "0";
        modal.querySelector('.modal-content').style.transform = "translateY(-20px)";
        setTimeout(() => {
            modal.style.display = "none";
        }, 300);
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }
    </script>
</body>
</html> 