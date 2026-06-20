<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->Host = 'smtp.milesweb.com';
$mail->SMTPAuth = true;
$mail->Username = 'support@applycscs.online';
$mail->Password = 'Applycscs@2026';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->setFrom('support@applycscs.online', 'Test');
$mail->addAddress('YOURPERSONALEMAIL@gmail.com');

$mail->Subject = 'SMTP TEST';
$mail->Body = 'SMTP working';

$mail->send();
echo "SUCCESS";
