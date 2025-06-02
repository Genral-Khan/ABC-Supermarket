<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];
$conn = connectDB();

$stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        
        // Merge session cart with user's cart if exists
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                // First check if the product exists
                $check_stmt = $conn->prepare("SELECT product_id FROM products WHERE product_id = ?");
                $check_stmt->bind_param("i", $item['product_id']);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows > 0) {
                    // Check if item already exists in cart
                    $cart_stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
                    $cart_stmt->bind_param("ii", $user['user_id'], $item['product_id']);
                    $cart_stmt->execute();
                    $cart_result = $cart_stmt->get_result();
                    
                    if ($cart_result->num_rows > 0) {
                        // Update existing cart item
                        $cart_item = $cart_result->fetch_assoc();
                        $new_quantity = min($cart_item['quantity'] + $item['quantity'], 99);
                        
                        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                        $update_stmt->bind_param("iii", $new_quantity, $user['user_id'], $item['product_id']);
                        $update_stmt->execute();
                    } else {
                        // Insert new cart item
                        $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                        $insert_stmt->bind_param("iii", $user['user_id'], $item['product_id'], $item['quantity']);
                        $insert_stmt->execute();
                    }
                }
            }
        }
        
        // Clear session cart after merging
        unset($_SESSION['cart']);
        
        // Check if we came from cart page
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if (strpos($referer, 'cart.php') !== false) {
            header("Location: checkout.php");
            exit();
        }
        
        // Check for stored redirect URL
        if (isset($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
            exit();
        }
        
        // Otherwise use the redirect parameter or default to index
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
        header("Location: $redirect");
        exit();
    }
}

$_SESSION['login_error'] = "Invalid email or password";
header("Location: login.php" . (isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''));
$conn->close();
?> 