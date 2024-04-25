#!/usr/bin/php
<?php
// Include PHPMailer autoload file
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // SMTP configuration (Gmail)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ciaraugasmoyserver@gmail.com'; // Your Gmail email address
    $mail->Password = 'mvsguxgtejjlsflc'; // Your Gmail password
    //serverp@ssword444@no<3
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Sender and recipient
    $mail->setFrom('ciaraugasmoyserver@gmail.com', 'Your Name'); // Sender's email and name
    $mail->addAddress('ccu3@njit.edu', 'Ciara'); // Recipient's email and name

    // Email content
    $mail->isHTML(false); // Set email format to plain text
    $mail->Subject = 'Important Message';
    $mail->Body = '12345';

    // Send the email
    $mail->send();
    echo "Email sent successfully";
} catch (Exception $e) {
    echo "Failed to send email. Error: {$mail->ErrorInfo}";
}
?>


