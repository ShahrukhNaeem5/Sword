<?php
// Start session for messages
session_start();

// Include database connection
include_once '../Config/connection.php';

// Initialize variables
$feedback = [
    'fullname' => '',
    'phone' => '',
    'email' => '',
    'msg' => ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    $fullname = $_POST['fullname'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $msg = $_POST['msg'] ?? '';

    // Basic validation
    if (empty($fullname) || empty($msg)) {
        $_SESSION['error'] = "Full name and message are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (fullname, phone, email, msg) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullname, $phone, $email, $msg);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Thank you for your feedback! It has been submitted successfully.";
            $feedback = [
                'fullname' => '',
                'phone' => '',
                'email' => '',
                'msg' => ''
            ];
        } else {
            $_SESSION['error'] = "Error submitting feedback: " . $stmt->error;
        }

        $stmt->close();
    }

    header("Location: Contact.php#contactForm");
    exit();
}

// Fetch contact information
$contact_query = "SELECT phone, whatsapp, email, address, work_days, work_hours FROM contact_information LIMIT 1";
$contact_result = $conn->query($contact_query);
$contact_info = $contact_result->num_rows > 0 ? $contact_result->fetch_assoc() : [
    'phone' => '03110281772',
    'whatsapp' => '03110281772',
    'email' => 'shahrukh@gmail.com',
    'address' => 'R-22 Block Street No 2 London UK, Near and Westminster Bridge',
    'work_days' => 'Monday - Saturday',
    'work_hours' => '9:00 - 9:00'
];

// Fetch map location from footer
$footer_query = "SELECT map_location FROM footer LIMIT 1";
$footer_result = $conn->query($footer_query);
$map_location = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3609.4042450756847!2d55.15473057270105!3d25.22330598136635!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f4094ade2dca3%3A0xef80338ddb88cf7f!2sUnited%20Kingdom!5e0!3m2!1sen!2s!4v1744136805222!5m2!1sen!2s';

if ($footer_result && $footer_result->num_rows > 0) {
    $row = $footer_result->fetch_assoc();
    if (!empty($row['map_location'])) {
        $map_location = trim($row['map_location']);
    }
}?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Assets/Css/style.css">
    <title>Contact Us</title>
    <style>
        .contact-page .map-container {
            position: relative;
            width: 100%;
            height: 550px;
            margin-top: 40px;
            color: #603b29;
        }

        .contact-page .map-wrapper {
            margin-top: 120px;
            height: 480px;
            width: 100%;
        }

        .contact-page .overlay-container {
            position: absolute;
            width: 100%;
            background-color: #5d360f21;
            top: -120px;
            height: 120px;
            display: flex;
            justify-content: space-evenly;
            z-index: 10;
        }

        .contact-page .overlay-div {
            text-align: left;
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid #ccc;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            font-size: 14px;
            width: 240px;
            transform: translateY(20px);
        }

        .contact-page .overlay-div p {
            font-size: 14px;
            font-weight: 500;
        }

        .contact-page .main-heading {
            color: #603b29;
            font-weight: 500;
            margin-top: 20px;
        }

        .contact-page .Feedback {
            color: #603b29;
            font-weight: 500;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .contact-page .overlay-container {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: space-evenly;
                align-items: flex-start;
                top: -150px;
                height: auto;
                padding: 10px 0;
            }

            .contact-page .overlay-div {
                width: 45%;
                height: 120px;
                padding: 10px;
                margin-bottom: 10px;
                transform: none;
            }

            .contact-page .map-wrapper {
                margin-top: 200px;
                height: 400px;
            }
        }

        .contact-page .contact-form input,
        .contact-page .contact-form textarea {
            background-color: #5d360f21;
        }

        .contact-page .contact-form input::placeholder,
        .contact-page .contact-form textarea::placeholder {
            color: #603b29;
        }

        .contact-page .contact-form .btn {
            background-color: #603b29;
            color: white;
            padding: 5px 16px;
        }

        .breadcrumb-item a {
            color: #6b3a0f;
        }

        .feedback-icon {
            font-size: 1.2rem;
            margin-right: 8px;
            color: #603b29;
        }

        
    </style>
</head>

<body>
    <?php include './Components/Header.php'; ?>

    <div class="contact-page container-fluid p-0">
        <div class="container mt-1 py-2">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 mb-md-0">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact Page</li>
                </ol>
            </nav>
        </div>

        <h1 class="text-center py-2 main-heading fw-bolder">Contact Us</h1>
        <div class="map-container">
            <div class="overlay-container">
                <div class="overlay-div">
                    <h6>Phone</h6>
                    <p><i class="bi bi-telephone-fill me-2"></i><?= htmlspecialchars($contact_info['phone']) ?></p>
                    <p><i class="bi bi-whatsapp me-2"></i><?= htmlspecialchars($contact_info['whatsapp']) ?></p>
                </div>
                <div class="overlay-div">
                    <h6>Address</h6>
                    <p class="d-flex align-items-start">
                        <i class="bi bi-geo-alt-fill me-2 mt-1"></i>
                        <span><?= htmlspecialchars($contact_info['address']) ?></span>
                    </p>
                </div>
                <div class="overlay-div">
                    <h6>Work Hours</h6>
                    <p><i class="bi bi-clock-fill me-2"></i><?= htmlspecialchars($contact_info['work_days']) ?></p>
                    <p class="ms-4 p-0"><?= htmlspecialchars($contact_info['work_hours']) ?></p>
                </div>
                <div class="overlay-div">
                    <h6>Email</h6>
                    <p><i class="bi bi-envelope-fill me-2"></i><?= htmlspecialchars($contact_info['email']) ?></p>
                </div>
            </div>

            <div class="map-wrapper">
                <iframe src="<?= htmlspecialchars($map_location) ?>" width="100%" height="480" style="border:0;"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

            
        </div>

        <div class="contact-form" id="contactForm">
            <div class="container">
                <h1 class="text-center Feedback mb-5">Give Us Your Feedback</h1>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="" method="post" id="feedbackForm">
                    <div class="row">
                        <div class="col-12 col-md-6 d-flex flex-column">
                            <input class="form-control mb-3 py-3" type="text" id="fullname" name="fullname"
                                value="<?= htmlspecialchars($feedback['fullname']) ?>" placeholder="Enter your full name" required>
                            <input class="form-control mb-3 py-3" type="text" id="phone" name="phone"
                                value="<?= htmlspecialchars($feedback['phone']) ?>" placeholder="Enter phone number">
                            <input class="form-control py-3" type="email" id="email" name="email"
                                value="<?= htmlspecialchars($feedback['email']) ?>" placeholder="Enter email address">
                        </div>
                        <div class="col-12 col-md-6">
                            <textarea class="form-control" id="msg" name="msg" placeholder="Enter your feedback" cols="22" rows="8" required><?= htmlspecialchars($feedback['msg']) ?></textarea>
                        </div>
                        <div class="col-12 text-center my-5">
                            <button class="btn px-4" type="submit" name="submit_feedback"><i class="bi bi-send"></i> Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include './Components/Footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('feedbackForm').addEventListener('submit', function (e) {
            let isValid = true;
            const email = document.getElementById('email');
            const fullname = document.getElementById('fullname');
            const msg = document.getElementById('msg');

            if (fullname.value.trim() === '') {
                alert('Full name is required');
                isValid = false;
            }

            if (msg.value.trim() === '') {
                alert('Message is required');
                isValid = false;
            }

            if (email.value.trim() !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                alert('Please enter a valid email address');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>