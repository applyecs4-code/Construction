<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

/* =====================
   CLEAN FUNCTION
===================== */
function clean($data) {
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}

/* =====================
   TERMS CHECK
===================== */
if (!isset($_POST['terms'])) {
    die('You must accept the terms and conditions.');
}

/* =====================
   FORM DATA
===================== */
$data = [
    'cardType'         => clean($_POST['cardType'] ?? ''),
    'citb_test'        => clean($_POST['citb_test'] ?? ''),
    'degree'           => clean($_POST['degree'] ?? ''),
    'title'            => clean($_POST['title'] ?? ''),
    'first_name'       => clean($_POST['first_name'] ?? ''),
    'surname'          => clean($_POST['surname'] ?? ''),
    'email'            => clean($_POST['email'] ?? ''),
    'mobile'           => clean($_POST['mobile'] ?? ''),
    'dob'              => clean($_POST['dob'] ?? ''),
    'street_address'   => clean($_POST['street_address'] ?? ''),
    'city'             => clean($_POST['city'] ?? ''),
    'county'           => clean($_POST['county'] ?? ''),
    'country'          => clean($_POST['country'] ?? ''),
    'address_postcode' => clean($_POST['address_postcode'] ?? ''),
    'ni_number'        => clean($_POST['ni_number'] ?? ''),
    'noNI'             => isset($_POST['noNI']) ? 'Yes' : 'No',
    'job_title'        => clean($_POST['job_title'] ?? ''),
    'utr_number'       => clean($_POST['utr_number'] ?? ''),
    'noRevision'       => isset($_POST['noRevision']) ? 'Yes' : 'No',
];

/* =====================
   BASIC VALIDATION
===================== */
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address");
}

/* =====================
   SEND MAIL FUNCTION (NO SMTP – MILESWEB SAFE)
===================== */
function sendMail($to, $toName, $subject, $body, $replyTo = null) {

    $mail = new PHPMailer(true);

    try {
        // ❌ NO SMTP – use PHP mail()
        $mail->setFrom('info@constructionscert.co.uk', 'CSCS Card Application');
        $mail->addAddress($to, $toName);

        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $body;

        return $mail->send();

    } catch (Exception $e) {
        error_log("MAIL ERROR: " . $mail->ErrorInfo);
        return false;
    }
}

/* =====================
   EMAIL CONTENT
===================== */
$adminBody = "
<h2>New CSCS Card Application</h2>

<h3>Personal Details</h3>
<p><b>Name:</b> {$data['title']} {$data['first_name']} {$data['surname']}</p>
<p><b>Email:</b> {$data['email']}</p>
<p><b>Mobile:</b> {$data['mobile']}</p>
<p><b>DOB:</b> {$data['dob']}</p>

<h3>Address</h3>
<p>{$data['street_address']}, {$data['city']}, {$data['county']}, {$data['country']} - {$data['address_postcode']}</p>

<h3>CSCS Details</h3>
<p><b>Card Type:</b> {$data['cardType']}</p>
<p><b>CITB Test:</b> {$data['citb_test']}</p>
<p><b>Qualification:</b> {$data['degree']}</p>
<p><b>Job Title:</b> {$data['job_title']}</p>
<p><b>UTR:</b> {$data['utr_number']}</p>

<h3>Other Info</h3>
<p><b>NI Number:</b> " . ($data['ni_number'] ?: 'Not Provided') . "</p>
<p><b>No NI:</b> {$data['noNI']}</p>
<p><b>Revision Required:</b> " . ($data['noRevision'] === 'Yes' ? 'No' : 'Yes') . "</p>

<hr>
<p><b>Amount:</b> £49.00</p>
";

$userBody = "
<h3>Thank you {$data['first_name']} ✅</h3>
<p>Your CSCS Card application has been received successfully.</p>
<p>We will contact you shortly.</p>
";

/* =====================
   SEND EMAILS
===================== */
$adminSent = sendMail(
    'info@constructionscert.co.uk',
    'Admin',
    'New CSCS Card Application',
    $adminBody,
    $data['email']
);

$userSent = sendMail(
    $data['email'],
    $data['first_name'],
    'Your CSCS Card Application Confirmation',
    $userBody
);

if (!$adminSent || !$userSent) {
    die("Email sending failed. Please try again.");
}

/* =====================
   SAVE + REDIRECT
===================== */
$_SESSION['cscs_application'] = $data;
header("Location: cardpayment.php");
exit;
