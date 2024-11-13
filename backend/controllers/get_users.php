<?php
// get_users.php
include '../config/db_connection.php'; // Include the database connection

// Test database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare the query to fetch all users
$query = "SELECT id, first_name, last_name, email, cellphone, role, image, created_at FROM users";
$result = $conn->query($query);

if ($result) {
    $users = [];

    // Adjust base URL according to your actual project setup
    $image_base_url = "../../frontend/public/images/users/";

    // Fetch each user and add to the users array
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'id' => $row['id'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'cellphone' => $row['cellphone'],
            'role' => $row['role'],
            'status' => 'active', // Assuming all users are active
            'image' => $row['image'] ? $image_base_url . $row['image'] : null, // Full URL to image
            'created_at' => $row['created_at']
        ];
    }

    // Return all users as JSON
    echo json_encode(['status' => 'success', 'users' => $users]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error fetching users: ' . $conn->error]);
}

// Close the database connection
$conn->close();
?>
