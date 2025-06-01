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

$cart_id = $_POST['cart_id'] ?? null;
$action = $_POST['action'] ?? 'update';
$quantity = $_POST['quantity'] ?? null;

if (!$cart_id) {
    echo json_encode(['success' => false, 'message' => 'Cart ID is required']);
    exit();
}

$conn = connectDB();

// Verify cart item belongs to user
$stmt = $conn->prepare("SELECT c.product_id, p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.cart_id = ? AND c.user_id = ?");
$stmt->bind_param("ii", $cart_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    $stmt->close();
    $conn->close();
    exit();
}

$cart_item = $result->fetch_assoc();

if ($action === 'remove') {
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $_SESSION['user_id']);
} else {
    if (!$quantity || $quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
        $stmt->close();
        $conn->close();
        exit();
    }
    
    // Check if quantity is available in stock
    if ($quantity > $cart_item['stock_quantity']) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        $stmt->close();
        $conn->close();
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("iii", $quantity, $cart_id, $_SESSION['user_id']);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => $action === 'remove' ? 'Item removed from cart' : 'Cart updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating cart']);
}

$stmt->close();
$conn->close(); 