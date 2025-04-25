<?php
session_start();
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

    <style>
        .product-body {
            display: flex;
            flex-direction: column;
            min-height: 250px;
            /* Ensure consistent height for alignment */
        }

        .quantity-control {
            margin-top: auto;
            /* Push to bottom of card */
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <?php
    include './Pages/Components/Header.php';
    ?>

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
    include './Pages/Components/Parent_category.php';
    include './Pages/Components/Categories.php';
        ?>
    <!-- CUSTOM CATEGORY-1 END-->




    <!-- Featured Page -->
    <?php
    include './Pages/Components/Product_featured.php'

        ?>




    <!-- Unique Pprduct -->






    <!-- Footer -->
    <?php
    include './Pages/Components/Product_unique.php';
    include './Pages/Components/Product_latest.php';
    include './Pages/Components/Footer.php';

    ?>

    <!-- Footer END -->



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {



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



        });
    </script>






</body>

</html>