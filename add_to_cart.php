<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit;
}

$product_id = intval($_POST['product_id']);
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if ($quantity < 1) $quantity = 1;
if ($quantity > 99) $quantity = 99;

$conn = connectDB();

// Verify product exists and get its details
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    $conn->close();
    exit;
}

$product = $result->fetch_assoc();

// If user is logged in, add to database cart
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)
                          ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
    $stmt->bind_param("iii", $_SESSION['user_id'], $product_id, $quantity);
    $stmt->execute();
} else {
    // Initialize cart in session if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add to session cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] === $product_id) {
            $item['quantity'] += $quantity;
            if ($item['quantity'] > 99) $item['quantity'] = 99;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = [
            'product_id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image_url' => $product['image_url']
        ];
    }
}

$conn->close();
echo json_encode(['success' => true, 'message' => 'Product added to cart']);
?> 