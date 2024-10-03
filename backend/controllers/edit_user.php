<?php
// Include database connection
include '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Check if email already exists for other users
    $checkEmailQuery = "SELECT * FROM users WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param('si', $email, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists for another user!']);
    } else {
        // Update user details in the users table
        $query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssi', $first_name, $last_name, $email, $role, $id);

        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'id' => $id, 'first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'role' => $role]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No changes made.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
    }

    $stmt->close();
    $conn->close();
}
