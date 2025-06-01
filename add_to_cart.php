<?php
require_once 'config/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$product_id = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

$conn = connectDB();

// Check if product exists and is in stock
$stmt = $conn->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    $stmt->close();
    $conn->close();
    exit();
}

$product = $result->fetch_assoc();
if ($product['stock_quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
    $stmt->close();
    $conn->close();
    exit();
}

// Check if product is already in cart
$stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update quantity
    $cart_item = $result->fetch_assoc();
    $new_quantity = $cart_item['quantity'] + $quantity;
    
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("iii", $new_quantity, $_SESSION['user_id'], $product_id);
} else {
    // Add new cart item
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $_SESSION['user_id'], $product_id, $quantity);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding product to cart']);
}

$stmt->close();
$conn->close(); 