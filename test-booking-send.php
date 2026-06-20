<?php
// test-booking-send.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$to = 'info@constructionscert.co.uk';

// Fetch form fields
$name           = $_POST['name'] ?? '';
$email          = $_POST['email'] ?? '';
$phone          = $_POST['phone'] ?? '';
$testType       = $_POST['test_type'] ?? '';
$preferredDate  = $_POST['preferred_date'] ?? '';
$formName       = $_POST['form_name'] ?? 'CSCS Test Booking';

// Validation
if (trim($name) === '' || trim($email) === '' || trim($phone) === '' || trim($testType) === '') {
    http_response_code(400);
    echo 'Missing required fields.';
    exit;
}

// Email subject
$emailSubject = "New CSCS Test Booking - applycscs UK";

// Email message
$body  = "New CSCS Test Booking Request:\r\n\r\n";
$body .= "Name: $name\r\n";
$body .= "Email: $email\r\n";
$body .= "Phone: $phone\r\n";
$body .= "Test Type: $testType\r\n";
$body .= "Preferred Date: " . ($preferredDate ?: 'Not specified') . "\r\n";
$body .= "Form Name: $formName\r\n\r\n";
$body .= "-----\r\nSent from the CSCS Test Booking form.\r\n";

// Headers
$fromEmail = 'info@constructionscert.co.uk';
$headers  = "From: applycscs UK <{$fromEmail}>\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send email
if (mail($to, $emailSubject, $body, $headers)) {
    http_response_code(200);
    echo 'OK';
} else {
    http_response_code(500);
    echo 'Error sending email.';
}
