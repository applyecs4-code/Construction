<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Create object of PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'support@applycscs.online';   // 🔹 यहाँ अपना Gmail डालो
    $mail->Password   = 'Applycscs@2026';      // 🔹 यहाँ अपना App Password डालो
    $mail->SMTPSecure = 'tls'; // या 'ssl'
    $mail->Port       = 587;   // TLS → 587, SSL → 465

    // Recipients
    $mail->setFrom('support@applycscs.online', 'CSCS Test Mail');
    $mail->addAddress('nr5529513@gmail.com', 'Admin');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Mail from CSCS Website';
    $mail->Body    = '<h3 style="color:green;">Congratulations 🎉 Email working successfully!</h3>';

    // Send
    if ($mail->send()) {
        echo "✅ Mail Sent Successfully!";
    } else {
        echo "❌ Mail Not Sent!";
    }
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}";
}
