<?php
include '../Config/connection.php';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories-Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="../Assets/Css/style.css">
    <style>
        .category-banner .banner-overlay h1 {
            color: rgb(252, 248, 247) !important;
            text-shadow: 2px 2px 2px burlywood;


        }

        .sub-head {
            display: flex;
        }

        .sub-head span {
            font-size: 32px;
            text-align: center;
            /* text-shadow: 2px 2px 2px burlywood; */
            color: #603b29ff !important;
            margin-bottom: 20px;
        }


        .breadcrumb li a {
            color: #603b29ff !important;

        }

        @media screen and (min-width:900px) {
            .sub-head span {
                margin-left: -150px;

            }
        }

        .breadcrumb-item a {
            color: #6b3a0f;
        }
    </style>
</head>

<body>
    <?php
    include './Components/Header.php';
    ?>


    <div class="container-fluid p-0 m-0">

        <div class="category-banner position-relative text-center mb-5">
            <img src="../Assets/Images/categories-banner.jpg" class="img-fluid w-100" alt="Category Banner"
                style="max-height: 400px; object-fit: cover;">
            <div class="banner-overlay position-absolute top-50 start-50 translate-middle">
                <h1 class="text-white display-4 fw-bold">Explore Our Categories</h1>
            </div>
        </div>
    </div>
    <div class="container">

        <div class="sub-head d-flex flex-column flex-md-column flex-lg-row align-items-center">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 mb-md-0">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Category Page</li>
                </ol>
            </nav>
            <div class="flex-grow-1 text-center">
                <span class="text-white display-4 fw-bold">Explore Our Categories</span>
            </div>
        </div>


    </div>
    <?php
    $show_heading = false;
    include './Components/Category_Page_Component.php';
    ?>



    <!-- CUSTOM CATEGORY-1 -->

    <!-- CUSTOM CATEGORY-1 END-->


    <!-- Footer -->
    <?php

    include './Components/Footer.php';

    ?>

    <!-- Footer END -->



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>









</body>

</html>