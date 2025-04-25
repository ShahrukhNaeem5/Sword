<?php
session_start();
include '../Config/connection.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

// Suppress display errors and log to file
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

try {
    // Clear output buffer
    ob_start();

    // Validate POST parameters
    if (!isset($_POST['product_id'], $_POST['quantity'])) {
        $response['message'] = 'Missing required parameters';
        echo json_encode($response);
        ob_end_flush();
        exit;
    }

    $productId = intval($_POST['product_id']); // e.g., 97
    $variationId = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0; // e.g., 7
    $quantity = intval($_POST['quantity']); // e.g., 11

    if ($quantity < 1) {
        $response['message'] = 'Quantity cannot be less than 1';
        echo json_encode($response);
        ob_end_flush();
        exit;
    }

    // Check stock availability
    $stockQuery = "SELECT stock_quantity, manage_stock FROM products WHERE product_id = ?";
    if ($variationId > 0) {
        $stockQuery = "SELECT stock_quantity FROM product_variations WHERE product_id = ? AND variation_id = ?";
    }
    $stockStmt = $conn->prepare($stockQuery);
    if (!$stockStmt) {
        error_log("Stock query preparation failed: " . $conn->error);
        $response['message'] = 'Stock check failed';
        echo json_encode($response);
        ob_end_flush();
        exit;
    }
    if ($variationId > 0) {
        $stockStmt->bind_param("ii", $productId, $variationId);
    } else {
        $stockStmt->bind_param("i", $productId);
    }
    $stockStmt->execute();
    $stockResult = $stockStmt->get_result()->fetch_assoc();
    $stockStmt->close();

    if ($variationId == 0 && $stockResult && isset($stockResult['manage_stock']) && !$stockResult['manage_stock']) {
        // Skip stock check if manage_stock is false
    } elseif (!$stockResult || !isset($stockResult['stock_quantity']) || $quantity > $stockResult['stock_quantity']) {
        $response['message'] = 'Requested quantity exceeds available stock';
        echo json_encode($response);
        ob_end_flush();
        exit;
    }

    // Guest user - update session
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = [];
    }
    $_SESSION['cart'][$productId][$variationId] = $quantity;
    $response['success'] = true;

    // Calculate summary
    if ($response['success']) {
        $response['summary'] = calculateCartSummary();
    }

    echo json_encode($response);
    ob_end_flush();
    exit;
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
    echo json_encode($response);
    ob_end_flush();
    exit;
}

function calculateCartSummary() {
    global $conn;
    $summary = ['total_items' => 0, 'subtotal' => 0];

    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $productId => $variations) {
            foreach ($variations as $variationId => $quantity) {
                $query = "SELECT COALESCE(pv.sale_price, pv.price, p.sale_price, p.price) as effective_price 
                          FROM products p 
                          LEFT JOIN product_variations pv ON p.product_id = pv.product_id AND pv.variation_id = ? 
                          WHERE p.product_id = ?";
                $stmt = $conn->prepare($query);
                if (!$stmt) {
                    error_log("Prepare failed: " . $conn->error);
                    return $summary;
                }
                $stmt->bind_param("ii", $variationId, $productId);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($result && isset($result['effective_price'])) {
                    $summary['total_items'] += $quantity;
                    $summary['subtotal'] += $result['effective_price'] * $quantity;
                } else {
                    error_log("No price found for product_id: $productId, variation_id: $variationId");
                    $response['message'] = 'Product or variation not found';
                }
            }
        }
    }

    return $summary;
}
?>