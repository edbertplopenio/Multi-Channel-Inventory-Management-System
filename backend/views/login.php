<?php
// Start the session
session_start();

// Include the database connection file
include('../config/db_connection.php');

// Check if the form has been submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email and password from the form
    $email = $_POST['email'];
    $password = $_POST['pass'];

    // Prepare the SQL query to find the user by email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the provided password with the hashed password from the database
        if (password_verify($password, $user['password'])) {
            // Store user information in the session
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Set a cookie to indicate splash screen has been shown
            setcookie('splashScreenShown', 'true', time() + 86400, '/'); // Cookie lasts for 1 day

            // Redirect to the main page (index.php)
            header("Location: ../../frontend/public/index.php");
            exit();
        } else {
            // If the password is incorrect, show an error message
            echo "Invalid credentials. Please try again.";
        }
    } else {
        // If no user is found with the provided email, show an error message
        echo "Invalid credentials. Please try again.";
    }
}
?>
