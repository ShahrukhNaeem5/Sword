<?php
$base_url = '/Project'; // or use $_SERVER['HTTP_HOST'] for dynamic detection

?>


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
                <?php

                // Fetch menu items from database
                $menuQuery = "SELECT id, menu_name, menu_link FROM mid_menu ORDER BY id ASC";
                $menuResult = $conn->query($menuQuery);

                // Store menu items in array
                $menuItems = [];
                while ($item = $menuResult->fetch_assoc()) {
                    $menuItems[] = $item;
                }
                ?>

                <div class="user-actions order-3 order-lg-3">
                    <?php foreach ($menuItems as $item): ?>
                        <?php
                        // Determine which icon to use based on menu name
                        $iconClass = 'bi-question-circle'; // default icon
                    
                        // Map specific menu names to icons
                        if (
                            stripos($item['menu_name'], 'sign') !== false ||
                            stripos($item['menu_name'], 'login') !== false
                        ) {
                            $iconClass = 'bi-box-arrow-in-right';
                        } elseif (
                            stripos($item['menu_name'], 'cart') !== false ||
                            stripos($item['menu_name'], 'basket') !== false
                        ) {
                            $iconClass = 'bi-cart-check-fill';
                        }
                        // Add more mappings as needed
                        ?>

                        <a href="<?= htmlspecialchars($item['menu_link']) ?>">
                            <i class="bi <?= $iconClass ?>"></i>
                            <span class="d-none d-md-inline"><?= htmlspecialchars($item['menu_name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Include your database connection
    
    // Fetch main menus (parent_id = NULL or 0)
    $mainMenuQuery = "SELECT * FROM website_menus WHERE parent_id IS NULL OR parent_id = 0 ORDER BY id";
    $mainMenuResult = $conn->query($mainMenuQuery);

    // Store all menus in an array first
    $allMenus = [];
    while ($menu = $mainMenuResult->fetch_assoc()) {
        $allMenus[$menu['id']] = $menu;
    }

    // Fetch all submenus
    $subMenuQuery = "SELECT * FROM website_menus WHERE parent_id IS NOT NULL AND parent_id != 0 ORDER BY id";
    $subMenuResult = $conn->query($subMenuQuery);

    // Organize submenus under their parents
    while ($submenu = $subMenuResult->fetch_assoc()) {
        if (isset($allMenus[$submenu['parent_id']])) {
            if (!isset($allMenus[$submenu['parent_id']]['children'])) {
                $allMenus[$submenu['parent_id']]['children'] = [];
            }
            $allMenus[$submenu['parent_id']]['children'][] = $submenu;
        }
    }
    ?>

    <nav class="main-navigation navbar navbar-expand-lg">
        <div class="container">
            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mb-0 ps-0">


                    <!-- Dynamic Menu Items -->
                    <?php foreach ($allMenus as $menu): ?>
                        <?php if (empty($menu['children'])): ?>
                            <!-- Simple Menu Item (no dropdown) -->
                            <li class="nav-item">
                                <a href="<?= htmlspecialchars($menu['menu_link'] ?? '#') ?>" class="nav-link">
                                    <?= htmlspecialchars($menu['menu_name']) ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <!-- Dropdown Menu Item -->
                            <li class="nav-item dropdown">
                                <a href="<?= htmlspecialchars($menu['menu_link'] ?? '#') ?>" class="nav-link dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                    <?= htmlspecialchars($menu['menu_name']) ?>
                                    <i class="bi bi-chevron-down ms-1"></i>
                                </a>
                                <ul class="dropdown-menu bg-brown border-0 rounded-0 mt-0">
                                    <?php foreach ($menu['children'] as $index => $submenu): ?>
                                        <li>
                                            <a class="dropdown-item text-white"
                                                href="<?= htmlspecialchars($submenu['menu_link'] ?? '#') ?>">
                                                <?= htmlspecialchars($submenu['menu_name']) ?>
                                            </a>
                                        </li>
                                        <?php if ($index < count($menu['children']) - 1): ?>
                                            <li>
                                                <hr class="dropdown-divider my-0" style="border-color: rgba(255,255,255,0.2);">
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>


                </ul>
            </div>
        </div>
    </nav>
</header>