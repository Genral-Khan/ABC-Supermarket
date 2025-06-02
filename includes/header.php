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
    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="card modal-content">
            <div class="modal-header">
                <h2>Login</h2>
                <span class="close" onclick="closeModal('loginModal')">&times;</span>
            </div>
            
            <div class="modal-body">
                <div id="loginError" class="alert alert-error" style="display: none;"></div>
                
                <form id="loginForm" onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <label for="login_identifier">Email or Username</label>
                        <input type="text" id="login_identifier" name="identifier" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_password">Password</label>
                        <input type="password" id="login_password" name="password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
                </form>
                
                <p style="text-align: center; margin-top: 1rem;">
                    Don't have an account? 
                    <a href="#" onclick="openModal('registerModal'); closeModal('loginModal')" style="color: #ff0000;">
                        Register here
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="card modal-content">
            <div class="modal-header">
                <h2>Register</h2>
                <span class="close" onclick="closeModal('registerModal')">&times;</span>
            </div>
            
            <div class="modal-body">
                <div id="registerError" class="alert alert-error" style="display: none;"></div>
                
                <form id="registerForm" onsubmit="handleRegister(event)">
                    <div class="form-group">
                        <label for="register_username">Username</label>
                        <input type="text" id="register_username" name="username" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register_email">Email</label>
                        <input type="email" id="register_email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register_password">Password</label>
                        <input type="password" id="register_password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register_confirm_password">Confirm Password</label>
                        <input type="password" id="register_confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
                </form>
                
                <p style="text-align: center; margin-top: 1rem;">
                    Already have an account? 
                    <a href="#" onclick="openModal('loginModal'); closeModal('registerModal')" style="color: #ff0000;">
                        Login here
                    </a>
                </p>
            </div>
        </div>
    </div>

    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Logo -->
                <a href="index.php" class="navbar-brand">
                    <i class="fas fa-shopping-cart"></i>
                    <span>ABC SUPERMARKET</span>
                </a>

                <!-- Search Bar -->
                <div class="search-container">
                    <form action="products.php" method="GET" class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput" 
                               placeholder="Search products..."
                               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </form>
                </div>

                <!-- Navigation Links -->
                <div class="nav-links" id="mobileMenu">
                    <a href="products.php" class="nav-item">
                        <i class="fas fa-store"></i>
                        Products
                    </a>
                    <a href="cart.php" class="nav-item">
                        <i class="fas fa-shopping-cart"></i>
                        Cart
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-menu">
                            <button class="user-trigger">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="user-dropdown">
                                <!-- <a href="profile.php">
                                    <i class="fas fa-user-circle"></i>
                                    My Profile
                                </a>
                                <a href="orders.php">
                                    <i class="fas fa-shopping-bag"></i>
                                    My Orders
                                </a> -->
                                <!-- <div class="dropdown-divider"></div> -->
                                <a href="logout.php" class="logout-link">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="#" onclick="openModal('loginModal')" class="auth-button">
                            <span>Sign In</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content"><!-- Main content wrapper -->

    <script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
        setTimeout(() => {
            document.getElementById(modalId).style.opacity = '1';
            document.getElementById(modalId).querySelector('.modal-content').style.transform = 'translateY(0)';
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.opacity = '0';
        modal.querySelector('.modal-content').style.transform = 'translateY(-20px)';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    async function handleLogin(event) {
        event.preventDefault();
        const form = event.target;
        const errorDiv = document.getElementById('loginError');
        
        try {
            const response = await fetch('login_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(new FormData(form))
            });
            
            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                errorDiv.textContent = data.message;
                errorDiv.style.display = 'block';
            }
        } catch (error) {
            errorDiv.textContent = 'An error occurred. Please try again.';
            errorDiv.style.display = 'block';
        }
    }

    async function handleRegister(event) {
        event.preventDefault();
        const form = event.target;
        const errorDiv = document.getElementById('registerError');
        
        // Validate passwords match
        const password = form.password.value;
        const confirmPassword = form.confirm_password.value;
        
        if (password !== confirmPassword) {
            errorDiv.textContent = 'Passwords do not match';
            errorDiv.style.display = 'block';
            return;
        }
        
        try {
            const response = await fetch('register_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(new FormData(form))
            });
            
            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                errorDiv.textContent = data.message;
                errorDiv.style.display = 'block';
            }
        } catch (error) {
            errorDiv.textContent = 'An error occurred. Please try again.';
            errorDiv.style.display = 'block';
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            closeModal(event.target.id);
        }
    }

    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.classList.toggle('active');
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        
        if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
            mobileMenu.classList.remove('active');
        }
    });
    </script>
</body>
</html> 