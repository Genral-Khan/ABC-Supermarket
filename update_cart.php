<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['index'])) {
    $index = intval($_POST['index']);
    
    // Remove item
    if (isset($_POST['remove'])) {
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
            echo json_encode(['success' => true]);
            exit;
        }
    }
    
    // Update quantity
    if (isset($_POST['quantity'])) {
        $quantity = intval($_POST['quantity']);
        if ($quantity < 1) $quantity = 1;
        if ($quantity > 99) $quantity = 99;
        
        if (isset($_SESSION['cart'][$index])) {
            $_SESSION['cart'][$index]['quantity'] = $quantity;
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?> 