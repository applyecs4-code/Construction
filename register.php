<?php
include "config.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // User inputs
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    // Password hash
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    // Prepared statement for secure insertion
    $sql = "INSERT INTO u241439453_info (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Registration successful! <a href='login.html'>Login here</a></p>"; 
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>
