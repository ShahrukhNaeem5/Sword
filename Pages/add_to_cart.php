<?php
session_start();
include '../Config/connection.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method', 405);
    }

    // Get and validate input
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input', 400);
    }

    $required = ['product_id', 'variation_id', 'quantity'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            throw new Exception("Missing required field: $field", 400);
        }
    }

    $product_id = intval($input['product_id']);
    $variation_id = intval($input['variation_id']);
    $quantity = intval($input['quantity']);

    if ($product_id <= 0 || $variation_id < 0 || $quantity <= 0) {
        throw new Exception('Invalid product ID, variation ID or quantity', 400);
    }

    // Initialize session cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Check stock availability
        $stock_query = "SELECT stock_quantity FROM product_variations 
                       WHERE variation_id = ? AND product_id = ? FOR UPDATE";
        $stmt = $conn->prepare($stock_query);
        $stmt->bind_param("ii", $variation_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stock = $result->fetch_assoc();
        $stmt->close();

        if (!$stock) {
            throw new Exception('Product variation not found', 404);
        }

        if ($stock['stock_quantity'] < $quantity) {
            throw new Exception('Insufficient stock', 400);
        }

        // Handle cart based on user login status
        if (isset($_SESSION['user_id'])) {
            // Logged-in user: Use database cart
            $user_id = intval($_SESSION['user_id']);

            // Check if item exists in cart
            $cart_query = "SELECT id, quantity FROM cart 
                          WHERE user_id = ? AND product_id = ? AND variation_id = ?";
            $stmt = $conn->prepare($cart_query);
            $stmt->bind_param("iii", $user_id, $product_id, $variation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $cart_item = $result->fetch_assoc();
            $stmt->close();

            if ($cart_item) {
                // Update existing cart item
                $new_quantity = $cart_item['quantity'] + $quantity;
                $update_query = "UPDATE cart SET quantity = ? WHERE id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("ii", $new_quantity, $cart_item['id']);
                $stmt->execute();
                $stmt->close();
            } else {
                // Insert new cart item
                $insert_query = "INSERT INTO cart (user_id, product_id, variation_id, quantity) 
                               VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("iiii", $user_id, $product_id, $variation_id, $quantity);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            // Non-logged-in user: Use session cart
            if (!isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] = [];
            }

            if (isset($_SESSION['cart'][$product_id][$variation_id])) {
                // Update quantity
                $_SESSION['cart'][$product_id][$variation_id] += $quantity;
            } else {
                // Add new item
                $_SESSION['cart'][$product_id][$variation_id] = $quantity;
            }
        }

        // Update stock
        $update_stock = "UPDATE product_variations 
                        SET stock_quantity = stock_quantity - ? 
                        WHERE variation_id = ? AND product_id = ?";
        $stmt = $conn->prepare($update_stock);
        $stmt->bind_param("iii", $quantity, $variation_id, $product_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        $response['success'] = true;
        $response['message'] = 'Item added to cart successfully';
        $response['cart_count'] = getCartCount($conn);
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    $response['message'] = $e->getMessage();
}

// Helper function to get cart count
function getCartCount($conn)
{
    $count = 0;
    if (isset($_SESSION['user_id'])) {
        // Logged-in user: Count from database
        $user_id = intval($_SESSION['user_id']);
        $query = "SELECT SUM(quantity) as count FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'] ?? 0;
        $stmt->close();
    } else {
        // Non-logged-in user: Count from session
        foreach ($_SESSION['cart'] as $product_id => $variations) {
            foreach ($variations as $quantity) {
                $count += $quantity;
            }
        }
    }
    return $count;
}

echo json_encode($response);
exit;
?>