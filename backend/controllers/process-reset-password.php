<?php

// Start session for error messages or feedback
session_start();

// Get the token from the POST data
$token = isset($_POST["token"]) ? $_POST["token"] : null;

if (!$token) {
    echo json_encode(['status' => 'error', 'message' => 'No token provided.']);
    exit;
}

// Hash the token to compare with stored hash in the database
$token_hash = hash("sha256", $token);

// Include the database connection
$mysqli = require __DIR__ . "/db_connection.php";  // Correct path to db_connection.php

// Prepare the SQL query to search for the user with the matching token hash
$sql = "SELECT * FROM users WHERE reset_token_hash = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the user data
$user = $result->fetch_assoc();

// If no user is found, show an error
if ($user === null) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired token.']);
    exit;
}

// If the token has expired, show an error
if (strtotime($user["reset_token_expires_at"]) <= time()) {
    echo json_encode(['status' => 'error', 'message' => 'Token has expired.']);
    exit;
}

// Validate password length (at least 8 characters)
if (strlen($_POST["password"]) < 8) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters long.']);
    exit;
}

// Validate password contains at least one letter
if (!preg_match("/[a-zA-Z]/", $_POST["password"])) {
    echo json_encode(['status' => 'error', 'message' => 'Password must contain at least one letter.']);
    exit;
}

// Validate password contains at least one number
if (!preg_match("/[0-9]/", $_POST["password"])) {
    echo json_encode(['status' => 'error', 'message' => 'Password must contain at least one number.']);
    exit;
}

// Ensure passwords match
if ($_POST["password"] !== $_POST["password_confirmation"]) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords must match.']);
    exit;
}

// Hash the new password using bcrypt
$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

// Update the user's password and clear the reset token
$sql = "UPDATE users 
        SET password_hash = ?, 
            reset_token_hash = NULL, 
            reset_token_expires_at = NULL 
        WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $password_hash, $user["id"]);
$stmt->execute();

// If the password was successfully updated
if ($stmt->affected_rows > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully. You can now login.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update password.']);
}

// Close the statement and database connection
$stmt->close();
$mysqli->close();

?>
