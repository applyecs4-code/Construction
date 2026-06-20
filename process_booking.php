```php
<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

// Payment details
$payment_link = 'https://wise.com/pay/r/F8knXX80OdokihI';
$amount = '720';

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
$citb_test     = htmlspecialchars(trim($_POST['citb_test_completed'] ?? ''), ENT_QUOTES, 'UTF-8');
$selected_nvq  = htmlspecialchars(trim($_POST['selected_nvq'] ?? 'Passive Fire Protection (NVQ Level 2)'), ENT_QUOTES, 'UTF-8');
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
    die('Invalid email address.');
}

// USER EMAIL
$to_user = $email;
$subject_user = "Your NVQ Booking Confirmation – ConstructionsCert";

$message_user = "Dear {$first_name} {$last_name},

Thank you for booking your NVQ with ConstructionsCert.

Course:
{$selected_nvq}

CITB Test completed within 2 years:
{$citb_test}

To complete your booking, please make payment of £{$amount} using the secure Wise payment link below:

{$payment_link}

After successful payment, we will contact you with further instructions regarding your remote NVQ assessment.

Applicant Details

Name: {$first_name} {$last_name}
Email: {$email}
Phone: {$phone}
Address: {$address1}, {$address2}, {$city}, {$postcode}

Kind regards,

Construction Team
";

$headers_user  = "MIME-Version: 1.0\r\n";
$headers_user .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers_user .= "From: ConstructionsCert <info@constructionscert.co.uk>\r\n";
$headers_user .= "Reply-To: info@constructionscert.co.uk\r\n";

mail($to_user, $subject_user, $message_user, $headers_user);

// ADMIN EMAIL
$to_admin = "info@constructionscert.co.uk,kdm88268@gmail.com";

$subject_admin = "New NVQ Booking - {$first_name} {$last_name}";

$message_admin = "A new NVQ booking has been submitted.

Name: {$first_name} {$last_name}
Email: {$email}
Phone: {$phone}

Address:
{$address1}
{$address2}
{$city}
{$postcode}

Selected NVQ:
{$selected_nvq}

CITB Test Completed:
{$citb_test}

Marketing Opt Out:
{$opt_out}

Amount Due:
£{$amount}

Payment Link:
{$payment_link}";

$headers_admin  = "MIME-Version: 1.0\r\n";
$headers_admin .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers_admin .= "From: ConstructionsCert <info@constructionscert.co.uk>\r\n";
$headers_admin .= "Reply-To: {$email}\r\n";

mail($to_admin, $subject_admin, $message_admin, $headers_admin);

// Redirect user to Wise
header("Location: {$payment_link}");
exit;
?>
```
