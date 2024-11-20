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

// Get sale_ids from POST data
$sale_ids = $_POST['sale_ids'] ?? [];

if (empty($sale_ids) || !is_array($sale_ids)) {
    echo json_encode(['success' => false, 'message' => 'No sale IDs provided.']);
    exit();
}

// Prepare placeholders for the IN clause
$placeholders = implode(',', array_fill(0, count($sale_ids), '?'));
$query = "UPDATE sales SET is_archived = 1 WHERE sale_id IN ($placeholders)";

$stmt = $conn->prepare($query);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL query.', 'error' => $conn->error]);
    exit();
}

// Bind the parameters dynamically
$stmt->bind_param(str_repeat('i', count($sale_ids)), ...$sale_ids);

// Execute the query
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Sales records archived successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to archive sales records.', 'error' => $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
