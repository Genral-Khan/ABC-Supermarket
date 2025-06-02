<?php
require_once 'config/database.php';
session_start();

// Store the redirect URL if provided
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = $_GET['redirect'];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $conn = connectDB();
        
        $stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect to previous page or home
                $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect");
                exit();
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container" style="max-width: 400px; margin: 2rem auto;">
    <div class="card fade-in">
        <h2 style="text-align: center; margin-bottom: 2rem;">Login</h2>
        
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['login_error']; ?></div>
            <?php unset($_SESSION['login_error']); ?>
        <?php elseif ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login_process.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 1rem;">
            Don't have an account? <a href="register.php" style="color: #ff0000;">Register here</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 