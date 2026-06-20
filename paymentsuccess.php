<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (!isset($_SESSION['booking_data'])) {
    header("Location: form.html");
    exit;
}

$booking = $_SESSION['booking_data'];

// ===== SMTP =====
$smtpHost     = "smtp.hostinger.com";
$smtpUsername = "noreply@tumhardomain.com";
$smtpPassword = "YOUR_EMAIL_PASSWORD";
$smtpPort     = 465;

// ===== Send User Confirmation Email =====
try {
    $mailUser = new PHPMailer(true);
    $mailUser->isSMTP();
    $mailUser->Host       = $smtpHost;
    $mailUser->SMTPAuth   = true;
    $mailUser->Username   = $smtpUsername;
    $mailUser->Password   = $smtpPassword;
    $mailUser->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mailUser->Port       = $smtpPort;

    $mailUser->setFrom($smtpUsername, "CSCS Booking");
    $mailUser->addAddress($booking['email']);

    $mailUser->isHTML(true);
    $mailUser->Subject = "✅ Your CSCS Test Booking is Confirmed";
    $mailUser->Body = "
        <h2>Booking Confirmed!</h2>
        <p>Dear {$booking['first_name']},</p>
        <p>Your booking for <strong>{$booking['test_type']}</strong> at <strong>{$booking['test_centre']}</strong> on <strong>{$booking['test_date']}</strong> at <strong>{$booking['test_time']}</strong> has been confirmed.</p>
        <p>Payment: £39 received.</p>
        <p>Thank you for booking!</p>
    ";
    $mailUser->send();
} catch (Exception $e) {
    error_log("User email error: " . $mailUser->ErrorInfo);
}

echo "<h2>✅ Payment Successful & Booking Confirmed!</h2>";

session_destroy(); // Clear session
?>
    