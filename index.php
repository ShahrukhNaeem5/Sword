<?php
include './Config/connection.php';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="./Assets/Css/style.css">

</head>

<body>
    <header class="bg-white">
        <!-- Top information bar - Made responsive -->
        <div class="top-info-bar">
            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <?php
                    // Fetch menu items from database
                    $sql = "SELECT * FROM `top_menu` ORDER BY top_menu_id ASC";
                    $result = $conn->query($sql);

                    // Start the HTML output
                    echo '<div class="policy-links text-center text-md-start mb-2 mb-md-0">';

                    // Check if there are results
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo '<a href="' . htmlspecialchars($row['menu_link']) . '">'
                                . htmlspecialchars($row['menu_name']) . '</a>';
                        }
                    } else {
                        // Fallback if no menu items are found
                        echo '';
                    }

                    // Close the div
                    echo '</div>';

                    // Close connection
                    
                    // Assuming you already have a MySQLi connection variable $conn available
                    
                    // Fetch contact information from database
                    $query = "SELECT phone, whatsapp, email FROM `contact_information` LIMIT 1";
                    $result = $conn->query($query);

                    // Check if query was successful and contains data
                    if ($result && $result->num_rows > 0) {
                        $contact = $result->fetch_assoc();

                        // Output the contact information
                        echo '<div class="contact-info d-flex flex-row flex-md-row gap-4 gap-md-4 text-center text-md-start">';
                        echo '<span><i class="bi bi-telephone-fill text-brown"></i> ' . htmlspecialchars($contact['phone']) . '</span>';
                        echo '<span><i class="bi bi-whatsapp text-brown"></i> ' . htmlspecialchars($contact['whatsapp']) . '</span>';
                        echo '<a href="mailto:' . htmlspecialchars($contact['email']) . '" class="text-brown"><i class="bi bi-envelope-at-fill"></i> ' . htmlspecialchars($contact['email']) . '</a>';
                        echo '</div>';
                    } else {
                        // Fallback if no contact info is found in database
                        echo '';


                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Main header with logo and search - Made responsive -->
        <?php
        // Fetch all logo assets from database
        $assetsQuery = "SELECT desktop_logo, mobile_logo FROM `site_assets` ORDER BY id DESC LIMIT 1";
        $assetsResult = $conn->query($assetsQuery);

        // Set default logo paths
        $defaultDesktopLogo = "./Assets/Images/logo.jpg";
        $defaultMobileLogo = "./Assets/Images/logo-mobile.jpg";

        // Initialize logo variables
        $desktopLogo = $defaultDesktopLogo;
        $mobileLogo = $defaultMobileLogo;

        if ($assetsResult && $assetsResult->num_rows > 0) {
            $assetsData = $assetsResult->fetch_assoc();
            $desktopLogo = !empty($assetsData['desktop_logo']) ? $assetsData['desktop_logo'] : $defaultDesktopLogo;
            $mobileLogo = !empty($assetsData['mobile_logo']) ? $assetsData['mobile_logo'] : $defaultMobileLogo;
        }

        // Simple device detection (you might want to use a more robust method)
        $isMobile = preg_match("/(android|iphone|ipad|mobile)/i", $_SERVER['HTTP_USER_AGENT']);
        $currentLogo = $isMobile ? $mobileLogo : $desktopLogo;
        ?>

        <div class="main-header">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <!-- Search container remains the same -->
                    <div class="search-container order-2 order-lg-1">
                        <div class="search-box">
                            <input type="search" class="form-control" placeholder="Search">
                            <i class="bi bi-search"></i>
                        </div>
                    </div>

                    <!-- Dynamic logo with srcset for responsive images -->
                    <div class="logo-container order-1 order-lg-2 text-center">
                        <img src="<?php echo htmlspecialchars($currentLogo); ?>" srcset="<?php echo htmlspecialchars($desktopLogo); ?> 1200w,
                             <?php echo htmlspecialchars($mobileLogo); ?> 600w"
                            sizes="(max-width: 768px) 600px, 1200px" alt="Company Logo" class="logo">
                    </div>

                    <!-- User actions remain the same -->
                    <div class="user-actions order-3 order-lg-3">
                        <a href="#"><i class="bi bi-box-arrow-in-right"></i> <span class="d-none d-md-inline">Sign
                                In</span></a>
                        <a href="#"><i class="bi bi-cart-check-fill"></i> <span class="d-none d-md-inline">Cart
                                (0)</span></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main navigation - Made responsive with Bootstrap toggle -->
        <nav class="main-navigation navbar navbar-expand-lg">
            <div class="container">
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav mb-0 ps-0">
                        <li class="nav-item">
                            <a href="#" class="nav-link">Home</a>
                        </li>

                        <!-- Category 1 Dropdown -->
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                Category 1 <i class="bi bi-chevron-down ms-1"></i>
                            </a>
                            <ul class="dropdown-menu bg-brown border-0 rounded-0 mt-0">
                                <li><a class="dropdown-item text-white" href="#">Subcategory 1</a></li>
                                <li>
                                    <hr class="dropdown-divider my-0" style="border-color: rgba(255,255,255,0.2);">
                                </li>
                                <li><a class="dropdown-item text-white" href="#">Subcategory 2</a></li>
                                <li>
                                    <hr class="dropdown-divider my-0" style="border-color: rgba(255,255,255,0.2);">
                                </li>
                                <li><a class="dropdown-item text-white" href="#">Subcategory 3</a></li>
                            </ul>
                        </li>

                        <!-- Category 2 Dropdown -->
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                Category 2 <i class="bi bi-chevron-down ms-1"></i>
                            </a>
                            <ul class="dropdown-menu bg-brown border-0 rounded-0 mt-0">
                                <li><a class="dropdown-item text-white" href="#">Subcategory 1</a></li>
                                <li>
                                    <hr class="dropdown-divider my-0" style="border-color: rgba(255,255,255,0.2);">
                                </li>
                                <li><a class="dropdown-item text-white" href="#">Subcategory 2</a></li>
                                <li>
                                    <hr class="dropdown-divider my-0" style="border-color: rgba(255,255,255,0.2);">
                                </li>
                                <li><a class="dropdown-item text-white" href="#">Subcategory 3</a></li>
                            </ul>
                        </li>

                        <!-- Category 3 Dropdown -->
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                Category 3 <i class="bi bi-chevron-down ms-1"></i>
                            </a>
                            <ul class="dropdown-menu bg-brown border-0 rounded-0 mt-0">
                                <li><a class="dropdown-item text-white" href="#">Subcategory 1</a></li>
                                <li>
                                    <hr class="dropdown-divider my-0" style="border-color: rgba(255,255,255,0.2);">
                                </li>
                                <li><a class="dropdown-item text-white" href="#">Subcategory 2</a></li>
                                <li>
                                    <hr class="dropdown-divider my-0" style="border-color: rgba(255,255,255,0.2);">
                                </li>
                                <li><a class="dropdown-item text-white" href="#">Subcategory 3</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- SLIDER -->

    <?php
    // Include database connection
    
    // Fetch slides from database
    $query = "SELECT * FROM slides ORDER BY id ASC";
    $result = $conn->query($query);
    $slides = $result->fetch_all(MYSQLI_ASSOC);
    ?>

    <div id="carouselExampleIndicators" class="carousel-height carousel slide">
        <div class="carousel-indicators">
            <?php foreach ($slides as $index => $slide): ?>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $index ?>"
                    class="<?= $index === 0 ? 'active' : '' ?>" aria-label="Slide <?= $index + 1 ?>"></button>
            <?php endforeach; ?>
        </div>

        <div class="carousel-inner">
            <?php foreach ($slides as $index => $slide): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?> position-relative">
                    <img src="./Assets/uploads/slides/<?= htmlspecialchars($slide['image']) ?>" class="d-block w-100"
                        alt="<?= htmlspecialchars($slide['heading']) ?>">
                    <div class="carousel-caption text-start d-md-block">
                        <?php echo htmlspecialchars_decode($slide['heading']); ?>
                        <?php echo htmlspecialchars_decode($slide['paragraph']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- SECTION ONE -->

    <?php
    // Include database connection
    
    // Fetch section content from database
    $query = "SELECT * FROM sections LIMIT 1"; // Assuming you want the first section
    $result = $conn->query($query);
    $section = $result->fetch_assoc();
    ?>

    <div class="container-fluid px-5 py-5 section">
        <!-- Main Heading from DB with HTML tags preserved -->
        <h1 class="text-center mt-5 mb-5 heading-main">
            <?= strip_tags(htmlspecialchars_decode($section['heading']), '<strong><em><span><a>') ?>
        </h1>

        <div class="row align-items-center">
            <!-- Image Column -->
            <div class="col-12 col-md-6 col-lg-6 section-image">
                <img src="./Assets/uploads/sections/<?= htmlspecialchars($section['image'] ?? 'section_image.webp') ?>"
                    width="100%" alt="<?= htmlspecialchars(strip_tags($section['heading'] ?? 'Sword Collection')) ?>"
                    class="img-fluid rounded shadow">
            </div>

            <!-- Content Column -->
            <div class="col-12 col-md-6 col-lg-6">
                <!-- Sub Heading from DB with HTML tags preserved -->
                <h2 class="heading-sub mb-4">
                    <?= strip_tags(htmlspecialchars_decode($section['sub_heading']), '<strong><em><span><a>') ?>
                </h2>

                <!-- Content from DB with preserved line breaks and limited HTML -->
                <div style="font-size: 1.1rem; line-height: 1.7; color: #555;">
                    <?= strip_tags(htmlspecialchars_decode($section['content']), '<strong><em><span><a><br>') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- CUSTOM CATEGORY-1 -->
   <?php 
   include './Pages/Components/Categories.php'
   ?>
    <!-- CUSTOM CATEGORY-2 END-->







    <!-- Featured Page -->
    <div class="product-carousel-container feature">
        <h1 class="text-center mt-5 mb-4 heading-main">Featured Products</h1>
        <div class="container-fluid">
            <!-- Swiper container -->
            <div class="swiper productSwipers">
                <div class="swiper-wrapper">
                    <!-- Product 1 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_1.jpg" class="product-img" alt="Wireless Headphones">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Wireless Headphones</h5>
                                <p class="product-desc">High-quality wireless headphones with noise cancellation and
                                    20-hour battery life.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.5)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="1">-</button>
                                        <input type="text" class="quantity-input" data-id="1" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="1">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="1">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 2 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_1.jpg" class="product-img" alt="Smart Fitness Tracker">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Wireless Headphones</h5>
                                <p class="product-desc">High-quality wireless headphones with noise cancellation and
                                    20-hour battery life</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.2)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="2">-</button>
                                        <input type="text" class="quantity-input" data-id="2" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="2">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="2">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 3 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_1.jpg" class="product-img"
                                alt="Portable Bluetooth Speaker">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Portable Bluetooth Speaker</h5>
                                <p class="product-desc">High-quality wireless headphones with noise cancellation and
                                    20-hour battery life
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★★ (4.7)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="3">-</button>
                                        <input type="text" class="quantity-input" data-id="3" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="3">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="3">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 4 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_1.jpg" class="product-img" alt="Wireless Charging Pad">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Wireless Charging Pad</h5>
                                <p class="product-desc">Fast charging pad compatible with all Qi-enabled devices.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.0)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="4">-</button>
                                        <input type="text" class="quantity-input" data-id="4" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="4">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="4">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 5 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_1.jpg" class="product-img"
                                alt="Stainless Steel Water Bottle">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Stainless Steel Water Bottle</h5>
                                <p class="product-desc">Keep your drinks hot or cold for hours with this insulated
                                    bottle.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★★ (4.8)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="5">-</button>
                                        <input type="text" class="quantity-input" data-id="5" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="5">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="5">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 6 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_1.jpg" class="product-img" alt="Ergonomic Keyboard">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Ergonomic Keyboard</h5>
                                <p class="product-desc">Comfortable split-design keyboard for long typing sessions.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.3)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="6">-</button>
                                        <input type="text" class="quantity-input" data-id="6" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="6">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="6">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 7 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_1.jpg" class="product-img" alt="4K Action Camera">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">4K Action Camera</h5>
                                <p class="product-desc">Capture your adventures in stunning 4K resolution with image
                                    stabilization.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★★ (4.9)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="7">-</button>
                                        <input type="text" class="quantity-input" data-id="7" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="7">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="7">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 8 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_2.jpg" class="product-img" alt="Smart LED Bulb">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Smart LED Bulb</h5>
                                <p class="product-desc">Control your lights with your phone and change colors with voice
                                    commands.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.1)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="8">-</button>
                                        <input type="text" class="quantity-input" data-id="8" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="8">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="8">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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




    <!-- Unique Pprduct -->
    <div class="product-carousel-container unique">
        <h1 class="text-center mt-5 mb-4 heading-main">Unique Products</h1>
        <div class="container-fluid">
            <div class="swiper UniqueProductSwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_3.jpg" class="product-img" alt="Wireless Headphones">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Wireless Headphones</h5>
                                <p class="product-desc">High-quality wireless headphones with noise cancellation and
                                    20-hour battery life.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.5)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="1">-</button>
                                        <input type="text" class="quantity-input" data-id="1" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="1">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="1">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 2 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_3.jpg" class="product-img" alt="Smart Fitness Tracker">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Wireless Headphones</h5>
                                <p class="product-desc">High-quality wireless headphones with noise cancellation and
                                    20-hour battery life</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.2)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="2">-</button>
                                        <input type="text" class="quantity-input" data-id="2" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="2">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="2">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 3 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_3.jpg" class="product-img"
                                alt="Portable Bluetooth Speaker">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Portable Bluetooth Speaker</h5>
                                <p class="product-desc">High-quality wireless headphones with noise cancellation and
                                    20-hour battery life
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★★ (4.7)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="3">-</button>
                                        <input type="text" class="quantity-input" data-id="3" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="3">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="3">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 4 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_3.jpg" class="product-img" alt="Wireless Charging Pad">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Wireless Charging Pad</h5>
                                <p class="product-desc">Fast charging pad compatible with all Qi-enabled devices.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.0)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="4">-</button>
                                        <input type="text" class="quantity-input" data-id="4" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="4">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="4">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 5 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_3.jpg" class="product-img"
                                alt="Stainless Steel Water Bottle">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Stainless Steel Water Bottle</h5>
                                <p class="product-desc">Keep your drinks hot or cold for hours with this insulated
                                    bottle.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★★ (4.8)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="5">-</button>
                                        <input type="text" class="quantity-input" data-id="5" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="5">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="5">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 6 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_3.jpg" class="product-img" alt="Ergonomic Keyboard">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Ergonomic Keyboard</h5>
                                <p class="product-desc">Comfortable split-design keyboard for long typing sessions.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.3)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="6">-</button>
                                        <input type="text" class="quantity-input" data-id="6" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="6">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="6">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 7 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_3.jpg" class="product-img" alt="4K Action Camera">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">4K Action Camera</h5>
                                <p class="product-desc">Capture your adventures in stunning 4K resolution with image
                                    stabilization.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★★ (4.9)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="7">-</button>
                                        <input type="text" class="quantity-input" data-id="7" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="7">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="7">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 8 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_3.jpg" class="product-img" alt="Smart LED Bulb">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Smart LED Bulb</h5>
                                <p class="product-desc">Control your lights with your phone and change colors with voice
                                    commands.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.1)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="8">-</button>
                                        <input type="text" class="quantity-input" data-id="8" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="8">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="8">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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


    <!-- Latest Page -->
    <div class="product-carousel-container latest">
        <h1 class="text-center mt-5 mb-4 heading-main">Latest Products</h1>
        <div class="container-fluid">
            <div class="swiper latestProductSwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_2.jpg" class="product-img" alt="Wireless Headphones">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Wireless Headphones</h5>
                                <p class="product-desc">High-quality wireless headphones with noise cancellation and
                                    20-hour battery life.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.5)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="1">-</button>
                                        <input type="text" class="quantity-input" data-id="1" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="1">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="1">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 2 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_2.jpg" class="product-img" alt="Smart Fitness Tracker">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Wireless Headphones</h5>
                                <p class="product-desc">High-quality wireless headphones with noise cancellation and
                                    20-hour battery life</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.2)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="2">-</button>
                                        <input type="text" class="quantity-input" data-id="2" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="2">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="2">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 3 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_2.jpg" class="product-img"
                                alt="Portable Bluetooth Speaker">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Portable Bluetooth Speaker</h5>
                                <p class="product-desc">High-quality wireless headphones with noise cancellation and
                                    20-hour battery life
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★★ (4.7)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="3">-</button>
                                        <input type="text" class="quantity-input" data-id="3" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="3">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="3">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 4 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_2.jpg" class="product-img" alt="Wireless Charging Pad">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Wireless Charging Pad</h5>
                                <p class="product-desc">Fast charging pad compatible with all Qi-enabled devices.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.0)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="4">-</button>
                                        <input type="text" class="quantity-input" data-id="4" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="4">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="4">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 5 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_2.jpg" class="product-img"
                                alt="Stainless Steel Water Bottle">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Stainless Steel Water Bottle</h5>
                                <p class="product-desc">Keep your drinks hot or cold for hours with this insulated
                                    bottle.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★★ (4.8)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="5">-</button>
                                        <input type="text" class="quantity-input" data-id="5" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="5">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="5">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 6 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_2.jpg" class="product-img" alt="Ergonomic Keyboard">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Ergonomic Keyboard</h5>
                                <p class="product-desc">Comfortable split-design keyboard for long typing sessions.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.3)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="6">-</button>
                                        <input type="text" class="quantity-input" data-id="6" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="6">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="6">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 7 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_2.jpg" class="product-img" alt="4K Action Camera">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">4K Action Camera</h5>
                                <p class="product-desc">Capture your adventures in stunning 4K resolution with image
                                    stabilization.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★★ (4.9)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="7">-</button>
                                        <input type="text" class="quantity-input" data-id="7" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="7">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="7">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 8 -->
                    <div class="swiper-slide">
                        <div class="product-card">
                            <img src="./Assets/Images/Category_2.jpg" class="product-img" alt="Smart LED Bulb">
                            <div class="product-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price">$59.99</div>
                                    <div class="pb-2"><small>SKU 00657237232</small></div>
                                </div>
                                <h5 class="product-title">Smart LED Bulb</h5>
                                <p class="product-desc">Control your lights with your phone and change colors with voice
                                    commands.</p>
                                <div class="box-container">
                                    <div class="box">1ft</div>
                                    <div class="box">2ft</div>
                                    <div class="box">3ft</div>
                                </div>
                                <div class="rating">★★★★☆ (4.1)</div>
                                <div class="quantity-control">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn minus" data-id="8">-</button>
                                        <input type="text" class="quantity-input" data-id="8" value="0" readonly>
                                        <button class="quantity-btn plus" data-id="8">+</button>
                                    </div>
                                    <button class="add-to-cart" data-id="8">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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


    <!-- Footer -->
   <?php
   include './Pages/Components/Footer.php'
   
   ?>

    <!-- Footer END -->



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


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

            /* second swiper */
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


            /* THIRD swiper*/
            const UniquProductsSwiper = new Swiper('.UniqueProductSwiper', {
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

            /* UNIQUE */
            // Quantity control functionality for Latest Products
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

            // Add to cart functionality for Latest Products
            document.querySelectorAll('.unique .add-to-cart').forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.getAttribute('data-id');
                    const input = document.querySelector(`.unique .quantity-input[data-id="${productId}"]`);
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


            // Initialize carousel
            var myCarousel = new bootstrap.Carousel(document.getElementById('categoryCarousel'), {
                interval: false,
                wrap: true,
                touch: true
            });

            // Handle window resize
            function handleResize() {
                const carousel = document.getElementById('categoryCarousel');
                if (window.innerWidth < 576) {
                    // Mobile - one item
                    carousel.querySelectorAll('.carousel-item .row > div').forEach(item => {
                        item.style.flex = '0 0 100%';
                        item.style.maxWidth = '100%';
                    });
                } else if (window.innerWidth < 992) {
                    // Tablet - two items
                    carousel.querySelectorAll('.carousel-item .row > div').forEach(item => {
                        item.style.flex = '0 0 50%';
                        item.style.maxWidth = '50%';
                    });
                } else {
                    // Desktop - three items
                    carousel.querySelectorAll('.carousel-item .row > div').forEach(item => {
                        item.style.flex = '0 0 33.33%';
                        item.style.maxWidth = '33.33%';
                    });
                }
            }

            // Initial call
            handleResize();

            // Listen for resize events
            window.addEventListener('resize', handleResize);
        });
    </script>




    

</body>

</html>