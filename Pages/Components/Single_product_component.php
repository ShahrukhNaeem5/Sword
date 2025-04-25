<?php



// Fetch product data
$product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 1;
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
        (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_main = 0 LIMIT 1) as main_image,
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
        p.product_id = ? AND p.status = 'published'
    GROUP BY 
        p.product_id";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if ($product) {
    $product['attribute_terms'] = !empty($product['attribute_terms']) ?
        array_unique(explode('|', $product['attribute_terms'])) : [];
    $product['main_image'] = $product['main_image'] ?: '../Assets/uploads/products/67fd7ff844256_Category_1';
} else {
    $product = [
        'product_id' => 1,
        'name' => 'Sample Product',
        'description' => 'This is a sample product description.',
        'short_description' => 'Sample short description.',
        'price' => 99.99,
        'sale_price' => 79.99,
        'sku' => 'SAMPLE123',
        'stock_status' => 'in_stock',
        'main_image' => '../Assets/uploads/products/67fd7ff3e5033_slide_1.jpg',
        'attribute_terms' => ['Color: Red', 'Size: M', 'Color: Blue']
    ];
}

// Fetch only non-main product images for the gallery
$gallery_query = "SELECT image_url FROM product_images WHERE product_id = ? AND is_main = 0 ORDER BY image_url";
$stmt = $conn->prepare($gallery_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$gallery_result = $stmt->get_result();
$gallery_images = [];
while ($row = $gallery_result->fetch_assoc()) {
    $gallery_images[] = $row['image_url'];
}
// If no gallery images, use a fallback (excluding the main image)
if (empty($gallery_images)) {
    $gallery_images = ['../Assets/Images/sword-product.webp'];
}
?>
<style>
    .single_product .image-container img {
        width: 100%;
        border-radius: 8px;
    }

    .single_product .main-image img {
        max-height: 600px;
        height: auto;
        object-fit: contain;
    }

    .single_product .gallery-thumbnails img {
        height: 80px;
        object-fit: cover;
    }

    .single_product .product-details {
        padding: 20px;
        position: relative;
        min-height: 400px;
    }

    .single_product .product-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .single_product .product-price {
        font-size: 1.8rem;
        color: #603b29;
        margin-bottom: 10px;
    }

    .single_product .product-sku,
    .single_product .product-stock {
        font-size: 0.9rem;
        color: #603b29;
        margin-bottom: 10px;
    }

    .single_product .product-desc {
        font-size: 1rem;
        color: #603b29;
        margin-bottom: 20px;
    }

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

    .quantity-control {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }

    .quantity-btn {
        background: #f9f9f9;
        border: none;
        padding: 5px 10px;
        font-size: 1.2rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .quantity-btn:hover {
        background: #e9ecef;
    }

    .quantity-input {
        width: 50px;
        text-align: center;
        border: none;
        background: #fff;
        font-size: 1.2rem;
    }

    .single_product .add-to-cart-btn {
        bottom: 20px;
        color: rgb(255, 255, 255);
        background-color: #603b29;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 1.2rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
        position: absolute;
        left: 20px;
    }

    .single_product .add-to-cart-btn:hover {
        background: rgb(255, 255, 255);
        border: solid 2px #603b29;
        color: #603b29;
    }

    /* Gallery-specific styles */
    .single_product .image-container {
        display: flex;
        gap: 10px;
    }

    .single_product .gallery-thumbnails {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 80px;
    }

    .single_product .gallery-thumbnails img {
        width: 100%;
        border: 1px solid #ddd;
        transition: border-color 0.2s;
        cursor: pointer;
    }

    .single_product .gallery-thumbnails img:hover,
    .single_product .gallery-thumbnails img.active {
        border-color: #603b29;
    }

    @media (max-width: 768px) {
        .single_product .image-container {
            flex-direction: column;
        }

        .single_product .gallery-thumbnails {
            flex-direction: row;
            width: 100%;
            overflow-x: auto;
            white-space: nowrap;
        }

        .single_product .gallery-thumbnails img {
            width: 60px;
            height: 60px;
            display: inline-block;
        }

        .single_product .main-image img {
            max-height: 400px;
        }

        .single_product .product-details {
            min-height: 500px;
        }

        .single_product .add-to-cart-btn {
            bottom: 10px;
            left: 10px;
        }
    }
</style>

<div class="container my-product">
    <div class="row">
        <div class="col-lg-6">
            <div class="image-container">
                <div class="gallery-thumbnails">
                    <?php if (!empty($gallery_images)): ?>
                        <?php foreach ($gallery_images as $index => $image): ?>
                            <img src="../Assets/uploads/products/<?php echo htmlspecialchars($image); ?>"
                                alt="Product Image <?php echo $index + 1; ?>"
                                class="<?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeMainImage(this)">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="main-image">
                    <img src="../Assets/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>" id="mainProductImage">
                </div>
            </div>
        </div>
        <div class="col-lg-6 product-details">
            <h2 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h2>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <div class="d-flex justify-content-between">
                <div class="product-price">
                    $<?php echo number_format($product['sale_price'], 2); ?>
                </div>
                <div class="product-sku mt-3">
                    SKU: <?php echo htmlspecialchars($product['sku'] ?: 'N/A'); ?>
                </div>
            </div>
            <p class="product-desc">
                <?php echo htmlspecialchars($product['short_description'] ?: substr($product['description'], 0, 200) . (strlen($product['description']) > 200 ? '...' : '')); ?>
            </p>
            <div class="box-container">
                <?php if (!empty($product['attribute_terms'])): ?>
                    <?php
                    $color_terms = [];
                    $other_attributes = [];

                    // Group attributes by name
                    foreach ($product['attribute_terms'] as $attr) {
                        if (preg_match('/(color|colour|colore?)\s*:\s*(.+)/i', $attr, $matches)) {
                            $color_terms[] = trim($matches[2]);
                        } elseif (preg_match('/^(red|green|blue|yellow|black|white|pink|purple|orange|brown|gray|silver|gold)$/i', $attr)) {
                            $color_terms[] = trim($attr);
                        } else {
                            $other_attributes[] = $attr;
                        }
                    }
                    ?>
                    <!-- Render other attributes first -->
                    <?php foreach ($other_attributes as $attr): ?>
                        <div class="box"><?php echo htmlspecialchars($attr); ?></div>
                    <?php endforeach; ?>
                    <!-- Add a line break if there are other attributes and color terms -->
                    <?php if (!empty($other_attributes) && !empty($color_terms)): ?>
                        <div style="width: 100%;"></div>
                    <?php endif; ?>
                    <!-- Render color terms last -->
                    <?php if (!empty($color_terms)): ?>
                        <?php foreach ($color_terms as $color): ?>
                            <?php
                            $clean_color = preg_replace('/[^a-z]/i', '', strtolower($color));
                            $color_code = getColorCode($clean_color);
                            ?>
                            <div class="color-box" style="background-color: <?php echo htmlspecialchars($color_code); ?>;"
                                title="<?php echo htmlspecialchars($color); ?>"></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="quantity-control">
                <div class="quantity-selector">
                    <button class="quantity-btn minus" data-id="<?php echo $product['product_id']; ?>">-</button>
                    <input type="text" class="quantity-input" data-id="<?php echo $product['product_id']; ?>" value="0"
                        readonly>
                    <button class="quantity-btn plus" data-id="<?php echo $product['product_id']; ?>">+</button>
                </div>
            </div>
            <button class="add-to-cart-btn" data-id="<?php echo $product['product_id']; ?>">Add to Cart</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Quantity controls
        const quantityButtons = document.querySelectorAll('.quantity-btn');
        const addToCartButton = document.querySelector('.add-to-cart');

        quantityButtons.forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-id');
                const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
                let value = parseInt(input.value);

                if (this.classList.contains('plus')) {
                    value = isNaN(value) ? 1 : value + 1;
                } else if (this.classList.contains('minus') && value > 0) {
                    value -= 1;
                }

                input.value = value;
            });
        });

        addToCartButton.addEventListener('click', function () {
            const productId = this.getAttribute('data-id');
            const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
            const quantity = parseInt(input.value);

            if (quantity > 0) {
                alert(`Added ${quantity} of product ID ${productId} to cart`);
                input.value = 0;
            } else {
                alert('Please select at least 1 item');
            }
        });

        // Function to change the main image
        function changeMainImage(element) {
            const mainImage = document.getElementById('mainProductImage');
            const thumbnails = document.querySelectorAll('.gallery-thumbnails img');

            // Update main image source
            mainImage.src = element.src;

            // Update active class
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            element.classList.add('active');
        }

        // Ensure changeMainImage is globally accessible
        window.changeMainImage = changeMainImage;
    });
</script>