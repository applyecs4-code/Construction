<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

// Payment details
$payment_link = 'https://www.paypal.com/ncp/payment/HSCDGHFW3BV3A';
$amount = '79';

// Collect and sanitize form data
$first_name    = htmlspecialchars(trim($_POST['first_name'] ?? ''), ENT_QUOTES, 'UTF-8');
$last_name     = htmlspecialchars(trim($_POST['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
$email         = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$confirm_email = filter_var(trim($_POST['confirm_email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone         = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
$address1      = htmlspecialchars(trim($_POST['address_line_1'] ?? ''), ENT_QUOTES, 'UTF-8');
$address2      = htmlspecialchars(trim($_POST['address_line_2'] ?? ''), ENT_QUOTES, 'UTF-8');
$city          = htmlspecialchars(trim($_POST['city'] ?? ''), ENT_QUOTES, 'UTF-8');
$postcode      = htmlspecialchars(trim($_POST['postcode'] ?? ''), ENT_QUOTES, 'UTF-8');
$citb_test     = $_POST['citb_test_completed'] ?? '';
$selected_nvq  = $_POST['selected_nvq'] ?? 'CSCS Green Card Level 1 (Award in Health & Safety)';
$opt_out       = isset($_POST['opt_out']) ? 'Yes' : 'No';

// Validation
if (
    empty($first_name) ||
    empty($last_name) ||
    empty($email) ||
    empty($phone) ||
    empty($address1) ||
    empty($city) ||
    empty($postcode) ||
    empty($citb_test)
) {
    die('Required fields missing.');
}

if ($email !== $confirm_email) {
    die('Email addresses do not match.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email format.');
}

/*
|--------------------------------------------------------------------------
| USER EMAIL
|--------------------------------------------------------------------------
*/

$to_user = $email;

$subject_user = "Your CSCS Green Card Booking Confirmation – ConstructionsCert";

$message_user = "
Dear {$first_name} {$last_name},

Thank you for your enquiry.

We have successfully received your details. Our team will review your information and contact you shortly regarding the next steps.

Kind regards,

Construction Team
";

$headers_user  = "MIME-Version: 1.0\r\n";
$headers_user .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers_user .= "From: ConstructionsCert <info@constructionscert.co.uk>\r\n";
$headers_user .= "Reply-To: info@constructionscert.co.uk\r\n";

$user_mail_sent = mail(
    $to_user,
    $subject_user,
    $message_user,
    $headers_user
);

/*
|--------------------------------------------------------------------------
| ADMIN EMAIL
|--------------------------------------------------------------------------
*/

$subject_admin = "New CSCS Green Card Booking - {$first_name} {$last_name}";

$message_admin = "
NEW BOOKING RECEIVED

Name: {$first_name} {$last_name}
Email: {$email}
Phone: {$phone}

Address Line 1: {$address1}
Address Line 2: {$address2}
City: {$city}
Postcode: {$postcode}

Course: {$selected_nvq}
CITB Test Completed: {$citb_test}
Marketing Opt Out: {$opt_out}
";

$headers_admin  = "MIME-Version: 1.0\r\n";
$headers_admin .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers_admin .= "From: ConstructionsCert <info@constructionscert.co.uk>\r\n";
$headers_admin .= "Reply-To: {$email}\r\n";

/*
|--------------------------------------------------------------------------
| SEND ADMIN EMAILS SEPARATELY
|--------------------------------------------------------------------------
*/

$admin1 = mail(
    'info@constructionscert.co.uk',
    $subject_admin,
    $message_admin,
    $headers_admin
);

$admin2 = mail(
    'kdm88268@gmail.com',
    $subject_admin,
    $message_admin,
    $headers_admin
);

/*
|--------------------------------------------------------------------------
| LOG RESULTS
|--------------------------------------------------------------------------
*/

error_log("User Mail: " . ($user_mail_sent ? 'SUCCESS' : 'FAILED'));
error_log("Admin Mail 1: " . ($admin1 ? 'SUCCESS' : 'FAILED'));
error_log("Admin Mail 2: " . ($admin2 ? 'SUCCESS' : 'FAILED'));

/*
|--------------------------------------------------------------------------
| REDIRECT TO PAYMENT
|--------------------------------------------------------------------------
*/

header('Location: ' . $payment_link);
exit;