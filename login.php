<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    // Prepared statement
    $sql = "SELECT id, name, password FROM u241439453_info WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        if (password_verify($pass, $hashed_password)) {
            // Login success
            $_SESSION['userid'] = $id;
            $_SESSION['username'] = $name;
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<p style='color:red;'>❌ Invalid email or password.</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Email not found.</p>";
    }

    $stmt->close();
}
$conn->close();
?>