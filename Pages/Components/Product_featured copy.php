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
    }

    .product-card img{
        background-color: white;
    }
</style>

<?php
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

// Fetch products from database
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
        (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_main = 1 LIMIT 1) as main_image,
        GROUP_CONCAT(DISTINCT at.term_name SEPARATOR '|') as attribute_terms
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
    WHERE 
        p.status = 'published'
    GROUP BY 
        p.product_id
    ORDER BY 
        p.created_at DESC
    LIMIT 8";

$result = $conn->query($query);

$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['attribute_terms'] = !empty($row['attribute_terms']) ?
            array_unique(explode('|', $row['attribute_terms'])) : [];
        $row['main_image'] = $row['main_image'] ?: './Assets/Images/Category_1.jpg';
        $products[] = $row;
    }
}
?>


<div class="product-carousel-container feature">
    <h1 class="text-center mt-5 mb-4 heading-main">Featured Products</h1>
    <div class="container-fluid">
        <!-- Swiper container -->
        <div class="swiper productSwipers">
            <div class="swiper-wrapper">
                <?php foreach ($products as $product): ?>
                    <!-- Dynamic Product Slide -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/uploads/products/<?= htmlspecialchars($product['main_image']) ?>"
                                class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">
                                        $<?= number_format($product['sale_price'] ?: $product['price'], 2) ?></div>
                                    <div class="pb-2"><small>SKU <?= htmlspecialchars($product['sku'] ?: 'N/A') ?></small>
                                    </div>
                                </div>
                                <h5 class="product-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="product-desc">
                                    <?= htmlspecialchars($product['short_description'] ?: substr($product['description'], 0, 100) . (strlen($product['description']) > 100 ? '...' : '')) ?>
                                </p>
                                <div class="box-container">
                                    <?php if (!empty($product['attribute_terms'])): ?>
                                        <?php
                                        $color_terms = [];
                                        $other_terms = [];

                                        foreach ($product['attribute_terms'] as $term) {
                                            if (preg_match('/(color|colour|colore?)\s*:\s*(.+)/i', $term, $matches)) {
                                                $color_terms[] = trim($matches[2]);
                                            } elseif (preg_match('/^(red|green|blue|yellow|black|white|pink|purple|orange|brown|gray|silver|gold)$/i', $term)) {
                                                $color_terms[] = trim($term);
                                            } else {
                                                $other_terms[] = $term;
                                            }
                                        }
                                        ?>

                                        <!-- Render other terms first -->
                                        <?php if (!empty($other_terms)): ?>
                                            <?php foreach (array_slice($other_terms, 0, 3) as $term): ?>
                                                <div class="box"><?= htmlspecialchars($term) ?></div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <!-- Add a line break if there are other terms and color terms -->
                                        <?php if (!empty($other_terms) && !empty($color_terms)): ?>
                                            <div style="width: 100%;"></div>
                                        <?php endif; ?>

                                        <!-- Render color terms last -->
                                        <?php if (!empty($color_terms)): ?>
                                            <?php foreach (array_slice($color_terms, 0, 3) as $color): ?>
                                                <?php
                                                $clean_color = preg_replace('/[^a-z]/i', '', strtolower($color));
                                                $color_code = getColorCodes($clean_color);
                                                ?>
                                                <div class="color-box" style="background-color: <?= htmlspecialchars($color_code) ?>;"
                                                    title="<?= htmlspecialchars($color) ?>"></div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        
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

<!-- Keep your existing JavaScript exactly the same -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mineProductsSwiper = new Swiper('.productSwipers', {
            slidesPerView: 4, // Show 3 cards by default on large screens
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
                // When window width is >= 320px (mobile)
                320: {
                    slidesPerView: 1,
                    spaceBetween: 10,
                },
                // When window width is >= 768px (tablet)
                768: {
                    slidesPerView: 2,
                    spaceBetween: 15,
                },
                // When window width is >= 1024px (desktop)
                1024: {
                    slidesPerView: 4,
                    spaceBetween: 20,
                }
            }
        });


        // Quantity control functionality for Featured Products
        document.querySelectorAll('.feature .quantity-btn').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-id');
                const input = document.querySelector(`.feature .quantity-input[data-id="${productId}"]`);
                let value = parseInt(input.value);

                if (this.classList.contains('plus')) {
                    value = isNaN(value) ? 1 : value + 1;
                } else if (this.classList.contains('minus') && value > 0) {
                    value -= 1;
                }

                input.value = value;
            });
        });

        // Add to cart functionality for Featured Products
        document.querySelectorAll('.feature .add-to-cart').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-id');
                const input = document.querySelector(`.feature .quantity-input[data-id="${productId}"]`);
                const quantity = parseInt(input.value);

                if (quantity > 0) {
                    alert(`Added ${quantity} of product ID ${productId} to cart`);
                    // Here you would typically send this data to your cart system
                    input.value = 0;
                } else {
                    alert('Please select at least 1 item');
                }
            });
        });
    })
</script>