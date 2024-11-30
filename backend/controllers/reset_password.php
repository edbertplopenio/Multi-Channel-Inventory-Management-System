<?php

// Start the session (if you need session variables for error messages, etc.)
session_start();

include '../config/db_connection.php';  // Ensure correct path to your db_connection.php

// Get the token from the URL
$token = isset($_GET["token"]) ? $_GET["token"] : null;

if (!$token) {
    echo json_encode(['status' => 'error', 'message' => 'No token provided.']);
    exit;
}

// Hash the token to compare with stored hash in the database
$token_hash = hash("sha256", $token);

// Include the database connection (use your updated connection function)
$mysqli = require __DIR__ . "../config/db_connection.php";  // Correct path to db_connection.php

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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>

    <h1>Reset Your Password</h1>

    <!-- The form where the user can enter their new password -->
    <form method="post" action="process-reset-password.php">

        <!-- Pass the token to the next processing script -->
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label for="password">New Password</label>
        <input type="password" id="password" name="password" required>

        <label for="password_confirmation">Repeat Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>

        <button type="submit">Reset Password</button>
    </form>

</body>
</html>

<?php
// Close the database connection
$stmt->close();
$mysqli->close();
?>
