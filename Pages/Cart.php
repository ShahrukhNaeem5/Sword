<?php
session_start();
include '../Config/connection.php';

// Function to get color codes for color attributes
function getColorCodes($colorName)
{
    $colors = [
        'red' => '#ff0000',
        'green' => '#00ff00',
        'blue' => '#0000ff',
        'yellow' => '#ffff00',
        'black' => '#000000',
        'white' => '#ffffff',
        'gray' => '#808080',
        'grey' => '#808080',
        'purple' => '#800080',
        'orange' => '#ffa500',
        'pink' => '#ffc0cb',
        'brown' => '#a52a2a',
        'cyan' => '#00ffff',
        'magenta' => '#ff00ff',
        'silver' => '#c0c0c0',
        'gold' => '#ffd700',
        'maroon' => '#800000',
        'olive' => '#808000',
        'lime' => '#00ff00',
        'teal' => '#008080',
        'navy' => '#000080',
        'violet' => '#ee82ee',
        'indigo' => '#4b0082',
        'turquoise' => '#40e0d0',
        'lavender' => '#e6e6fa',
        'coral' => '#ff7f50',
        'salmon' => '#fa8072',
        'beige' => '#f5f5dc',
        'ivory' => '#fffff0',
        'khaki' => '#f0e68c'
    ];
    $lowerColor = strtolower(trim($colorName));
    return $colors[$lowerColor] ?? '#cccccc';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View and manage your shopping cart.">
    <meta name="keywords" content="cart, shopping, e-commerce">
    <meta name="author" content="Your Company Name">
    <title>Cart - Your Company Name</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/Css/style.css">
    <style>
        .cart-container .breadcrumb-item a {
            color: #6b3a0f;
        }

        .cart-container {
            padding: 2rem 0;
        }

        .cart-container h2 {
            color: #6b3a0f;
        }

        .cart-container .cart-item {
            display: flex;
            align-items: stretch;
            padding: 1rem;
            border: 1px solid #e0e0e0;
            margin-bottom: 1rem;
            background: #EAE5DF;
            transition: box-shadow 0.3s;
            position: relative;
            min-height: 150px;
        }

        .cart-container .cart-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-container .img-box {
            width: 140px;
            flex-shrink: 0;
            height: 100%;
            align-self: stretch;
        }

        .cart-container .img-box img {
            width: 100%;
            height: 150px;
        }

        .cart-container .text-box {
            flex-grow: 1;
            padding-left: 1rem;
        }

        .cart-container .text-box p {
            font-size: 15px;
        }

        .cart-container .text-box h4,
        .cart-container .text-box h5,
        .cart-container .text-box p {
            color: #603B29 !important;
        }

        .cart-container .text-box h4,
        .cart-container .text-box h5 {
            font-weight: 700;
        }

        .cart-container .quantity-control-cart {
            display: flex;
            align-items: center;
        }

        .cart-container .quantity-btn {
            width: 28px;
            height: 30px;
            border: 1px solid #603B29;
            background: #f9f9f9;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .cart-container .quantity-btn:hover {
            background: var(--primary-color);
            color: #fff;
        }

        .cart-container .quantity-input {
            width: 60px;
            text-align: center;
            border: 1px solid #603B29;
            background: #EAE5DF;
            padding: 0.2rem;
            font-size: 0.9rem;
            color: #603B29;
        }

        .cart-container .remove-btn {
            color: #603B29;
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.3s;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .cart-container .remove-btn:hover {
            font-size: 1.3rem;
        }

        .cart-container .summary-box {
            background: #EAE5DF;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border: 1px solid #e0e0e0;
        }

        .cart-container .summary-box .back {
            font-weight: 100;
        }

        .cart-container .summary-box h4 {
            margin-bottom: 1rem;
            color: #6b3a0f;
        }

        .cart-container .summary-box p,
        .cart-container .summary-box span,
        .cart-container .summary-box strong {
            color: #6b3a0f;
        }

        .cart-container .summary-box span {
            font-weight: 500;
        }

        .cart-container .summary-box strong {
            font-weight: 700;
        }

        .cart-container .btn-checkout {
            background-color: #603B29;
            color: #fff;
            width: 100%;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            transition: background 0.3s;
        }

        .cart-container .btn-checkout:hover {
            background-color: transparent;
            border: 2px solid #603B29;
            font-weight: 700;
            color: #603B29;
        }

        .cart-container .btn-back {
            width: 100%;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            margin-top: 0.5rem;
            color: #603B29;
            font-weight: 500;
        }

        .cart-container .empty-cart {
            text-align: left;
            padding: 3rem 0;
            color: #603B29;
        }

        .cart-container .color-box {
            display: inline-block;
            width: 15px;
            height: 15px;
            margin-right: 5px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .cart-container .cart-item {
                flex-direction: column;
                align-items: center;
                padding-top: 2rem;
                min-height: auto;
            }

            .cart-container .img-box {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100%;
                max-width: 250px;
                margin-bottom: 1rem;
                margin-left: auto;
                margin-right: auto;
            }

            .cart-container .img-box img {
                max-width: 100%;
                height: auto;
                object-fit: contain;
                display: block;
            }

            .cart-container .quantity-control-cart {
                display: flex;
                align-items: center;
                margin-left: 20px;
            }

            .cart-container .text-box {
                padding-left: 0;
                text-align: center;
            }

            .cart-container .remove-btn {
                top: 10px;
                right: 10px;
            }
        }
    </style>
</head>

<body>
    <?php include './Components/Header.php'; ?>

    <div class="container cart-container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </nav>

        <h2 class="text-center fw-bold mb-4">Cart</h2>

        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <?php
                // Initialize variables
                $cartItems = [];
                $totalItems = 0;
                $subtotal = 0;

                if (isset($_SESSION['user_id'])) {
                    // Logged-in user: Fetch cart from database
                    $userId = intval($_SESSION['user_id']);
                    $query = "SELECT c.product_id, c.quantity, p.name, p.price, p.sale_price, 
                             p.description, p.short_description, pi.image_url,
                             pv.variation_id, pv.price as variation_price, pv.sale_price as variation_sale_price,
                             pv.attributes as variation_attributes
                      FROM cart c 
                      JOIN products p ON c.product_id = p.product_id 
                      LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_main = 1 
                      LEFT JOIN product_variations pv ON p.product_id = pv.product_id AND pv.variation_id = c.variation_id
                      WHERE c.user_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        $row['image'] = $row['image_url'] ?? 'default.jpg';
                        // Use variation price if available, otherwise use product price
                        if ($row['variation_price'] > 0) {
                            $row['effective_price'] = $row['variation_sale_price'] ?: $row['variation_price'];
                        } else {
                            $row['effective_price'] = $row['sale_price'] ?: $row['price'];
                        }
                        // Parse variation attributes
                        if ($row['variation_id'] > 0 && !empty($row['variation_attributes'])) {
                            $attributes = json_decode($row['variation_attributes'], true);
                            if (is_array($attributes)) {
                                $row['variation_display'] = '';
                                $row['attribute_name'] = '';
                                if (isset($attributes['color'])) {
                                    $row['variation_display'] = $attributes['color'];
                                    $row['attribute_name'] = 'Color';
                                } elseif (isset($attributes['size'])) {
                                    $row['variation_display'] = $attributes['size'];
                                    $row['attribute_name'] = 'Size';
                                } elseif (!empty($attributes)) {
                                    $row['variation_display'] = reset($attributes);
                                    $row['attribute_name'] = key($attributes);
                                }
                            } else {
                                $row['variation_display'] = null;
                                $row['attribute_name'] = null;
                            }
                        } else {
                            $row['variation_display'] = null;
                            $row['attribute_name'] = null;
                        }
                        $cartItems[] = $row;
                        $totalItems += $row['quantity'];
                        $subtotal += $row['effective_price'] * $row['quantity'];
                    }
                    $stmt->close();
                } elseif (isset($_SESSION['cart'])) {
                    // Guest user: Fetch cart from session
                    foreach ($_SESSION['cart'] as $product_id => $variations) {
                        foreach ($variations as $variation_id => $quantity) {
                            // Fetch product details and variation attributes from database
                            $query = "SELECT p.product_id, p.name, p.price, p.sale_price, 
                                     p.description, p.short_description, pi.image_url,
                                     pv.variation_id, pv.price as variation_price, pv.sale_price as variation_sale_price,
                                     pv.attributes as variation_attributes
                              FROM products p
                              LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_main = 1
                              LEFT JOIN product_variations pv ON p.product_id = pv.product_id AND pv.variation_id = ?
                              WHERE p.product_id = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("ii", $variation_id, $product_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($row = $result->fetch_assoc()) {
                                $row['image'] = $row['image_url'] ?? 'default.jpg';
                                // Use variation price if available, otherwise use product price
                                if ($row['variation_price'] > 0) {
                                    $row['effective_price'] = $row['variation_sale_price'] ?: $row['variation_price'];
                                } else {
                                    $row['effective_price'] = $row['sale_price'] ?: $row['price'];
                                }
                                $row['quantity'] = $quantity;
                                // Parse variation attributes
                                if ($row['variation_id'] > 0 && !empty($row['variation_attributes'])) {
                                    $attributes = json_decode($row['variation_attributes'], true);
                                    if (is_array($attributes)) {
                                        $row['variation_display'] = '';
                                        $row['attribute_name'] = '';
                                        if (isset($attributes['color'])) {
                                            $row['variation_display'] = $attributes['color'];
                                            $row['attribute_name'] = 'Color';
                                        } elseif (isset($attributes['size'])) {
                                            $row['variation_display'] = $attributes['size'];
                                            $row['attribute_name'] = 'Size';
                                        } elseif (!empty($attributes)) {
                                            $row['variation_display'] = reset($attributes);
                                            $row['attribute_name'] = key($attributes);
                                        }
                                    } else {
                                        $row['variation_display'] = null;
                                        $row['attribute_name'] = null;
                                    }
                                } else {
                                    $row['variation_display'] = null;
                                    $row['attribute_name'] = null;
                                }
                                $cartItems[] = $row;
                                $totalItems += $quantity;
                                $subtotal += $row['effective_price'] * $quantity;
                            }
                            $stmt->close();
                        }
                    }
                }

                if (empty($cartItems)): ?>
                    <div class="empty-cart px-3 text-center text-lg-start">
                        <h3>Your cart is empty 😔</h3>
                        <p>Looks like you haven’t added anything yet. Start exploring and find something you’ll love!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <i class="fas fa-trash-alt remove-btn" data-id="<?php echo $item['product_id']; ?>"
                                data-variation="<?php echo $item['variation_id'] ?? 0; ?>" aria-label="Remove item"></i>
                            <div class="img-box">
                                <img src="../Assets/uploads/products/<?php echo htmlspecialchars($item['image']); ?>"
                                    alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div class="text-box">
                                <h4 class="m-0 pb-1"><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p class="m-0 pb-1"><?php echo htmlspecialchars($item['short_description']); ?></p>
                                <?php if (isset($item['variation_id']) && $item['variation_id'] > 0 && !empty($item['variation_attributes'])): ?>
                                    <?php
                                    // Decode the attributes
                                    $attributes = json_decode($item['variation_attributes'], true);
                                    $displayAttributes = [];

                                    if (is_array($attributes)) {
                                        // Fetch attribute terms from database to get proper names
                                        foreach ($attributes as $attr_id => $term_id) {
                                            $termQuery = "SELECT a.name AS attribute_name, at.term_name 
                                                FROM attribute_term at
                                                JOIN attributes a ON at.attribute_id = a.id
                                                WHERE at.attribute_term_id = ?";
                                            $stmt = $conn->prepare($termQuery);
                                            $stmt->bind_param("i", $term_id);
                                            $stmt->execute();
                                            $termResult = $stmt->get_result();

                                            if ($termRow = $termResult->fetch_assoc()) {
                                                $displayAttributes[] = [
                                                    'name' => $termRow['attribute_name'],
                                                    'value' => $termRow['term_name']
                                                ];
                                            }
                                            $stmt->close();
                                        }
                                    }
                                    ?>

                                    <?php if (!empty($displayAttributes)): ?>
                                        <?php foreach ($displayAttributes as $attr): ?>
                                            <?php
                                            $is_color = preg_match('/(color|colour|colore?)/i', $attr['name']);
                                            $color_code = $is_color ? getColorCodes(strtolower(trim($attr['value']))) : null;
                                            ?>
                                            <p class="text-muted m-0 p-0">
                                                <?php echo htmlspecialchars($attr['name']); ?>:
                                                <?php if ($is_color && $color_code): ?>
                                                    <span class="color-box"
                                                        style="background-color: <?php echo htmlspecialchars($color_code); ?>;"
                                                        title="<?php echo htmlspecialchars($attr['value']); ?>"></span>
                                                    <?php echo htmlspecialchars($attr['value']); ?>
                                                <?php else: ?>
                                                    <?php echo htmlspecialchars($attr['value']); ?>
                                                <?php endif; ?>
                                            </p>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <h5>$<?php echo number_format($item['effective_price'], 2); ?></h5>
                                <div class="quantity-control-cart">
                                    <button class="quantity-btn minus" data-id="<?php echo $item['product_id']; ?>"
                                        data-variation="<?php echo $item['variation_id'] ?? 0; ?>">-</button>
                                    <input type="text" class="quantity-input" data-id="<?php echo $item['product_id']; ?>"
                                        data-variation="<?php echo $item['variation_id'] ?? 0; ?>"
                                        value="<?php echo $item['quantity']; ?>" readonly>
                                    <button class="quantity-btn plus" data-id="<?php echo $item['product_id']; ?>"
                                        data-variation="<?php echo $item['variation_id'] ?? 0; ?>">+</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="summary-box">
                    <div class="d-flex justify-content-between">
                        <h4>Quantity Item :</h4>
                        <span><?php echo $totalItems; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Discount</span>
                        <span>$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                    </div>
                    <button class="btn btn-checkout mt-3">Proceed to Checkout</button>
                    <a href="../index.php" class="btn btn-back"><i class="back-icon fa-solid fa-arrow-left"></i> Back to
                        Shopping</a>
                </div>
            </div>
        </div>
    </div>

    <?php include './Components/Footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Update quantity
            $('.quantity-btn').click(function () {
                const $btn = $(this);
                const productId = $btn.data('id');
                const variationId = $btn.data('variation') || 0;
                const $input = $btn.siblings('.quantity-input');
                let quantity = parseInt($input.val());
                const isPlus = $btn.hasClass('plus');

                quantity = isPlus ? quantity + 1 : quantity - 1;

                if (quantity < 1) {
                    if (confirm('Do you want to remove this item?')) {
                        removeItem(productId, variationId);
                    }
                    return;
                }

                updateQuantity(productId, variationId, quantity);
            });

            // Remove item
            $('.remove-btn').click(function () {
                const productId = $(this).data('id');
                const variationId = $(this).data('variation') || 0;
                if (confirm('Are you sure you want to remove this item?')) {
                    removeItem(productId, variationId);
                }
            });

            function updateQuantity(productId, variationId, quantity) {
                $.ajax({
                    url: './update_cart.php',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        variation_id: variationId,
                        quantity: quantity
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log("Raw response:", response);
                        console.log("Response type:", typeof response);
                        try {
                            const data = (typeof response === 'string') ? JSON.parse(response) : response;
                            if (data.success) {
                                $(`input[data-id="${productId}"][data-variation="${variationId}"]`).val(quantity);
                                updateSummary(data.summary);
                            } else {
                                alert(data.message || 'Error updating quantity');
                            }
                        } catch (e) {
                            console.error("Error parsing response:", e);
                            console.error("Raw response content:", response);
                            alert('Error processing response');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        console.error("Response text:", xhr.responseText);
                        alert('Error connecting to server');
                    }
                });
            }

            function removeItem(productId, variationId) {
                $.ajax({
                    url: './remove_cart.php',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        variation_id: variationId
                    },
                    dataType: 'json', // Expect JSON response
                    success: function (response) {
                        if (response.success) {
                            $(`[data-id="${productId}"][data-variation="${variationId}"]`).closest('.cart-item').remove();
                            updateSummary(response.summary);
                            if (response.cart_empty) {
                                $('.col-lg-8').html('<div class="empty-cart"><h3>Your cart is empty 😔</h3><p>Looks like you haven\'t added anything yet. Start exploring and find something you\'ll love!</p></div>');
                            }
                        } else {
                            alert(response.message || 'Error removing item');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        console.error("Response text:", xhr.responseText);
                        // Try to parse the response anyway in case it's valid JSON
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response && response.success) {
                                // If it was actually successful despite the error
                                $(`[data-id="${productId}"][data-variation="${variationId}"]`).closest('.cart-item').remove();
                                updateSummary(response.summary);
                                if (response.cart_empty) {
                                    $('.col-lg-8').html('<div class="empty-cart px-3"><h3>Your cart is empty 😔</h3><p>Looks like you haven\'t added anything yet. Start exploring and find something you\'ll love!</p></div>');
                                }
                            } else {
                                alert(response.message || 'Error removing item');
                            }
                        } catch (e) {
                            alert('Error connecting to server. Please refresh the page to see changes.');
                        }
                    }
                });
            }

            function updateSummary(summary) {
                console.log("Summary received:", summary);
                if (summary && typeof summary.total_items !== 'undefined' && typeof summary.subtotal !== 'undefined') {
                    const $quantitySpan = $('.summary-box .d-flex').eq(0).find('span').eq(0);
                    const $subtotalSpan = $('.summary-box .d-flex').eq(1).find('span').eq(1);
                    const $totalSpan = $('.summary-box .d-flex').eq(3).find('strong').eq(1);

                    console.log("Quantity span found:", $quantitySpan.length > 0);
                    console.log("Current quantity text:", $quantitySpan.text());

                    $quantitySpan.text(summary.total_items);
                    $subtotalSpan.text('$' + summary.subtotal.toFixed(2));
                    $totalSpan.text('$' + summary.subtotal.toFixed(2));
                } else {
                    console.error("Invalid summary object:", summary);
                }
            }
        });
    </script>
</body>

</html>