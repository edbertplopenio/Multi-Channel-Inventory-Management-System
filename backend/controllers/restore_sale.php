<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include the database connection file
require_once '../../backend/config/db_connection.php';

// Verify database connection
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit();
}

// Get the sale_id from the request
$sale_id = isset($_POST['sale_id']) ? $_POST['sale_id'] : null;

if (!$sale_id) {
    echo json_encode(['success' => false, 'message' => 'Sale ID is required']);
    exit();
}

// SQL query to restore the sale
$query = "UPDATE sales SET is_archived = 0 WHERE sale_id = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement']);
    exit();
}

$stmt->bind_param('i', $sale_id); // Bind sale_id as an integer

// Execute the query
if ($stmt->execute()) {
    // Success response
    echo json_encode(['success' => true, 'message' => 'Sale restored successfully']);
} else {
    // Error response if the query fails
    $error = $stmt->error;
    echo json_encode(['success' => false, 'message' => 'Failed to restore sale: ' . $error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
