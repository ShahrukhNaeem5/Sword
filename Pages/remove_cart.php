<?php
session_start();
include '../Config/connection.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

try {
    if (isset($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);
        $variationId = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;

        if (isset($_SESSION['user_id'])) {
            // Logged-in user - remove from database
            $userId = intval($_SESSION['user_id']);
            $stmt = $conn->prepare("DELETE FROM cart WHERE product_id = ? AND variation_id = ? AND user_id = ?");
            $stmt->bind_param("iii", $productId, $variationId, $userId);
            
            if ($stmt->execute()) {
                $response['success'] = true;
            } else {
                $response['message'] = 'Database delete failed';
            }
            $stmt->close();
        } elseif (isset($_SESSION['cart'][$productId][$variationId])) {
            // Guest user - remove from session
            unset($_SESSION['cart'][$productId][$variationId]);
            if (empty($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
            }
            $response['success'] = true;
        }

        // Calculate summary and check if cart is empty
        if ($response['success']) {
            $response['summary'] = calculateCartSummary($conn);
            $response['cart_empty'] = ($response['summary']['total_items'] === 0);
        }
    } else {
        $response['message'] = 'Missing product ID';
    }
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}

function calculateCartSummary($conn) {
    $totalItems = 0;
    $subtotal = 0;

    if (isset($_SESSION['user_id'])) {
        // For logged-in users
        $userId = intval($_SESSION['user_id']);
        $query = "SELECT c.quantity, 
                 COALESCE(pv.sale_price, pv.price, p.sale_price, p.price) as effective_price
                 FROM cart c
                 JOIN products p ON c.product_id = p.product_id
                 LEFT JOIN product_variations pv ON pv.variation_id = c.variation_id
                 WHERE c.user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $totalItems += $row['quantity'];
            $subtotal += $row['effective_price'] * $row['quantity'];
        }
        $stmt->close();
    } elseif (isset($_SESSION['cart'])) {
        // For guest users
        foreach ($_SESSION['cart'] as $product_id => $variations) {
            foreach ($variations as $variation_id => $quantity) {
                $query = "SELECT COALESCE(pv.sale_price, pv.price, p.sale_price, p.price) as effective_price
                         FROM products p
                         LEFT JOIN product_variations pv ON pv.variation_id = ?
                         WHERE p.product_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ii", $variation_id, $product_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $totalItems += $quantity;
                    $subtotal += $row['effective_price'] * $quantity;
                }
                $stmt->close();
            }
        }
    }

    return [
        'total_items' => $totalItems,
        'subtotal' => $subtotal
    ];
}

echo json_encode($response);
?>