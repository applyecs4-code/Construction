```php
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
function clean($v) {
    return htmlspecialchars(trim($v ?? ''), ENT_QUOTES, 'UTF-8');
}

/* =====================
   FORM DATA
===================== */
$data = [
    'firstName'        => clean($_POST['firstName'] ?? ''),
    'surname'          => clean($_POST['surname'] ?? ''),
    'dob'              => clean($_POST['dob'] ?? ''),
    'email'            => clean($_POST['email'] ?? ''),
    'confirmEmail'     => clean($_POST['confirmEmail'] ?? ''),
    'mobile'           => clean($_POST['mobile'] ?? ''),
    'testType'         => clean($_POST['testType'] ?? ''),
    'testCentre'       => clean($_POST['testCentre'] ?? ''),
    'testDate'         => clean($_POST['testDate'] ?? ''),
    'testTime'         => clean($_POST['testTime'] ?? ''),
    'streetAddress'    => clean($_POST['streetAddress'] ?? ''),
    'city'             => clean($_POST['city'] ?? ''),
    'country'          => clean($_POST['country'] ?? ''),
    'postcodeAddress'  => clean($_POST['postcodeAddress'] ?? ''),
    'testLanguage'     => clean($_POST['testLanguage'] ?? ''),
    'testCategory'     => clean($_POST['testCategory'] ?? ''),
    'cscs_card_type'   => clean($_POST['cscs_card_type'] ?? ''),
];

/* =====================
   VALIDATION
===================== */
$required = [
    'firstName',
    'surname',
    'dob',
    'email',
    'confirmEmail',
    'mobile',
    'testType',
    'testCentre',
    'testDate',
    'testTime',
    'streetAddress',
    'city',
    'country',
    'postcodeAddress'
];

foreach ($required as $r) {
    if (empty($data[$r])) {
        die("Missing required field: $r");
    }
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    die("Invalid email");
}

if ($data['email'] !== $data['confirmEmail']) {
    die("Email mismatch");
}

/* =====================
   SEND MAIL FUNCTION
===================== */
function sendMail($to, $toName, $subject, $body, $replyEmail = null) {

    $mail = new PHPMailer(true);

    try {

        $mail->setFrom('info@constructionscert.co.uk', 'CSCS Booking');
        $mail->addAddress($to, $toName);

        if ($replyEmail) {
            $mail->addReplyTo($replyEmail);
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

$cardText = ($data['cscs_card_type'] === 'renewal')
    ? 'Renewal CSCS Card'
    : 'New CSCS Card';

$adminBody = "
<h2>New CSCS Booking</h2>

<p><b>Name:</b> {$data['firstName']} {$data['surname']}</p>
<p><b>Date of Birth:</b> {$data['dob']}</p>
<p><b>Email:</b> {$data['email']}</p>
<p><b>Mobile:</b> {$data['mobile']}</p>

<p><b>Test:</b> {$data['testType']}</p>
<p><b>Centre:</b> {$data['testCentre']}</p>
<p><b>Date:</b> {$data['testDate']} {$data['testTime']}</p>

<p><b>Language:</b> {$data['testLanguage']}</p>
<p><b>Package:</b> {$data['testCategory']}</p>

<p><b>Address:</b>
{$data['streetAddress']},
{$data['city']},
{$data['country']}
{$data['postcodeAddress']}
</p>

<p><b>CSCS Card:</b> {$cardText}</p>
";

$userBody = "
<h3>Thank You {$data['firstName']} ✅</h3>

<p>Your CSCS booking request has been received.</p>

<p><b>Test:</b> {$data['testType']}</p>
<p><b>Centre:</b> {$data['testCentre']}</p>

<p>We will contact you shortly.</p>
";

/* =====================
   SEND EMAILS
===================== */

$adminSent = sendMail(
    'info@constructionscert.co.uk',
    'CSCS Admin',
    'New CSCS Test Booking',
    $adminBody,
    $data['email']
);

$userSent = sendMail(
    $data['email'],
    $data['firstName'],
    'Your CSCS Booking Confirmation',
    $userBody
);

if (!$adminSent || !$userSent) {
    die("Email sending failed. Please try again.");
}

/* =====================
   SAVE + REDIRECT
===================== */

$_SESSION['cscs_booking'] = $data;

header("Location: https://buy.stripe.com/9B600jgpK9Xd4tq3Se0Ny01");
exit;
?>
```
