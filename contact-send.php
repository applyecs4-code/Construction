<?php
// contact-send.php

// Debug ke liye (test time pe rakho, baad me hata sakte ho)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// AJAX / normal dono ke liye simple text response
header('Content-Type: text/plain; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

// 👉 YAHAN APNI EMAIL DAALO (JIS INBOX MEIN MESSAGE AAYEGA)
$to = 'info@constructionscert.co.uk';   // tumhari main inbox email

// Form values safely get karo
$name    = isset($_POST['name'])    ? trim($_POST['name'])    : '';
$email   = isset($_POST['email'])   ? trim($_POST['email'])   : '';
$phone   = isset($_POST['phone'])   ? trim($_POST['phone'])   : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : 'Contact Form Enquiry';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Basic validation
if ($name === '' || $email === '' || $message === '') {
    http_response_code(400);
    echo 'Missing required fields.';
    exit;
}

// Email subject (jo tumko milega)
$emailSubject = "New Contact Form Submission - applycscs UK";

// Email body
$body  = "You have received a new contact form message:\r\n\r\n";
$body .= "Name: " . $name . "\r\n";
$body .= "Email: " . $email . "\r\n";
$body .= "Phone: " . $phone . "\r\n";
$body .= "Subject: " . $subject . "\r\n\r\n";
$body .= "Message:\r\n" . $message . "\r\n\r\n";
$body .= "-----\r\nSent from aplycscs UK contact page.\r\n";

// ✅ From email — yahi domain ka REAL/EXISTING mailbox hona chahiye
$fromEmail = 'info@constructionscert.co.uk';

// Headers
$headers  = "From: applycscs UK <" . $fromEmail . ">\r\n";
$headers .= "Reply-To: " . $email . "\r\n"; // user ka email yahan
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// ✅ Pehle bina "-f" test karein (kai host pe isse error aata hai)
$sent = mail($to, $emailSubject, $body, $headers);

if ($sent) {
    http_response_code(200);
    echo 'OK';
} else {
    http_response_code(500);
    echo 'Error sending email.';
}
