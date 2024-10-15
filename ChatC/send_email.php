<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $prescriptionId = $_POST['prescriptionId'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $pharmacy = $_POST['pharmacy'];
    $note = $_POST['note'];

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                           // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                      // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                  // Enable SMTP authentication
        $mail->Username   = 'dewminimnadee611@gmail.com';           // Your Gmail address
        $mail->Password   = 'ilyz zooz ulqw kvkz';                  // Your App Password (generated from Google)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
        $mail->Port       = 587;                                   // TCP port to connect to

        // Recipients
        $mail->setFrom('dewminimnadee611@gmail.com', 'Pharmacy');   // Sender email and name
        $mail->addAddress($email, $name);                          // Add a recipient (from form)

        // Content
        $mail->isHTML(true);                                       // Set email format to HTML
        $mail->Subject = 'Prescription Details';

        // Build the message body
        $message = "
            <h2>Prescription Details</h2>
            <p><strong>Prescription ID:</strong> $prescriptionId</p>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Address:</strong> $address</p>
            <p><strong>Phone Number:</strong> $phone</p>
            <p><strong>Pharmacy:</strong> $pharmacy</p>
            <p><strong>Note:</strong> $note</p>
        ";

        $mail->Body    = $message;

        // Send the email
        if ($mail->send()) {
            echo "Email sent successfully to $email!";
        } else {
            echo "Error sending email.";
        }

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
