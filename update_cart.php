<?php
session_start();
require_once 'config/database.php';
header('Content-Type: application/json');

// Handle database cart if user is logged in
if (isset($_SESSION['user_id'])) {
    $conn = connectDB();
    
    if (isset($_POST['index'])) {
        $index = intval($_POST['index']);
        
        // Get the product_id for the item at this index
        $stmt = $conn->prepare("
            SELECT cart_id, product_id 
            FROM cart 
            WHERE user_id = ? 
            LIMIT 1 OFFSET ?
        ");
        $stmt->bind_param("ii", $_SESSION['user_id'], $index);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $cart_item = $result->fetch_assoc();
            
            // Remove item
            if (isset($_POST['remove'])) {
                $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
                $stmt->bind_param("i", $cart_item['cart_id']);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                    exit;
                }
            }
            
            // Update quantity
            if (isset($_POST['quantity'])) {
                $quantity = intval($_POST['quantity']);
                if ($quantity < 1) $quantity = 1;
                if ($quantity > 99) $quantity = 99;
                
                $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
                $stmt->bind_param("ii", $quantity, $cart_item['cart_id']);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                    exit;
                }
            }
        }
    }
    $conn->close();
} else {
    // Handle session cart for non-logged in users
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
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?> 