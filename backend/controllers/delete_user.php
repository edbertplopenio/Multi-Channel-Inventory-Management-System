<?php
// Include database connection
include '../config/db_connection.php';

// Ensure request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the POST data (JSON)
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    // Check if the user exists before attempting to delete
    $checkUserQuery = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($checkUserQuery);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'User not found!']);
    } else {
        // Delete user from users table
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'User deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting user: ' . $stmt->error]);
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
