<?php
/**
 * E-Learning Course Checkout - Form Handler
 * 
 * This script processes the form submission, sends email to admin,
 * sends confirmation email to user, and redirects to Stripe payment page.
 */

// ============================================================
// ERROR REPORTING (FOR DEBUGGING - REMOVE IN PRODUCTION)
// ============================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============================================================
// CONFIGURATION
// ============================================================

// Admin email where form data will be sent
$admin_email = 'info@constructionscert.co.uk';

// Stripe payment link (redirect after successful submission)
$payment_link = 'https://buy.stripe.com/9B6cMX44v5Dv1LZ9MFeUU01';

// Email subjects
$admin_subject = 'New E-Learning Course Enquiry - Constructions Cert';
$user_subject = 'Your E-Learning Course Enquiry - Constructions Cert';

// Site URL
$site_url = 'https://constructionscert.co.uk';

// ============================================================
// PROCESS FORM DATA
// ============================================================

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ============================================================
    // COLLECT AND SANITIZE FORM DATA
    // ============================================================
    
    // Helper function to sanitize input
    function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    // Collect all form fields with proper array handling
    $params = isset($_POST['params']) ? $_POST['params'] : [];
    
    $form_data = [
        'first_name'        => isset($params['First Name']) ? sanitize($params['First Name']) : '',
        'last_name'         => isset($params['Last Name']) ? sanitize($params['Last Name']) : '',
        'email'             => isset($params['Email']) ? sanitize($params['Email']) : '',
        'confirm_email'     => isset($params['Confirm Email']) ? sanitize($params['Confirm Email']) : '',
        'phone'             => isset($params['Phone']) ? sanitize($params['Phone']) : '',
        'dob_day'           => isset($params['Date of Birth (Day)']) ? sanitize($params['Date of Birth (Day)']) : '',
        'dob_month'         => isset($params['Date of Birth (Month)']) ? sanitize($params['Date of Birth (Month)']) : '',
        'dob_year'          => isset($params['Date of Birth (Year)']) ? sanitize($params['Date of Birth (Year)']) : '',
        'company_name'      => isset($params['Company name (optional)']) ? sanitize($params['Company name (optional)']) : '',
        'is_limited_company'=> isset($params['Are you a Limited/PLC Company? ']) ? sanitize($params['Are you a Limited/PLC Company? ']) : '',
        'address_line_1'    => isset($params['Address Line 1']) ? sanitize($params['Address Line 1']) : '',
        'city'              => isset($params['City']) ? sanitize($params['City']) : '',
        'postcode'          => isset($params['Postcode']) ? sanitize($params['Postcode']) : '',
        'accept_terms'      => isset($params['i-accept-the-terms-and-co']) ? 'Yes' : 'No',
        'opt_out_marketing' => isset($_POST['opt_out_email_marketing']) ? 'Yes (Opted Out)' : 'No (Opted In)',
    ];
    
    // Format full date of birth
    $form_data['date_of_birth'] = $form_data['dob_day'] . '/' . $form_data['dob_month'] . '/' . $form_data['dob_year'];
    
    // ============================================================
    // VALIDATE REQUIRED FIELDS
    // ============================================================
    
    $errors = [];
    
    if (empty($form_data['first_name'])) {
        $errors[] = 'First Name is required.';
    }
    if (empty($form_data['last_name'])) {
        $errors[] = 'Last Name is required.';
    }
    if (empty($form_data['email']) || !filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid Email is required.';
    }
    if ($form_data['email'] !== $form_data['confirm_email']) {
        $errors[] = 'Email and Confirm Email do not match.';
    }
    if (empty($form_data['phone'])) {
        $errors[] = 'Phone number is required.';
    }
    if (empty($form_data['address_line_1'])) {
        $errors[] = 'Address Line 1 is required.';
    }
    if (empty($form_data['city'])) {
        $errors[] = 'City is required.';
    }
    if (empty($form_data['postcode'])) {
        $errors[] = 'Postcode is required.';
    }
    if ($form_data['accept_terms'] !== 'Yes') {
        $errors[] = 'You must accept the Terms and Conditions.';
    }
    
    // If there are validation errors, show them and stop
    if (!empty($errors)) {
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Validation Error - Constructions Cert</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                body { background: #f8f9fa; font-family: Arial, sans-serif; }
                .error-container { max-width: 700px; margin: 50px auto; }
                .error-list { list-style: none; padding: 0; }
                .error-list li { padding: 8px 0; border-bottom: 1px solid #f5c2c7; }
                .error-list li:last-child { border-bottom: none; }
                .btn-go-back { background: #003366; color: #fff; padding: 10px 30px; border-radius: 50px; text-decoration: none; }
                .btn-go-back:hover { background: #002244; color: #fff; }
            </style>
        </head>
        <body>
            <div class="container error-container">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</h4>
                    </div>
                    <div class="card-body">
                        <ul class="error-list">';
        foreach ($errors as $error) {
            echo '<li><i class="fas fa-times text-danger me-2"></i> ' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>
                        <hr>
                        <a href="javascript:history.back()" class="btn-go-back"><i class="fas fa-arrow-left"></i> Go Back</a>
                    </div>
                </div>
            </div>
        </body>
        </html>';
        exit;
    }
    
    // ============================================================
    // BUILD ADMIN EMAIL CONTENT
    // ============================================================
    
    // Plain text version for admin
    $admin_text = "
    ============================================================
    NEW E-LEARNING COURSE ENQUIRY
    ============================================================
    
    Enquiry Date: " . date('d/m/Y H:i:s') . "
    IP Address: " . $_SERVER['REMOTE_ADDR'] . "
    
    ------------------------------------------------------------
    PERSONAL DETAILS
    ------------------------------------------------------------
    
    First Name:          " . $form_data['first_name'] . "
    Last Name:           " . $form_data['last_name'] . "
    Email:               " . $form_data['email'] . "
    Phone:               " . $form_data['phone'] . "
    Date of Birth:       " . $form_data['date_of_birth'] . "
    Company Name:        " . ($form_data['company_name'] ?: 'Not provided') . "
    Limited/PLC Company: " . $form_data['is_limited_company'] . "
    
    ------------------------------------------------------------
    ADDRESS DETAILS
    ------------------------------------------------------------
    
    Address Line 1:      " . $form_data['address_line_1'] . "
    City:                " . $form_data['city'] . "
    Postcode:            " . $form_data['postcode'] . "
    
    ------------------------------------------------------------
    TERMS & PREFERENCES
    ------------------------------------------------------------
    
    Accepted Terms:      " . $form_data['accept_terms'] . "
    Marketing Opt-Out:   " . $form_data['opt_out_marketing'] . "
    
    ------------------------------------------------------------
    COURSE INFORMATION
    ------------------------------------------------------------
    
    Course Type:         E-Learning Course
    Price:               £15.00 (excl. VAT)
    
    ============================================================
    ";
    
    // HTML version for admin email
    $admin_email_html = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
            .container { max-width: 700px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 10px; }
            .header { background: #003366; color: #fff; padding: 15px 20px; border-radius: 10px 10px 0 0; }
            .header h2 { margin: 0; }
            .section { background: #fff; padding: 15px 20px; margin: 10px 0; border-radius: 8px; border: 1px solid #e5e5e5; }
            .section-title { font-weight: 700; color: #003366; border-bottom: 2px solid #003366; padding-bottom: 8px; margin-bottom: 12px; }
            .label { font-weight: 600; color: #555; display: inline-block; width: 180px; }
            .footer { background: #003366; color: #fff; padding: 12px 20px; border-radius: 0 0 10px 10px; font-size: 12px; text-align: center; }
            .badge-new { background: #4CAF50; color: #fff; padding: 2px 12px; border-radius: 20px; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>🆕 New E-Learning Course Enquiry</h2>
                <p style='margin:5px 0 0; opacity:0.8;'>" . date('d/m/Y H:i:s') . "</p>
            </div>
            
            <div class='section'>
                <div class='section-title'>👤 Personal Details</div>
                <p><span class='label'>First Name:</span> " . $form_data['first_name'] . "</p>
                <p><span class='label'>Last Name:</span> " . $form_data['last_name'] . "</p>
                <p><span class='label'>Email:</span> <a href='mailto:" . $form_data['email'] . "'>" . $form_data['email'] . "</a></p>
                <p><span class='label'>Phone:</span> " . $form_data['phone'] . "</p>
                <p><span class='label'>Date of Birth:</span> " . $form_data['date_of_birth'] . "</p>
                <p><span class='label'>Company Name:</span> " . ($form_data['company_name'] ?: 'Not provided') . "</p>
                <p><span class='label'>Limited/PLC Company:</span> " . $form_data['is_limited_company'] . "</p>
            </div>
            
            <div class='section'>
                <div class='section-title'>🏠 Address Details</div>
                <p><span class='label'>Address Line 1:</span> " . $form_data['address_line_1'] . "</p>
                <p><span class='label'>City:</span> " . $form_data['city'] . "</p>
                <p><span class='label'>Postcode:</span> <strong>" . $form_data['postcode'] . "</strong></p>
            </div>
            
            <div class='section'>
                <div class='section-title'>📋 Terms & Preferences</div>
                <p><span class='label'>Accepted Terms:</span> " . $form_data['accept_terms'] . "</p>
                <p><span class='label'>Marketing Opt-Out:</span> " . $form_data['opt_out_marketing'] . "</p>
            </div>
            
            <div class='section' style='background:#e8f5e9; border-color:#4CAF50;'>
                <div class='section-title' style='border-bottom-color:#4CAF50; color:#2E7D32;'>📚 Course Information</div>
                <p><span class='label'>Course Type:</span> <strong>E-Learning Course</strong></p>
                <p><span class='label'>Price:</span> <strong>£15.00</strong> (excl. VAT)</p>
                <p><span class='label'>Status:</span> <span class='badge-new'>New Enquiry</span></p>
            </div>
            
            <div class='footer'>
                <p style='margin:0;'>This is an automated email from Constructions Cert.</p>
                <p style='margin:0; font-size:11px; opacity:0.7;'>Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // ============================================================
    // BUILD USER CONFIRMATION EMAIL
    // ============================================================
    
    $user_email_html = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
            .container { max-width: 650px; margin: 0 auto; padding: 20px; background: #ffffff; border-radius: 10px; border: 1px solid #e5e5e5; }
            .header { background: linear-gradient(135deg, #003366, #005ea5); color: #fff; padding: 20px 25px; border-radius: 10px 10px 0 0; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 25px; }
            .content h2 { color: #003366; border-bottom: 2px solid #003366; padding-bottom: 10px; }
            .details { background: #f5f7fa; padding: 15px 20px; border-radius: 8px; margin: 15px 0; }
            .details p { margin: 5px 0; }
            .btn { display: inline-block; background: #12b447; color: #fff; padding: 12px 30px; border-radius: 50px; text-decoration: none; font-weight: 600; }
            .btn:hover { background: #0d8a36; }
            .footer { background: #f5f7fa; padding: 15px 20px; border-radius: 0 0 10px 10px; font-size: 12px; text-align: center; color: #6c7a8a; border-top: 1px solid #e5e5e5; }
            .highlight { color: #005ea5; font-weight: 600; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>📚 Thank You, " . $form_data['first_name'] . "!</h1>
                <p style='margin:5px 0 0; opacity:0.9;'>Your E-Learning Course Enquiry</p>
            </div>
            
            <div class='content'>
                <h2>✅ Enquiry Received</h2>
                <p>Dear " . $form_data['first_name'] . " " . $form_data['last_name'] . ",</p>
                <p>Thank you for your interest in our <strong>E-Learning Course</strong>. We have received your enquiry and our team will review it shortly.</p>
                
                <div class='details'>
                    <h3 style='margin-top:0; color:#003366;'>📋 Your Enquiry Details</h3>
                    <p><strong>Name:</strong> " . $form_data['first_name'] . " " . $form_data['last_name'] . "</p>
                    <p><strong>Email:</strong> " . $form_data['email'] . "</p>
                    <p><strong>Phone:</strong> " . $form_data['phone'] . "</p>
                    <p><strong>Course:</strong> E-Learning Course</p>
                    <p><strong>Enquiry Date:</strong> " . date('d/m/Y H:i:s') . "</p>
                </div>
                
                <div style='text-align:center; margin:25px 0;'>
                    <a href='" . $payment_link . "' class='btn' style='color:#fff; text-decoration:none;'>
                        💳 Proceed to Payment
                    </a>
                </div>
                
                <p style='font-size:14px; color:#6c7a8a;'>
                    <strong>What happens next?</strong><br>
                    1. Click the button above to complete your payment securely via Stripe.<br>
                    2. After payment, you will receive instant access to your course.<br>
                    3. If you have any questions, please <a href='" . $site_url . "/contact' style='color:#005ea5;'>contact us</a>.
                </p>
                
            </div>
            
            <div class='footer'>
                <p style='margin:0;'>&copy; " . date('Y') . " Constructions Cert. All rights reserved.</p>
                <p style='margin:0; font-size:11px; opacity:0.7;'>
                    <a href='" . $site_url . "/terms' style='color:#6c7a8a;'>Terms</a> | 
                    <a href='" . $site_url . "/privacy' style='color:#6c7a8a;'>Privacy</a> | 
                    <a href='" . $site_url . "/contact' style='color:#6c7a8a;'>Contact</a>
                </p>
                <p style='margin:5px 0 0; font-size:11px; opacity:0.6;'>This is an automated email. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // ============================================================
    // SEND EMAILS USING mail() FUNCTION
    // ============================================================
    
    // Common headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send admin email
    $admin_headers = $headers;
    $admin_headers .= "From: Constructions Cert <noreply@constructionscert.co.uk>" . "\r\n";
    $admin_headers .= "Reply-To: " . $form_data['email'] . "\r\n";
    
    $admin_sent = @mail($admin_email, $admin_subject, $admin_email_html, $admin_headers);
    
    // Send user confirmation email
    $user_headers = $headers;
    $user_headers .= "From: Constructions Cert <info@constructionscert.co.uk>" . "\r\n";
    $user_headers .= "Reply-To: " . $admin_email . "\r\n";
    
    $user_sent = @mail($form_data['email'], $user_subject, $user_email_html, $user_headers);
    
    // ============================================================
    // SAVE TO DATABASE (OPTIONAL - Uncomment if you have a database)
    // ============================================================
    
    /*
    // Example: Save to MySQL database
    $servername = "localhost";
    $username = "your_username";
    $password = "your_password";
    $dbname = "your_database";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $stmt = $conn->prepare("INSERT INTO e_learning_enquiries (first_name, last_name, email, phone, dob, company_name, is_limited, address, city, postcode, accepted_terms, opt_out, enquiry_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $stmt->bind_param("ssssssssssss", 
        $form_data['first_name'],
        $form_data['last_name'],
        $form_data['email'],
        $form_data['phone'],
        $form_data['date_of_birth'],
        $form_data['company_name'],
        $form_data['is_limited_company'],
        $form_data['address_line_1'],
        $form_data['city'],
        $form_data['postcode'],
        $form_data['accept_terms'],
        $form_data['opt_out_marketing']
    );
    
    $stmt->execute();
    $stmt->close();
    $conn->close();
    */
    
    // ============================================================
    // STORE DATA IN SESSION (FOR PAYMENT CONFIRMATION)
    // ============================================================
    
    session_start();
    $_SESSION['checkout_data'] = [
        'name' => $form_data['first_name'] . ' ' . $form_data['last_name'],
        'email' => $form_data['email'],
        'course' => 'E-Learning Course',
        'amount' => '15.00',
        'submitted_at' => date('d/m/Y H:i:s')
    ];
    
    // ============================================================
    // REDIRECT TO STRIPE PAYMENT PAGE
    // ============================================================
    
    // Redirect to Stripe payment page
    header("Location: " . $payment_link);
    exit;
    
} else {
    // ============================================================
    // IF SOMEONE ACCESSES THIS FILE DIRECTLY (NOT POST)
    // ============================================================
    
    header("Location: " . $site_url . "/e-learning-checkout");
    exit;
}
?>