<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Learn about our company, mission, and values at My eCommerce Store.">
    <meta name="keywords" content="about us, company, mission, values, ecommerce">
    <meta name="author" content="My eCommerce Store">
    <title>About Us - My eCommerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/Css/style.css">
    <style>
        .About-page h1 {
            color: #603b29;
            font-weight: 500;
        }

        .about-img {
            width: 100%;
            height: auto;
            object-fit: cover;
            max-height: 500px;
        }

        .about-text {
            display: flex;
            color: #603b29;
            flex-direction: column;
            text-align: left;
        }

        .about-section img {
            height: 300px;
            width: 100%;
            object-fit: contain;
        }

        @media (max-width: 768px) {
            .about-text {
                padding: 1rem;
            }
        }

        .breadcrumb-item a {
            color: #6b3a0f;
        }
    </style>
</head>

<body>
    <?php
    include '../Config/connection.php';
    include './Components/Header.php';

    // Set UTF-8 encoding for the database connection
    $conn->set_charset('utf8mb4');

    // Fetch about sections from the database using MySQLi
    $query = "SELECT title, description, image_path, position FROM about_sections ORDER BY position ASC";
    $result = $conn->query($query);
    $sections = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $sections[] = $row;
        }
        $result->free();
    } else {
        echo '<div class="alert alert-danger text-center">Failed to load content. Please try again later.</div>';
    }
    ?>

    <div class="About-page container-fluid py-3 px-5">
        <div class="container mt-1 py-2">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 mb-md-0">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">About Page</li>
                </ol>
            </nav>
        </div>
        <h1 class="text-center py-3 mb-3 fw-bolder">About Us</h1>

        <?php
        $index = 0;
        $allowedTags = '<h2><strong><em><i><a><br><p><ul><ol><li>';
        foreach ($sections as $section) {
            $orderClass = ($index % 2 == 0) ? 'order-lg-1' : 'order-lg-2';
            $textOrderClass = ($index % 2 == 0) ? 'order-lg-2' : 'order-lg-1';
            ?>
            <div class="row about-section <?php echo $index > 0 ? 'mt-5' : ''; ?>">
                <div class="col-12 col-lg-6 <?php echo $orderClass; ?>">
                    <img src="../Assets/uploads/about_section/<?php echo htmlspecialchars($section['image_path']); ?>"
                        alt="<?php echo htmlspecialchars(strip_tags($section['title'])); ?>" class="about-img"
                        loading="lazy">
                </div>
                <div class="col-12 col-lg-6 <?php echo $textOrderClass; ?> about-text">
                    <div><?php echo strip_tags($section['title'], $allowedTags); ?></div>
                    <div>
                        <?php
                        // Remove slashes and convert newlines to <br> for plain text, then allow specific tags
                        $description = nl2br(stripslashes($section['description']));
                        echo strip_tags($description, $allowedTags);
                        ?>
                    </div>
                </div>
            </div>
            <?php
            $index++;
        }
        ?>
    </div>

    <?php
    include './Components/Footer.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>