<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$identifier = $_POST['identifier'];
$password = $_POST['password'];

if (empty($identifier) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
    exit();
}

$conn = connectDB();

// Check if identifier is email or username
$stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $identifier, $identifier);
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
        
        echo json_encode(['success' => true]);
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid email/username or password']);
$conn->close();
?> 