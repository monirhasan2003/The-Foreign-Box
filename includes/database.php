<?php
// Database Connection Credentials
// This file establishes the connection to the MySQL database.
// Store this file in a secure directory like 'config' or 'includes'.

// Define database parameters
define('DB_HOST', 'localhost');         // Your database host (usually 'localhost' for local setup)
define('DB_USER', 'root');             // Your database username (usually 'root' for XAMPP)
define('DB_PASS', '');                 // Your database password (usually empty for XAMPP)
define('DB_NAME', 'ecommerce_db');     // The name of the database you created

// Create a new MySQLi object to connect to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($conn->connect_error) {
    // If there is an error, stop the script and display the error message.
    // In a live production environment, you might want to log this error instead of displaying it.
    die("Connection Failed: " . $conn->connect_error);
}

// Set the character set to utf8mb4 to support a wide range of characters
$conn->set_charset("utf8mb4");

// Optional: You can uncomment the line below to confirm the connection is successful.
// echo "Database connected successfully!";

?>