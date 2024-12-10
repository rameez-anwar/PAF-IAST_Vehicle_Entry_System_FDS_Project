<?php
$host = 'localhost'; // Database host
$dbname = 'vehicle_entry_system'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password (default for XAMPP is empty)

try {
    // Set the PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable exceptions for error handling
} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
