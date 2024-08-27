<?php
include('../config/db_connection.php');

// Admin user details
$first_name = "Admin";
$last_name = "User";
$email = "admin@example.com";
$password = "password123"; // Plain text password
$role = "Admin";

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert the user into the database
$sql = "INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssss", $first_name, $last_name, $email, $hashed_password, $role);

if (mysqli_stmt_execute($stmt)) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
