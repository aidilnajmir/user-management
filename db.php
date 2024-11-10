<?php
// Database connection settings
$host = 'localhost'; // Database host
$db   = 'user_db';   // Database name
$user = 'root';      // Database username
$pass = '';          // Database password

try {
    // Create a new PDO instance for database connection
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exception
} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
}
?>
