<style>
    .color-box {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-right: 1px;
        border: 1px solid #ddd;
        border-radius: 50%;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .color-box:hover {
        transform: scale(1.1);
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    }

    .color-box.selected,
    .box.selected {
        border: 2px solid #007bff;
        transform: scale(1.1);
    }

    .box-container {
        margin: 10px 0;
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .box {
        padding: 3px 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 12px;
        background: #f8f9fa;
        cursor: pointer;
    }

    .product-card img {
        background-color: white;
    }

    .stock-status {
        font-size: 12px;
        color: #dc3545;
    }

    .stock-status.in-stock {
        color: #28a745;
    }
</style>

<?php
function getColorCodesLatest($colorName)
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

// Fetch products and their variations from database
$query = "
    SELECT 
        p.product_id, 
        p.name, 
        p.description, 
        p.short_description,
        p.price, 
        p.sale_price, 
        p.sku,
        p.stock_status,
        (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_main = 1 LIMIT 1) AS main_image,
        GROUP_CONCAT(DISTINCT CONCAT(a.name, ':', at.term_name, ':', at.attribute_term_id) SEPARATOR '|') AS attribute_terms,
        pv.variation_id,
        pv.sku AS variation_sku,
        pv.price AS variation_price,
        pv.sale_price AS variation_sale_price,
        pv.stock_quantity,
        pv.attributes AS variation_attributes
    FROM 
        products p
    LEFT JOIN 
        product_images pi ON p.product_id = pi.product_id
    LEFT JOIN 
        product_attributes pa ON p.product_id = pa.product_id
    LEFT JOIN 
        product_attribute_terms pat ON pa.id = pat.product_attribute_id
    LEFT JOIN 
        attribute_term at ON pat.attribute_term_id = at.attribute_term_id
    LEFT JOIN 
        attributes a ON pa.attribute_id = a.id
    LEFT JOIN 
        product_variations pv ON p.product_id = pv.product_id
    WHERE 
        p.status = 'published'
    GROUP BY 
        p.product_id, pv.variation_id
    ORDER BY 
        p.created_at DESC
    LIMIT 8";

$result = $conn->query($query);
if (!$result) {
    error_log("Query Error: " . $conn->error);
    die("Database query failed: " . $conn->error);
}

$products = [];
$current_product_id = null;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        error_log("Processing row: product_id={$row['product_id']}, variation_id=" . ($row['variation_id'] ?? 'null'));
        if ($row['product_id'] !== $current_product_id) {
            $current_product_id = $row['product_id'];
            $product = [
                'product_id' => $row['product_id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'short_description' => $row['short_description'],
                'price' => $row['price'],
                'sale_price' => $row['sale_price'],
                'sku' => $row['sku'],
                'stock_status' => $row['stock_status'],
                'main_image' => $row['main_image'] ?: './Assets/Images/Category_1.jpg',
                'attribute_terms' => !empty($row['attribute_terms']) ? array_unique(explode('|', $row['attribute_terms'])) : [],
                'variations' => []
            ];
            $products[$row['product_id']] = $product;
        }
        if ($row['variation_id']) {
            $attributes = json_decode($row['variation_attributes'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON decode error for variation_id {$row['variation_id']}: " . json_last_error_msg());
                $attributes = [];
            }
            $products[$row['product_id']]['variations'][] = [
                'variation_id' => $row['variation_id'],
                'sku' => $row['variation_sku'],
                'price' => $row['variation_price'],
                'sale_price' => $row['variation_sale_price'],
                'stock_quantity' => $row['stock_quantity'],
                'attributes' => $attributes
            ];
        }
    }
} else {
    error_log("No products found in query");
}

// Convert $products to a list for array_map
$products_list = array_values($products);
error_log("Products fetched: " . json_encode($products_list, JSON_PRETTY_PRINT));
?>

<div class="product-carousel-container latest">
    <h1 class="text-center mt-5 mb-4 heading-main">Latest Products</h1>
    <div class="container-fluid">
        <!-- Swiper container -->
        <div class="swiper latestProductsSwiper">
            <div class="swiper-wrapper">
                <?php foreach ($products as $product): ?>
                    <!-- Dynamic Product Slide -->
                    <div class="swiper-slide">
                        <div class="product-card" data-product-id="<?= $product['product_id'] ?>">
                            <img src="./Assets/uploads/products/<?= htmlspecialchars($product['main_image']) ?>"
                                class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price"
                                        data-default-price="<?= number_format($product['sale_price'] ?: $product['price'], 2) ?>">
                                        $<?= number_format($product['sale_price'] ?: $product['price'], 2) ?>
                                    </div>
                                    <div class="pb-2">
                                        <small class="product-sku"
                                            data-default-sku="<?= htmlspecialchars($product['sku'] ?: 'N/A') ?>">
                                            SKU <?= htmlspecialchars($product['sku'] ?: 'N/A') ?>
                                        </small>
                                    </div>
                                </div>
                                <h5 class="product-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="product-desc">
                                    <?= htmlspecialchars($product['short_description'] ?: substr($product['description'], 0, 100) . (strlen($product['description']) > 100 ? '...' : '')) ?>
                                </p>
                                <div class="stock-status <?= $product['stock_status'] === 'instock' ? 'in-stock' : '' ?>"
                                    data-default-status="<?= $product['stock_status'] === 'instock' ? 'In Stock' : 'Out of Stock' ?>">
                                    <?= $product['stock_status'] === 'instock' ? 'In Stock' : 'Out of Stock' ?>
                                </div>
                                <div class="box-container">
                                    <?php if (!empty($product['attribute_terms'])): ?>
                                        <?php
                                        $color_terms = [];
                                        $other_terms = [];
                                        $term_map = [];

                                        foreach ($product['attribute_terms'] as $term) {
                                            if (preg_match('/(.+):(.+):(\d+)/', $term, $matches)) {
                                                $attr_name = $matches[1];
                                                $term_name = $matches[2];
                                                $term_id = $matches[3];
                                                $term_map[$term_id] = $term_name;
                                                if (preg_match('/(color|colour|colore?)/i', $attr_name)) {
                                                    $color_terms[] = ['name' => $term_name, 'id' => $term_id];
                                                } else {
                                                    $other_terms[] = ['name' => $term_name, 'id' => $term_id];
                                                }
                                            }
                                        }
                                        ?>

                                        <!-- Render other terms first -->
                                        <?php if (!empty($other_terms)): ?>
                                            <?php foreach (array_slice($other_terms, 0, 3) as $term): ?>
                                                <div class="box" data-term-id="<?= $term['id'] ?>"
                                                    data-product-id="<?= $product['product_id'] ?>">
                                                    <?= htmlspecialchars($term['name']) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <!-- Add a line break if there are other terms and color terms -->
                                        <?php if (!empty($other_terms) && !empty($color_terms)): ?>
                                            <div style="width: 100%;"></div>
                                        <?php endif; ?>

                                        <!-- Render color terms last -->
                                        <?php if (!empty($color_terms)): ?>
                                            <?php foreach (array_slice($color_terms, 0, 3) as $term): ?>
                                                <?php
                                                $clean_color = preg_replace('/[^a-z]/i', '', strtolower($term['name']));
                                                $color_code = getColorCodesLatest($clean_color);
                                                ?>
                                                <div class="color-box" data-term-id="<?= $term['id'] ?>"
                                                    data-product-id="<?= $product['product_id'] ?>"
                                                    style="background-color: <?= htmlspecialchars($color_code) ?>;"
                                                    title="<?= htmlspecialchars($term['name']) ?>"></div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="rating">★★★★☆ (4.5)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus"
                                            data-id="<?= $product['product_id'] ?>">-</button>
                                        <input type="text" class="quantity-input" data-id="<?= $product['product_id'] ?>"
                                            value="0" readonly>
                                        <button class="quantity-btn plus" data-id="<?= $product['product_id'] ?>">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="<?= $product['product_id'] ?>">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                                <!-- Hidden input to store selected variation ID -->
                                <input type="hidden" class="selected-variation" data-id="<?= $product['product_id'] ?>"
                                    value="">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Add pagination -->
            <div class="swiper-paginations"></div>
            <!-- Add navigation buttons -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
    <div class="btn-discover w-100 d-flex justify-content-center">
        <button class="btn btn-lg btn-success">See All</button>
    </div>
</div>

<script>
    // Store variation data in JavaScript
    const productVariationsLatest = <?php echo json_encode($products_list, JSON_PRETTY_PRINT) ?>;
    console.log('productVariationsLatest:', productVariationsLatest);

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Swiper
        const latestProductsSwiper = new Swiper('.latestProductsSwiper', {
            slidesPerView: 4,
            spaceBetween: 20,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-paginations',
                clickable: true,
            },
            breakpoints: {
                320: { slidesPerView: 1, spaceBetween: 10 },
                768: { slidesPerView: 2, spaceBetween: 15 },
                1024: { slidesPerView: 4, spaceBetween: 20 }
            }
        });

        // Attribute click handler
        document.querySelectorAll('.latest .color-box, .latest .box').forEach(element => {
            element.addEventListener('click', function () {
                const productId = this.getAttribute('data-product-id');
                const termId = this.getAttribute('data-term-id');
                console.log(`Attribute clicked - productId: ${productId}, termId: ${termId}`);

                // Find the product card
                const productCard = this.closest('.product-card');
                if (!productCard) {
                    console.error(`No product card found for productId: ${productId}`);
                    return;
                }

                // Get UI elements
                const priceElement = productCard.querySelector('.product-price');
                const skuElement = productCard.querySelector('.product-sku');
                const stockStatusElement = productCard.querySelector('.stock-status');
                const variationInput = productCard.querySelector('.selected-variation');

                if (!priceElement || !skuElement || !stockStatusElement || !variationInput) {
                    console.error('Missing UI elements in product card:', {
                        priceElement, skuElement, stockStatusElement, variationInput
                    });
                    return;
                }

                // Remove selected class from all attributes in this product card
                productCard.querySelectorAll('.color-box, .box').forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');

                // Find variation matching the selected term
                let selectedVariation = null;
                const product = productVariationsLatest.find(p => p.product_id == productId);

                if (product && product.variations) {
                    const variations = Array.isArray(product.variations) ? product.variations : Object.values(product.variations);
                    console.log('Variations for product:', variations);
                    selectedVariation = variations.find(v => {
                        const attributeValues = Object.values(v.attributes || {}).map(val => String(val));
                        console.log(`Checking variation ${v.variation_id}, attributes:`, attributeValues);
                        return attributeValues.includes(String(termId));
                    });
                    console.log('Selected variation:', selectedVariation);
                } else {
                    console.warn(`No product or variations found for productId: ${productId}`);
                }

                if (selectedVariation) {
                    // Update price
                    const price = selectedVariation.sale_price || selectedVariation.price;
                    priceElement.textContent = `$${parseFloat(price).toFixed(2)}`;
                    console.log(`Updated price to: $${parseFloat(price).toFixed(2)}`);

                    // Update SKU
                    skuElement.textContent = `SKU ${selectedVariation.sku || 'N/A'}`;
                    console.log(`Updated SKU to: ${selectedVariation.sku || 'N/A'}`);

                    // Update stock status
                    const stockText = selectedVariation.stock_quantity > 0 ? 'In Stock' : 'Out of Stock';
                    stockStatusElement.textContent = stockText;
                    stockStatusElement.className = `stock-status ${selectedVariation.stock_quantity > 0 ? 'in-stock' : ''}`;
                    console.log(`Updated stock status to: ${stockText}`);

                    // Store variation ID
                    variationInput.value = selectedVariation.variation_id;
                    console.log(`Updated variation ID to: ${selectedVariation.variation_id}`);
                } else {
                    console.warn(`No variation found for termId: ${termId}`);
                    // Revert to default if no variation found
                    priceElement.textContent = `$${priceElement.getAttribute('data-default-price')}`;
                    skuElement.textContent = `SKU ${skuElement.getAttribute('data-default-sku')}`;
                    stockStatusElement.textContent = stockStatusElement.getAttribute('data-default-status');
                    stockStatusElement.className = `stock-status ${stockStatusElement.getAttribute('data-default-status') === 'In Stock' ? 'in-stock' : ''}`;
                    variationInput.value = '';
                    console.log('Reverted to default values');
                }
            });
        });

        // Quantity control functionality
        document.querySelectorAll('.latest .quantity-btn').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-id');
                const input = document.querySelector(`.latest .quantity-input[data-id="${productId}"]`);
                let value = parseInt(input.value) || 0;

                if (this.classList.contains('plus')) {
                    value++;
                } else if (this.classList.contains('minus') && value > 0) {
                    value--;
                }

                input.value = value;
            });
        });

        // Add to cart functionality
        document.querySelectorAll('.latest .add-to-cart').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-id');
                const input = document.querySelector(`.latest .quantity-input[data-id="${productId}"]`);
                const variationInput = document.querySelector(`.latest .selected-variation[data-id="${productId}"]`);
                const quantity = parseInt(input.value) || 0;
                const variationId = variationInput.value;

                if (quantity <= 0) {
                    alert('Please select at least 1 item');
                    return;
                }

                if (!variationId) {
                    alert('Please select a variation (e.g., color or size) before adding to cart.');
                    return;
                }

                console.log(`Adding to cart - Product: ${productId}, Variation: ${variationId}, Qty: ${quantity}`);

                fetch('./Pages/add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        product_id: productId,
                        variation_id: variationId,
                        quantity: quantity
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(text || 'Network response was not ok');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update cart badge directly with cart_count from response
                            const cartCountElement = document.querySelector('#cart-count');
                            if (cartCountElement) {
                                cartCountElement.textContent = data.cart_count;
                                cartCountElement.style.display = data.cart_count > 0 ? 'inline' : 'none';
                            }

                            // Reset UI
                            input.value = 0; // Reset quantity
                            variationInput.value = ''; // Reset variation
                            const productCard = document.querySelector(`.product-card[data-product-id="${productId}"]`);
                            productCard.querySelectorAll('.color-box, .box').forEach(el => el.classList.remove('selected'));
                            productCard.querySelector('.product-price').textContent = `$${productCard.querySelector('.product-price').getAttribute('data-default-price')}`;
                            productCard.querySelector('.product-sku').textContent = `SKU ${productCard.querySelector('.product-sku').getAttribute('data-default-sku')}`;
                            productCard.querySelector('.stock-status').textContent = productCard.querySelector('.stock-status').getAttribute('data-default-status');
                            productCard.querySelector('.stock-status').className = `stock-status ${productCard.querySelector('.stock-status').getAttribute('data-default-status') === 'In Stock' ? 'in-stock' : ''}`;

                            alert(data.message); // Show success message
                        } else {
                            alert('Error adding to cart: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        // Log raw response for debugging
                        fetch('../../add_to_cart.php').then(res => res.text()).then(text => {
                            console.log('Raw response:', text);
                        });
                        alert('Error adding to cart: ' + error.message);
                    });
            });
        });
    });
</script>