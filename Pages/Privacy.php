<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Learn about our company, mission, and values.">
    <meta name="keywords" content="about us, company, mission, values">
    <meta name="author" content="Your Company Name">
    <title>Privacy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/Css/style.css">
    <style>
        .privacy h1,
        .privacy h3,
        .privacy h2,
        .privacy h4,
        .privacy h6,
        .privacy h5 {
            color: #6b3a0f;

        }

        .privacy h6 {
            margin-top: 35px;
        }


        .privacy p,
        .privacy ul li {
            font-size: 15px;
        }

        .breadcrumb-item a {
            color: #6b3a0f;
        }
    </style>
</head>

<body>
    <?php
    // Verify PHP includes
    include '../Config/connection.php';
    include './Components/Header.php';
    ?>


    <div class="container privacy pb-5">
        <div class="container mt-1 py-2">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 mb-md-0">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
                </ol>
            </nav>

        </div>
        <h2 class="text-center fw-bolder py-4">Privacy Policy</h2>
        
       <?php
        include './Components/Privacy_component.php';
       ?>
    </div>





    <?php
    include './Components/Footer.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>