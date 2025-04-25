<?php
session_start();
include '../Config/connection.php';

header('Content-Type: application/json');

$response = ['success' => false, 'cart_count' => 0];

try {
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
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_id => $variations) {
                foreach ($variations as $quantity) {
                    $count += $quantity;
                }
            }
        }
    }

    $response['success'] = true;
    $response['cart_count'] = $count;
} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>