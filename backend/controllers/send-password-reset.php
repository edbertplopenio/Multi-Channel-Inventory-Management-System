<?php
include '../config/db_connection.php';  // Ensure correct path to your db_connection.php

// Assuming you are calling this with POST and the email is passed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $email = $_POST["email"];

        // Generate a token and its hash
        $token = bin2hex(random_bytes(16)); 
        $token_hash = hash("sha256", $token);

        // Set expiry for token (30 minutes)
        $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

        // Check if the email exists in the database
        $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkEmailQuery);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            echo json_encode(['status' => 'error', 'message' => 'No account found with that email address.']);
            exit;
        }

        // Update the reset token in the database
        $updateQuery = "UPDATE users SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('sss', $token_hash, $expiry, $email);

        if ($stmt->execute()) {
            // Include the mailer setup and pass recipient dynamically
            $mail = require __DIR__ . "../../../backend/controllers/mailer.php";

            // Set the dynamic recipient email (the email passed in the request)
            $mail->clearAddresses();
            $mail->addAddress($email);  // Dynamically set the recipient email

            // Set the email body
            $mail->Subject = "Password Reset";
            $mail->Body = <<<END
            Click <a href="http://localhost/MIOS/backend/controllers/reset_password.php?token=$token">here</a> to reset your password.
            END;

            // Attempt to send the email
            try {
                $mail->send();
                echo json_encode(['status' => 'success', 'message' => 'Password reset email sent. Please check your inbox.']);
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send reset email. Mailer error: ' . $mail->ErrorInfo]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update reset token. Please try again later.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    }

    $conn->close();
}
