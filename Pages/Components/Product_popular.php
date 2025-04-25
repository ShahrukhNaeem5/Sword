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
        $row['main_image'] = $row['main_image'] ?: '../Assets/Images/Category_1.jpg';
        $products[] = $row;
    }
}
?>

<style>
    .product-carousel-container {
        /* background-color: #EAE5DF; */
        background-color:rgb(255, 255, 255);
        padding: 40px 0;
    }

    .product-carousel-container .heading-main {
        font-size: 2.5rem;
        font-weight: 700;
        color: #603b29;
        margin-bottom: 30px;
    }

    .product-carousel-container .swiper {
        padding: 0 20px;
    }

    .product-carousel-container .product-card {
        background-color: #EAE5DF;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        height: 450px; /* Fixed height for consistency */
        display: flex;
        flex-direction: column;
    }

    .product-carousel-container .product-card:hover {
        transform: translateY(-5px);
    }

    .product-carousel-container .product-img {
        width: 100%;
        height: 200px; /* Fixed height for images */
        object-fit: cover;
        border-bottom: 1px solid #ddd;
    }

    .product-carousel-container .product-body {
        padding: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-carousel-container .product-price {
        font-size: 1.5rem;
        color: #603b29;
        font-weight: 600;
    }

    .product-carousel-container .product-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #603b29;
        margin: 10px 0;
      
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .product-carousel-container .product-desc {
        font-size: 0.9rem;
        color: #603b29;
        margin-bottom: 10px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-carousel-container .color-box {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-right: 1px;
        border: 1px solid #ddd;
        border-radius: 50%;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .product-carousel-container .color-box:hover {
        transform: scale(1.1);
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    }

    .product-carousel-container .box-container {
        margin: 10px 0;
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .product-carousel-container .box {
        padding: 3px 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 12px;
        background: #f8f9fa;
    }

    .product-carousel-container .rating {
        font-size: 0.9rem;
        color: #f1c40f;
        margin-bottom: 10px;
    }

    .product-carousel-container .quantity-control {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .product-carousel-container .quantity-selector {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }

    .product-carousel-container .quantity-btn {
        background: #f9f9f9;
        border: none;
        padding: 5px 10px;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .product-carousel-container .quantity-btn:hover {
        background: #e9ecef;
    }

    .product-carousel-container .quantity-input {
        width: 40px;
        text-align: center;
        border: none;
        background: #fff;
        font-size: 1rem;
    }

    /* .product-carousel-container .add-to-cart {
        background-color: #603b29;
        color: rgb(255, 255, 255);
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.2s;
    } */

    /* .product-carousel-container .add-to-cart:hover {
        background: rgb(255, 255, 255);
        color: #603b29;
    } */

    .product-carousel-container .btn-discover {
        margin-top: 30px;
    }

    .product-carousel-container .btn-success {
        background-color: #603b29;
        border: none;
        padding: 10px 30px;
        font-size: 1.1rem;
        transition: background 0.3s ease;
    }

    .product-carousel-container .btn-success:hover {
        background-color: #8b5a2b;
    }

    /* Swiper navigation and pagination styles */
    .product-carousel-container .swiper-button-next,
    .product-carousel-container .swiper-button-prev {
        color: #603b29;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        top: 50%;
        transform: translateY(-50%);
    }

    .product-carousel-container .swiper-button-next:after,
    .product-carousel-container .swiper-button-prev:after {
        font-size: 18px;
    }

    .product-carousel-container .swiper-paginations {
        bottom: 10px;
    }

    .product-carousel-container .swiper-paginations .swiper-pagination-bullet {
        background: #603b29;
        opacity: 0.5;
    }

    .product-carousel-container .swiper-paginations .swiper-pagination-bullet-active {
        opacity: 1;
    }

    @media (max-width: 1024px) {
        .product-carousel-container .product-card {
            height: 420px;
        }

        .product-carousel-container .product-img {
            height: 180px;
        }

        .product-carousel-container .product-title {
            font-size: 1.1rem;
        }

        .product-carousel-container .product-price {
            font-size: 1.3rem;
        }
    }

    @media (max-width: 768px) {
        .product-carousel-container .product-card {
            height: 400px;
        }

        .product-carousel-container .product-img {
            height: 160px;
        }

        .product-carousel-container .product-title {
            font-size: 1rem;
        }

        .product-carousel-container .product-desc {
            font-size: 0.8rem;
        }

        .product-carousel-container .quantity-btn {
            padding: 3px 8px;
            font-size: 0.9rem;
        }

        .product-carousel-container .quantity-input {
            width: 35px;
            font-size: 0.9rem;
        }

        .product-carousel-container .add-to-cart {
            padding: 6px 10px;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 320px) {
        .product-carousel-container .product-card {
            height: 380px;
        }

        .product-carousel-container .product-img {
            height: 140px;
        }
    }
</style>

<div class="product-carousel-container unique">
    <h1 class="text-center mt-5 mb-4 heading-main">Other Popular Products</h1>
    <div class="container-fluid">
        <!-- Swiper container -->
        <div class="swiper productSwipers">
            <div class="swiper-wrapper">
                <?php foreach ($products as $product): ?>
                    <!-- Dynamic Product Slide -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="../Assets/uploads/products/<?= htmlspecialchars($product['main_image']) ?>"
                                 class="product-img" 
                                 alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">
                                        $<?= number_format($product['sale_price'] ?: $product['price'], 2) ?>
                                    </div>
                                    <div class="pb-2"><small>SKU <?= htmlspecialchars($product['sku'] ?: 'N/A') ?></small></div>
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
                                                $color_code = getColorCode($clean_color); // Use the function from single_product.php
                                                ?>
                                                <div class="color-box" 
                                                     style="background-color: <?= htmlspecialchars($color_code) ?>;"
                                                     title="<?= htmlspecialchars($color) ?>">
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="rating">★★★★☆ (4.5)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="<?= $product['product_id'] ?>">-</button>
                                        <input type="text" class="quantity-input" data-id="<?= $product['product_id'] ?>" value="0" readonly>
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

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mineProductsSwiper = new Swiper('.productSwipers', {
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

        // Quantity control functionality for unique Products
        document.querySelectorAll('.unique .quantity-btn').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-id');
                const input = document.querySelector(`.unique .quantity-input[data-id="${productId}"]`);
                let value = parseInt(input.value);

                if (this.classList.contains('plus')) {
                    value = isNaN(value) ? 1 : value + 1;
                } else if (this.classList.contains('minus') && value > 0) {
                    value -= 1;
                }

                input.value = value;
            });
        });

        // Add to cart functionality for unique Products
        document.querySelectorAll('.unique .add-to-cart').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-id');
                const input = document.querySelector(`.unique .quantity-input[data-id="${productId}"]`);
                const quantity = parseInt(input.value);

                if (quantity > 0) {
                    alert(`Added ${quantity} of product ID ${productId} to cart`);
                    input.value = 0;
                } else {
                    alert('Please select at least 1 item');
                }
            });
        });
    });
</script>