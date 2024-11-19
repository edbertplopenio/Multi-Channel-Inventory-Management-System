<?php
// Start the session
session_start();

// Include the database connection file
require_once('../config/db_connection.php');

// Check if the form has been submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize the email and password
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['pass']);

    // Check if email or password is empty
    if (empty($email) || empty($password)) {
        echo "Both email and password are required.";
        exit();
    }

    // Prepare the SQL query to find the user by email
    $sql = "SELECT id, email, password, role FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters and execute the query
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if a user was found
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Verify the provided password with the hashed password in the database
            if (password_verify($password, $user['password'])) {
                // Store user information in the session
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_id'] = $user['id']; // Store user_id for future use

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

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // Handle SQL preparation errors
        echo "An error occurred while processing your request.";
    }
} else {
    // If accessed without POST request, redirect to the login page
    header("Location: login.html");
    exit();
}

// Close the database connection
mysqli_close($conn);
?>
