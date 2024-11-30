<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "../../../vendor/autoload.php"; // Ensure the correct path

// Load environment variables using PHP dotenv
if (file_exists(__DIR__ . '/../../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
    echo 'Environment variables loaded successfully.<br>';
} else {
    echo 'Failed to load .env file.<br>';
}


// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Debugging: Output the values of the environment variables
    echo 'From Email: ' . getenv('SMTP_FROM') . '<br>';
    echo 'From Name: ' . getenv('SMTP_FROM_NAME') . '<br>';

    // Set mailer to use SMTP
    $mail->isSMTP();

    // Enable SMTP authentication
    $mail->SMTPAuth = true;

    // Set the SMTP server
    $mail->Host = getenv('SMTP_HOST');  // Use environment variable for host
    $mail->Port = getenv('SMTP_PORT');  // Use environment variable for port
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Use TLS encryption

    // SMTP username and password from environment variables
    $mail->Username = getenv('SMTP_USER');  // Gmail username from .env
    $mail->Password = getenv('SMTP_PASS');  // Gmail app password from .env

    // Set the sender's email and name
    $mail->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME'));

    // Set the recipient email dynamically (this will be passed in send-password-reset.php)
    // You will dynamically set the recipient email when sending the password reset email.
    $mail->addAddress($recipientEmail);  // $recipientEmail is passed from send-password-reset.php

    // Email subject and body
    $mail->Subject = 'Password Reset';
    $mail->Body = 'Click the link to reset your password.';

    // Enable HTML content in the email body
    $mail->isHtml(true);

} catch (Exception $e) {
    // If something goes wrong, log the error
    echo "Mailer Error: " . $mail->ErrorInfo;
    exit;
}

// Return the mail object
return $mail;
