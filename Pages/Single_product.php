<?php
include '../Config/connection.php';

function getColorCode($colorName)
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

// Fetch all product images for the gallery
$gallery_query = "SELECT image_url FROM product_images WHERE product_id = ? ORDER BY is_main DESC";
$stmt = $conn->prepare($gallery_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$gallery_result = $stmt->get_result();
$gallery_images = [];
while ($row = $gallery_result->fetch_assoc()) {
    $gallery_images[] = $row['image_url'];
}
// If no gallery images, use a fallback
if (empty($gallery_images)) {
    $gallery_images = [$product['main_image'], '../Assets/Images/sword-product.webp'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Product Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Add Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="../Assets/Css/style.css">
    <style>
        /* Scope image styles to avoid affecting carousel */
        
        /* Tab styles */
        .policy-tabs {
            margin-top: 40px;
            border-bottom: 1px solid #ddd;
        }

        .policy-tabs .nav-link {
            color: #603b29;
            font-size: 1.1rem;
            font-weight: 500;
            padding: 10px 20px;
            border: none;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .policy-tabs .nav-link:hover {
            color: #8b5a2b;
            border-bottom: 2px solid #8b5a2b;
        }

        .policy-tabs .nav-link.active {
            color: #603b29;
            border-bottom: 2px solid #603b29;
            background: none;
        }

        .tab-content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
            color: #603b29;
            font-size: 1rem;
            line-height: 1.6;
        }

        .tab-sect {
            background-color: #EAE5DF;
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

            .single_product .add-to-cart {
                bottom: 10px;
                left: 10px;
            }

            .policy-tabs .nav-link {
                font-size: 0.9rem;
                padding: 8px 15px;
            }

            .tab-content {
                font-size: 0.9rem;
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <?php include './Components/Header.php'; ?>

    <div class="container-fluid my-5">
        <div class="content single_product">
            

            <?php include './Components/Single_product_component.php' ?>



            <div class="tab-sect container-fluid m-0">
                <!-- Tabbed Section for Shipping and Refund Policies -->
                <div class="row mt-5">
                    <div class="col-12">
                        <ul class="nav nav-tabs policy-tabs" id="policyTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="shipping-tab" data-bs-toggle="tab"
                                    data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping"
                                    aria-selected="true">Shipping Policy</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="refund-tab" data-bs-toggle="tab" data-bs-target="#refund"
                                    type="button" role="tab" aria-controls="refund" aria-selected="false">Refund
                                    Policy</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="policyTabContent">
                            <div class="tab-pane fade show active" id="shipping" role="tabpanel"
                                aria-labelledby="shipping-tab">
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nisi, neque laudantium,
                                    tenetur in sequi eius vero quod mollitia molestias quae ducimus laborum sint
                                    pariatur natus expedita dolorum temporibus quisquam eveniet asperiores quaerat
                                    doloribus tempora itaque? Iste asperiores architecto blanditiis quibusdam
                                    tempora
                                    voluptate inventore doloribus dignissimos ipsa fugiat deleniti, explicabo eaque
                                    alias adipisci cum facilis quo quisquam molestias porro esse aspernatur? Sed
                                    incidunt maiores ea, ad adipisci voluptas ipsum blanditiis sequi nobis, ab,
                                    impedit
                                    soluta a commodi! Ullam quis deleniti tempora aperiam corporis nesciunt, impedit
                                    amet sunt? Laborum, impedit! Distinctio inventore officiis sunt pariatur
                                    recusandae
                                    dolores? Doloribus molestiae officia dolor consequatur.</p>

                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonummy nibh
                                    euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum
                                    dolor
                                    sit amet, consectetur adipiscing elit, sed diam.</p>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonummy nibh
                                    euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum
                                    dolor
                                    sit amet, consectetur adipiscing elit, sed diam.</p>
                            </div>
                            <div class="tab-pane fade" id="refund" role="tabpanel" aria-labelledby="refund-tab">
                                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Veniam neque libero
                                    beatae
                                    possimus porro et ipsum, quae veritatis, magni ab ea pariatur? Incidunt, quia?
                                    Enim
                                    a fuga dolore soluta inventore dolorem distinctio cum corrupti voluptatem
                                    praesentium quae nulla, ullam doloribus officia, cumque expedita at ipsa placeat
                                    cupiditate minima, in voluptates ea repudiandae. Iure, consequatur ullam illo
                                    laboriosam dolores sint nobis similique nemo architecto inventore, impedit alias
                                    qui
                                    ea facilis quaerat odit vel repellat culpa rerum ipsa? Quaerat, quod nesciunt.
                                    Qui
                                    id veritatis sapiente, exercitationem omnis recusandae vel corrupti sit,
                                    mollitia
                                    veniam reprehenderit quod nemo harum at fuga cum et dolorem.</p>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed diam nonummy nibh
                                    euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum
                                    dolor
                                    sit amet, consectetur adipiscing elit, sed diam.</p>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing cons.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
        
    </div>
    <?php include './Components/Product_popular.php' ?>
    <?php include './Components/Footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   
</body>

</html>