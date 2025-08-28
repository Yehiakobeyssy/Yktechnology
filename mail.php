<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
$applicationemail='yktechnologyapp@kawnex.com';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'Mailer/autoload.php';

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer();

    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                    // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.titan.email';                       // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'yktechnologyapp@kawnex.com';            // SMTP username
    $mail->Password   = 'Mounir@141168';       //Mounir@141168            // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable implicit TLS encryption
    $mail->Port       = 587;                                    // SMTP password

    $mail->SMTPSecure = 'tls';                             // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

// Content
$mail->isHTML(true);  
$mail->CharSet = "UTF-8";
?>