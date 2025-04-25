<?php
include '../Config/connection.php';

// Get category_id from URL
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Fetch category details
$categoryQuery = "SELECT name FROM categories WHERE category_id = " . $category_id;
$categoryResult = $conn->query($categoryQuery);
$category = $categoryResult->fetch_assoc();
$categoryName = $category ? htmlspecialchars($category['name']) : 'Category';

// Function to get color codes
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

// Fetch dynamic price range for the category
$price_range_query = "
    SELECT MIN(COALESCE(p.sale_price, p.price)) as min_price, MAX(COALESCE(p.sale_price, p.price)) as max_price 
    FROM products p
    INNER JOIN product_categories pc ON p.product_id = pc.product_id
    WHERE p.status = 'published' AND pc.category_id = ?
";
$price_stmt = $conn->prepare($price_range_query);
$price_stmt->bind_param('i', $category_id);
$price_stmt->execute();
$price_range_result = $price_stmt->get_result();
$price_range = $price_range_result->fetch_assoc();
$min_price = $price_range['min_price'] ?: 0;
$max_price = $price_range['max_price'] ?: 100;

// Fetch unique filter attributes from the database for the category
$color_query = "
    SELECT DISTINCT term_name 
    FROM attribute_term at
    WHERE term_name REGEXP '^(red|blue|green|yellow|purple|black|white|pink|orange|brown|gray|silver|gold)$' 
    AND EXISTS (
        SELECT 1 
        FROM product_attribute_terms pat
        JOIN product_attributes pa ON pat.product_attribute_id = pa.id
        JOIN product_categories pc ON pa.product_id = pc.product_id
        WHERE pat.attribute_term_id = at.attribute_term_id
        AND pc.category_id = ?
    )
    ORDER BY term_name
";
$color_stmt = $conn->prepare($color_query);
$color_stmt->bind_param('i', $category_id);
$color_stmt->execute();
$color_result = $color_stmt->get_result();
$available_colors = [];
if ($color_result && $color_result->num_rows > 0) {
    while ($row = $color_result->fetch_assoc()) {
        $available_colors[] = ucfirst(strtolower($row['term_name']));
    }
}

// Fetch custom attributes for the category
$custom_attributes_query = "
    SELECT a.name as attribute_name, at.term_name
    FROM attributes a
    JOIN attribute_term at ON a.id = at.attribute_id
    WHERE a.name NOT IN ('Color', 'Size') AND at.term_name != ''
    AND EXISTS (
        SELECT 1 
        FROM product_attribute_terms pat
        JOIN product_attributes pa ON pat.product_attribute_id = pa.id
        JOIN product_categories pc ON pa.product_id = pc.product_id
        WHERE pat.attribute_term_id = at.attribute_term_id
        AND pc.category_id = ?
    )
    ORDER BY a.name, at.term_name
";
$custom_stmt = $conn->prepare($custom_attributes_query);
$custom_stmt->bind_param('i', $category_id);
$custom_stmt->execute();
$custom_attributes_result = $custom_stmt->get_result();
$custom_attributes = [];
if ($custom_attributes_result && $custom_attributes_result->num_rows > 0) {
    while ($row = $custom_attributes_result->fetch_assoc()) {
        $custom_attributes[$row['attribute_name']][] = $row['term_name'];
    }
}

// Pagination settings
$items_per_page = 9;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;

// Handle filtering and sorting
$price_min = isset($_GET['price_min']) ? floatval($_GET['price_min']) : $min_price;
$price_max = isset($_GET['price_max']) ? floatval($_GET['price_max']) : $max_price;
$colors = isset($_GET['colors']) ? (array)$_GET['colors'] : [];
$custom_filters = [];
foreach ($custom_attributes as $attr_name => $terms) {
    $param_name = strtolower(str_replace(' ', '_', $attr_name));
    $custom_filters[$attr_name] = isset($_GET[$param_name]) ? (array)$_GET[$param_name] : [];
}
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at_desc';

// Build the WHERE clause for filtering
$where_clauses = ["p.status = 'published'", "pc.category_id = ?"];
$params = [$category_id];
$param_types = 'i';

if ($price_min > $min_price || $price_max < $max_price) {
    $where_clauses[] = "(p.sale_price IS NOT NULL AND p.sale_price BETWEEN ? AND ? OR p.sale_price IS NULL AND p.price BETWEEN ? AND ?)";
    $params[] = $price_min;
    $params[] = $price_max;
    $params[] = $price_min;
    $params[] = $price_max;
    $param_types .= 'dddd';
}

if (!empty($colors)) {
    $color_placeholders = implode(',', array_fill(0, count($colors), '?'));
    $where_clauses[] = "LOWER(at.term_name) IN ($color_placeholders)";
    $params = array_merge($params, array_map('strtolower', $colors));
    $param_types .= str_repeat('s', count($colors));
}

foreach ($custom_filters as $attr_name => $selected_terms) {
    if (!empty($selected_terms)) {
        $term_placeholders = implode(',', array_fill(0, count($selected_terms), '?'));
        $where_clauses[] = "at.term_name IN ($term_placeholders)";
        $params = array_merge($params, $selected_terms);
        $param_types .= str_repeat('s', count($selected_terms));
    }
}

$where_sql = implode(' AND ', $where_clauses);

// Build the ORDER BY clause for sorting
$order_by = 'p.created_at DESC';
switch ($sort) {
    case 'name_asc':
        $order_by = 'p.name ASC';
        break;
    case 'name_desc':
        $order_by = 'p.name DESC';
        break;
    case 'price_asc':
        $order_by = 'COALESCE(p.sale_price, p.price) ASC';
        break;
    case 'price_desc':
        $order_by = 'COALESCE(p.sale_price, p.price) DESC';
        break;
}

// Fetch total number of products for pagination
$count_query = "
    SELECT COUNT(DISTINCT p.product_id) as total 
    FROM products p
    INNER JOIN product_categories pc ON p.product_id = pc.product_id
    LEFT JOIN product_attributes pa ON p.product_id = pa.product_id
    LEFT JOIN product_attribute_terms pat ON pa.id = pat.product_attribute_id
    LEFT JOIN attribute_term at ON pat.attribute_term_id = at.attribute_term_id
    WHERE $where_sql
";
$count_stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_items = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

// Fetch products with filters, sorting, and pagination
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
        GROUP_CONCAT(
            CONCAT(a.name, ':', at.term_name)
            SEPARATOR '|'
        ) as attributes
    FROM 
        products p
    INNER JOIN 
        product_categories pc ON p.product_id = pc.product_id
    LEFT JOIN 
        product_images pi ON p.product_id = pi.product_id
    LEFT JOIN 
        product_attributes pa ON p.product_id = pa.product_id
    LEFT JOIN 
        attributes a ON pa.attribute_id = a.id
    LEFT JOIN 
        product_attribute_terms pat ON pa.id = pat.product_attribute_id
    LEFT JOIN 
        attribute_term at ON pat.attribute_term_id = at.attribute_term_id
    WHERE 
        $where_sql
    GROUP BY 
        p.product_id
    ORDER BY 
        $order_by
    LIMIT ? OFFSET ?
";
$params[] = $items_per_page;
$params[] = $offset;
$param_types .= 'ii';

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['attributes'] = !empty($row['attributes']) ?
            array_unique(explode('|', $row['attributes'])) : [];
        $row['main_image'] = $row['main_image'] ?: '../Assets/Images/Category_1.jpg';
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products in <?php echo $categoryName; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/Css/style.css">
    <style>
        .product-body {
            display: flex;
            flex-direction: column;
            min-height: 250px;
        }

        .quantity-control {
            margin-top: auto;
            padding-top: 10px;
        }

        .filter-sidebar {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            position: sticky;
            top: 20px;
            height: fit-content;
            z-index: 100;
        }

        .filter-sidebar h5 {
            margin-bottom: 15px;
        }

        .filter-sidebar .price-range {
            position: relative;
            margin-bottom: 20px;
        }

        .filter-sidebar .price-range input[type="range"] {
            width: 100%;
            margin-top: 10px;
        }

        .filter-sidebar .price-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #8B4513;
            margin-top: 5px;
        }

        .filter-sidebar .form-check {
            margin-bottom: 10px;
        }

        .filter-sidebar .btn-apply {
            background: #8B4513;
            color: white;
            width: 100%;
            margin-bottom: 10px;
        }

        .filter-sidebar .btn-reset {
            background: #fff;
            border: 1px solid #ddd;
            width: 100%;
        }

        .product-grid {
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 10px;
        }

        .product-grid::-webkit-scrollbar {
            width: 8px;
        }

        .product-grid::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .product-grid::-webkit-scrollbar-thumb {
            background: #8B4513;
            border-radius: 4px;
        }

        .product-grid::-webkit-scrollbar-thumb:hover {
            background: #6b3a0f;
        }

        @media (max-width: 768px) {
            .filter-sidebar {
                position: relative;
                top: 0;
                margin-bottom: 20px;
            }

            .filter-sidebar.collapse:not(.show) {
                display: none;
            }

            .filter-sidebar.show {
                display: block;
            }

            .product-grid {
                max-height: none;
                overflow-y: visible;
            }

            .row-cols-md-3 {
                row-cols-2;
            }

            .row-cols-lg-3 {
                row-cols-2;
            }

            .btn-brown {
                background-color: #8B4513;
                border-color: #8B4513;
            }

            .btn-brown:hover {
                background-color: #6b3a0f;
                border-color: #6b3a0f;
            }
        }

        .breadcrumb-item a {
            color: #6b3a0f;
        }

        select[name="sort"] {
            padding: 10px 10px;
            margin-right: 10px;
            border: 2px solid #6b3a0f;
            border-radius: 8px;
            background-color: #fff;
            color: #333;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath d='M7 10l5 5 5-5z' fill='%234CAF50'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px 16px;
        }

        select[name="sort"]:hover {
            border-color: #6b3a0f;
            box-shadow: 0 0 5px #6b3a0f;
        }

        select[name="sort"]:focus {
            outline: none;
            border-color: #6b3a0f;
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

        .product-card img {
            background-color: white;
        }
    </style>
</head>

<body>
    <?php include './Components/Header.php'; ?>

    <div class="container-fluid my-5">
        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-lg-3 col-md-4 mb-4">
                <!-- Filter Button for Small Screens -->
                <button class="btn btn-brown d-md-none mb-3 w-100" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filterSidebar" aria-expanded="false" aria-controls="filterSidebar">
                    <i class="bi bi-filter"></i> Show Filters
                </button>

                <!-- Filter Sidebar Content -->
                <div class="filter-sidebar collapse d-md-block" id="filterSidebar">
                    <h5>Filter</h5>
                    <form method="GET" action="">
                        <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
                        <!-- Price Range -->
                        <div class="price-range">
                            <input type="range" name="price_min" min="<?php echo $min_price; ?>" max="<?php echo $max_price; ?>"
                                value="<?php echo htmlspecialchars($price_min); ?>"
                                oninput="document.getElementById('price-min-label').textContent = '$' + this.value">
                            <input type="range" name="price_max" min="<?php echo $min_price; ?>" max="<?php echo $max_price; ?>"
                                value="<?php echo htmlspecialchars($price_max); ?>"
                                oninput="document.getElementById('price-max-label').textContent = '$' + this.value">
                            <div class="price-labels">
                                <span id="price-min-label">$<?php echo $price_min; ?></span>
                                <span id="price-max-label">$<?php echo $max_price; ?></span>
                            </div>
                        </div>

                        <!-- Colors -->
                        <h6>Color</h6>
                        <?php foreach ($available_colors as $color): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="colors[]"
                                    value="<?php echo strtolower($color); ?>" id="color-<?php echo strtolower($color); ?>"
                                    <?php echo in_array(strtolower($color), $colors) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="color-<?php echo strtolower($color); ?>">
                                    <?php echo $color; ?>
                                    <span class="color-box" style="background-color: <?php echo getColorCodes($color); ?>;"></span>
                                </label>
                            </div>
                        <?php endforeach; ?>

                        <!-- Custom Attributes -->
                        <?php foreach ($custom_attributes as $attr_name => $terms): ?>
                            <h6 class="mt-3"><?php echo htmlspecialchars($attr_name); ?></h6>
                            <?php if (!empty($terms)): ?>
                                <?php foreach ($terms as $term): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            name="<?php echo strtolower(str_replace(' ', '_', $attr_name)); ?>[]"
                                            value="<?php echo htmlspecialchars($term); ?>"
                                            id="<?php echo strtolower(str_replace(' ', '_', $attr_name)); ?>-<?php echo htmlspecialchars($term); ?>"
                                            <?php echo in_array($term, $custom_filters[$attr_name]) ? 'checked' : ''; ?>>
                                        <label class="form-check-label"
                                            for="<?php echo strtolower(str_replace(' ', '_', $attr_name)); ?>-<?php echo htmlspecialchars($term); ?>">
                                            <?php echo htmlspecialchars($term); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No <?php echo htmlspecialchars(strtolower($attr_name)); ?> available.</p>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <!-- Apply and Reset Buttons -->
                        <button type="submit" class="btn btn-apply mt-3">Apply</button>
                        <button type="button" class="btn btn-reset" onclick="window.location.href='Categorized_products.php?category_id=<?php echo $category_id; ?>'">Reset All</button>
                    </form>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-md-8">
                <div class="sub-head d-flex flex-column flex-md-column flex-lg-row align-items-center">
                    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2 mb-md-0">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="Categorized_products.php">Products</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $categoryName; ?></li>
                        </ol>
                    </nav>
                    <div class="flex-grow-1 text-center">
                        <span class="text-white display-4 fw-bold">Explore <?php echo $categoryName; ?></span>
                    </div>
                </div>

                <!-- Header with Sorting -->
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="heading-main">Products in <?php echo $categoryName; ?></h1>
                    <div>
                        <select name="sort"
                            onchange="window.location.href='Categorized_products.php?category_id=<?php echo $category_id; ?>&' + (new URLSearchParams({ ...Object.fromEntries(new URLSearchParams(window.location.search)), sort: this.value, page: 1 }).toString())">
                            <option value="created_at_desc" <?php echo $sort == 'created_at_desc' ? 'selected' : ''; ?>>Sort by: Newest</option>
                            <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                            <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                            <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price (Low to High)</option>
                            <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
                        </select>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="product-grid">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 g-4">
                        <?php if (empty($products)): ?>
                            <div class="col">
                                <p>No products found in this category.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                                <a href="./Single_product.php?product_id=<?php echo htmlspecialchars($product['product_id']); ?>">
                                    <div class="product-card">
                                        <img src="../Assets/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>"
                                            class="product-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <div class="product-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="product-price">
                                                    $<?php echo number_format($product['sale_price'] ?: $product['price'], 2); ?>
                                                </div>
                                                <div><small>SKU <?php echo htmlspecialchars($product['sku'] ?: 'N/A'); ?></small></div>
                                            </div>
                                            <h5 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                            <p class="product-desc">
                                                <?php echo htmlspecialchars($product['short_description'] ?: substr($product['description'], 0, 100) . (strlen($product['description']) > 100 ? '...' : '')); ?>
                                            </p>
                                            <div class="box-container">
                                                <?php if (!empty($product['attributes'])): ?>
                                                    <?php
                                                    $color_terms = [];
                                                    $other_attributes = [];

                                                    // Group attributes by name
                                                    foreach ($product['attributes'] as $attr) {
                                                        list($attr_name, $term) = explode(':', $attr, 2);
                                                        if (strtolower($attr_name) === 'color') {
                                                            $color_terms[] = $term;
                                                        } else {
                                                            $other_attributes[$attr_name][] = $term;
                                                        }
                                                    }
                                                    ?>

                                                    <!-- Render other attributes first -->
                                                    <?php foreach ($other_attributes as $attr_name => $terms): ?>
                                                        <div class="box"><?php echo htmlspecialchars($attr_name . ': ' . implode(', ', $terms)); ?></div>
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
                                                            $color_code = getColorCodes($clean_color);
                                                            ?>
                                                            <div class="color-box"
                                                                style="background-color: <?php echo htmlspecialchars($color_code); ?>;"
                                                                title="<?php echo htmlspecialchars($color); ?>"></div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="rating">★★★★☆ (4.5)</div>
                                            <div class="quantity-control">
                                                <div class="row">
                                                    <!-- Left Column: Quantity Selector -->
                                                    <div class="col-8">
                                                        <div class="quantity-selector">
                                                            <button class="quantity-btn minus"
                                                                data-id="<?php echo $product['product_id']; ?>">-</button>
                                                            <input type="text" class="quantity-input"
                                                                data-id="<?php echo $product['product_id']; ?>" value="0" readonly>
                                                            <button class="quantity-btn plus"
                                                                data-id="<?php echo $product['product_id']; ?>">+</button>
                                                        </div>
                                                    </div>

                                                    <!-- Right Column: Add to Cart Button -->
                                                    <div class="col-4">
                                                        <button class="add-to-cart" data-id="<?php echo $product['product_id']; ?>">
                                                            <i class="fa-solid fa-cart-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mt-4">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="?category_id=<?php echo $category_id; ?>&page=<?php echo $page - 1; ?>&price_min=<?php echo $price_min; ?>&price_max=<?php echo $price_max; ?>&<?php echo http_build_query(['colors' => $colors]); ?>&<?php echo http_build_query($custom_filters); ?>&sort=<?php echo $sort; ?>">Previous</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link"
                                        href="?category_id=<?php echo $category_id; ?>&page=<?php echo $i; ?>&price_min=<?php echo $price_min; ?>&price_max=<?php echo $price_max; ?>&<?php echo http_build_query(['colors' => $colors]); ?>&<?php echo http_build_query($custom_filters); ?>&sort=<?php echo $sort; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="?category_id=<?php echo $category_id; ?>&page=<?php echo $page + 1; ?>&price_min=<?php echo $price_min; ?>&price_max=<?php echo $price_max; ?>&<?php echo http_build_query(['colors' => $colors]); ?>&<?php echo http_build_query($custom_filters); ?>&sort=<?php echo $sort; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include './Components/Footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Quantity control functionality
            document.querySelectorAll('.quantity-btn').forEach(button => {
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

            // Add to cart functionality
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function () {
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
            });
        });
    </script>
</body>

</html>