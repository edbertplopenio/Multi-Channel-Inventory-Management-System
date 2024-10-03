<?php
// add_user.php
include '../config/db_connection.php'; // Include the database connection

// Test database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log(print_r($_POST, true)); // Log POST data for debugging

    $firstName = trim($_POST['first-name']);
    $lastName = trim($_POST['last-name']);
    $email = trim($_POST['email']);
    $cellphone = trim($_POST['cellphone']);
    $role = trim($_POST['role']);
    $password = $_POST['password'];
    
    // Check for empty fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($cellphone) || empty($role) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit();
    }

    // Check if the email already exists in the database
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();
    if ($checkEmail->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email is already registered.']);
        exit();
    }
    $checkEmail->close();

    // Check password length
    if (strlen($password) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters long.']);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, cellphone, role, password, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssss", $firstName, $lastName, $email, $cellphone, $role, $hashedPassword);

    // Execute the query
    if ($stmt->execute()) {
        // Get the inserted user ID
        $newUserId = $stmt->insert_id;

        // Return user details
        echo json_encode([
            'status' => 'success', 
            'message' => 'User added successfully',
            'user' => [
                'id' => $newUserId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'role' => $role,
                'status' => 'active' // Assume the new user is active
            ]
        ]);
    } else {
        error_log("SQL Error: " . $stmt->error); // Log SQL error
        echo json_encode(['status' => 'error', 'message' => 'Error adding user: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
