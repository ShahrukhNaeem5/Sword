<?php
define("BASE_URL", "/Project/Dashboard/"); // LOCAL EXAMPLE

// Database configuration
define('DB_HOST', 'localhost'); // Database host (usually 'localhost')
define('DB_USER', 'root');      // Database username
define('DB_PASS', '');          // Database password
define('DB_NAME', 'cd_cyberhoster'); // Your database name

// Error reporting (only for development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for proper Unicode support
$conn->set_charset("utf8mb4");

// Function to safely close the database connection
function closeDatabaseConnection() {
    global $conn;
    if (isset($conn)) {
        $conn->close();
    }
}

// Register shutdown function to close connection when script ends
register_shutdown_function('closeDatabaseConnection');


?>