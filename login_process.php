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

$stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        
        // Merge session cart with user's cart if exists
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)
                                      ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
                $stmt->bind_param("iii", $user['user_id'], $item['product_id'], $item['quantity']);
                $stmt->execute();
            }
            // Clear session cart after merging
            unset($_SESSION['cart']);
        }
        
        // Redirect to requested page or default to index
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
        header("Location: " . $redirect);
        exit();
    }
}

$_SESSION['login_error'] = "Invalid email or password";
header("Location: login.php");
$conn->close();
?> 