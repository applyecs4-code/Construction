<?php
// Show all errors (sirf debugging ke liye)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials (Hostinger panel se exact check karein)
$servername = "localhost";
$username   = "u241439453_info"; 
$password   = "Appycscs@2026"; 
$dbname     = "u241439453_login"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_errno) {
    die("❌ Database Connection failed: (" . $conn->connect_errno . ") " . $conn->connect_error);
} else {
    // Debug ke liye
    // echo "✅ Database connected successfully";
}
?>
