<?php

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
        p.status = 'publish'
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

<div class="product-carousel-container latest">
    <h1 class="text-center mt-5 mb-4 heading-main">Latest Products</h1>
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
                                        <?php foreach (array_slice($product['attribute_terms'], 0, 3) as $term): ?>
                                            <div class="box"><?= htmlspecialchars($term) ?></div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="box">1ft</div>
                                        <div class="box">2ft</div>
                                        <div class="box">3ft</div>
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


<script>
    document.addEventListener('DOMContentLoaded', function () {

    const latestProductsSwiper = new Swiper('.latestProductSwiper', {
        slidesPerView: 4, // Show 4 cards by default on large screens
        spaceBetween: 20,
        navigation: {
            nextEl: '.swiper-button-next', // Navigation buttons
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-paginations', // Pagination
            clickable: true,
        },
        breakpoints: {
            320: {
                slidesPerView: 1,
                spaceBetween: 10,
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 15,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 20,
            }
        }
    });

     // Quantity control functionality for Latest Products
     document.querySelectorAll('.latest .quantity-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.getAttribute('data-id');
                    const input = document.querySelector(`.latest .quantity-input[data-id="${productId}"]`);
                    let value = parseInt(input.value);

                    if (this.classList.contains('plus')) {
                        value = isNaN(value) ? 1 : value + 1;
                    } else if (this.classList.contains('minus') && value > 0) {
                        value -= 1;
                    }

                    input.value = value;
                });
            });

            // Add to cart functionality for Latest Products
            document.querySelectorAll('.latest .add-to-cart').forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.getAttribute('data-id');
                    const input = document.querySelector(`.latest .quantity-input[data-id="${productId}"]`);
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
            });

</script>