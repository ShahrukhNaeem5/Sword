<?php
// Function to get footer data with menu grouping
function getFooterData($conn)
{
    $footer = [];

    // Get main footer content
    $result = $conn->query("SELECT * FROM footer LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $footer = $result->fetch_assoc();
    }

    // Get footer menus grouped by menu_group
    $footer['menus'] = [];
    $result = $conn->query("
        SELECT * FROM footer_menu 
        WHERE is_active = 1 
        ORDER BY menu_group, menu_title
    ");

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $group = $row['menu_group'] ?? 'Other';
            if (!isset($footer['menus'][$group])) {
                $footer['menus'][$group] = [];
            }
            $footer['menus'][$group][] = $row;
        }
    }

    return $footer;
}

$footerData = getFooterData($conn);
?>
<style>
    a{
        text-decoration: none;
    }
</style>
<footer class="pt-4 pb-4">
    <div class="container-fluid">
        <div class="row px-5">
            <?php
            // Display menu groups as columns
            $menuGroups = $footerData['menus'] ?? [];
            $displayedGroups = 0;

            foreach ($menuGroups as $groupName => $menus):
                // Skip if no menus in this group
                if (empty($menus))
                    continue;

                // Limit to 3 main menu groups
                if ($displayedGroups >= 3)
                    break;
                $displayedGroups++;
                ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <h5><?= htmlspecialchars($groupName) ?></h5>
                    <ul class="list-unstyled">
                        <?php foreach ($menus as $menu): ?>
                            <li>
                                <a href="<?= htmlspecialchars($menu['menu_link']) ?>" class="text-light">
                                    <?= htmlspecialchars($menu['menu_title']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>

            <!-- Column 4: Map -->
            <div class="col-md-3 col-sm-12 mb-4">
                <h5>Our Location</h5>
                <div style="width: 100%; height: 150px;">
                    <?php if (!empty($footerData['map_location'])): ?>
                        <iframe src="<?= htmlspecialchars($footerData['map_location']) ?>" width="100%" height="100%"
                            style="border:0;" allowfullscreen="" loading="lazy">
                        </iframe>
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center h-100">
                            <p class="text-muted">Map not configured</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Social Media Icons -->
                <div class="social-icons mt-3 d-flex justify-content-start">
                    <?php
                    $socialLinks = json_decode($footerData['footer_content'] ?? '{}', true);
                    $socialIcons = [
                        'facebook' => 'fa-brands fa-facebook',
                        'twitter' => 'fa-brands fa-twitter',
                        'whatsapp' => 'fa-brands fa-whatsapp',
                        'youtube' => 'fa-brands fa-youtube',
                        'instagram' => 'fa-brands fa-instagram'
                    ];

                    foreach ($socialIcons as $platform => $iconClass):
                        if (!empty($socialLinks[$platform])):
                            ?>
                            <a href="<?= htmlspecialchars($socialLinks[$platform]) ?>" class="me-3">
                                <i class="<?= $iconClass ?> fa-2x"></i>
                            </a>
                            <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>

        <div class="container-fluid footer-info">
            <?php
            // First, fetch contact information from the dedicated table
            $contactInfo = [];
            $result = $conn->query("SELECT * FROM contact_information LIMIT 1");
            if ($result && $result->num_rows > 0) {
                $contactInfo = $result->fetch_assoc();
            }
            ?>

            <div class="row px-5">
                <?php
                $contactItems = [
                    [
                        'icon' => 'fa fa-map-marker-alt',
                        'title' => 'Address',
                        'value' => $contactInfo['address'] ?? 'ABC Area no 19, UK',
                        'key' => 'address'
                    ],
                    [
                        'icon' => 'fa fa-phone-alt',
                        'title' => 'Phone',
                        'value' => $contactInfo['phone'] ?? '+1234567890',
                        'key' => 'phone'
                    ],
                    [
                        'icon' => 'fa-brands fa-whatsapp',
                        'title' => 'WhatsApp',
                        'value' => $contactInfo['whatsapp'] ?? '+123456755890',
                        'key' => 'whatsapp'
                    ],
                    [
                        'icon' => 'fa fa-envelope',
                        'title' => 'Email',
                        'value' => $contactInfo['email'] ?? 'example@domain.com',
                        'key' => 'email'
                    ]
                ];

                foreach ($contactItems as $item):
                    // Skip if value is empty and we're not in development
                    if (empty($item['value']))
                        continue;
                    ?>
                    <div class="col-12 col-md-3">
                        <div class="d-flex align-items-center">
                            <i class="<?= $item['icon'] ?> me-3"></i>
                            <div>
                                <h6><?= $item['title'] ?></h6>
                                <?php if ($item['key'] === 'email'): ?>
                                    <p><a href="mailto:<?= htmlspecialchars($item['value']) ?>"
                                            class="text-reset"><?= htmlspecialchars($item['value']) ?></a></p>
                                <?php elseif ($item['key'] === 'phone' || $item['key'] === 'whatsapp'): ?>
                                    <p><a href="tel:<?= preg_replace('/[^0-9+]/', '', $item['value']) ?>"
                                            class="text-reset"><?= htmlspecialchars($item['value']) ?></a></p>
                                <?php else: ?>
                                    <p><?= htmlspecialchars($item['value']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <p><?= htmlspecialchars($footerData['copyright_text'] ?? 'Your Company') ?></p>
        </div>
    </div>
</footer>