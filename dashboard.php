<?php
session_start();

// Agar user login nahi hai to login page par bhej do
if (!isset($_SESSION['userid']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Agar login hai to welcome message show karo
$username = htmlspecialchars($_SESSION['username']); // XSS से बचाव
echo "👋 Welcome, " . $username;
echo "<br><a href='logout.php'>Logout</a>";
?>
