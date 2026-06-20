<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "config.php"; // Database connection
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $sql = "SELECT id, name FROM u241439453_info WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        $stmt->bind_result($id, $name);
        $stmt->fetch();

        // For now just show message
        // Later you can add email send with reset link
        $message = "✅ Password reset link has been sent to $email (simulation).";
    } else {
        $message = "❌ Email not found in our records.";
    }

    $stmt->close();
}
$conn->close();
?>

<!-- Forgot Password Form -->
<div class="forgot-form" style="max-width:400px;margin:auto;padding:20px;border:1px solid #ccc;border-radius:8px; text-align:center;">
  <h2>Forgot Password</h2>

  <?php if(isset($message)) echo "<p style='color:green;'>$message</p>"; ?>

  <form method="POST" action="">
    <input type="email" name="email" placeholder="Enter your registered Email" required style="width:100%;padding:10px;margin:10px 0;"><br>
    <button type="submit" style="padding:10px 20px;background-color:#3f498c;color:white;border:none;border-radius:5px;cursor:pointer;">Send Reset Link</button>
  </form>

  <p style="margin-top:10px;"><a href="login.html">Back to Login</a></p>
</div>

<style>
.forgot-form input:focus {
  border-color: #3f498c;
  box-shadow: 0 0 8px rgba(63,73,140,0.5);
  outline: none;
  transition: all 0.3s ease;
}

.forgot-form button:hover {
  background: #5a57c7;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(63,73,140,0.3);
  transition: all 0.3s ease;
}

.forgot-form a {
  color: #3f498c;
  text-decoration: none;
  transition: color 0.3s ease;
}

.forgot-form a:hover {
  color: #5a57c7;
  text-decoration: underline;
}
</style>
