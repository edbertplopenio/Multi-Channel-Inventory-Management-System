<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

require_once '../../backend/config/db_connection.php';

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// Get sale_id from POST data
$sale_id = $_POST['sale_id'] ?? null;

if (!$sale_id) {
    echo json_encode(['success' => false, 'message' => 'Sale ID is missing.']);
    exit();
}

// Archive the sales record (mark as archived)
$archive_query = "UPDATE sales SET is_archived = 1 WHERE sale_id = ?";

// Prepare the SQL statement
$stmt = $conn->prepare($archive_query);

// Check if the statement was prepared successfully
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL query.', 'error' => $conn->error]);
    exit();
}

// Bind the parameters to the prepared statement
$stmt->bind_param('i', $sale_id);

// Execute the query
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Sales record archived successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to archive the sales record.', 'error' => $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
