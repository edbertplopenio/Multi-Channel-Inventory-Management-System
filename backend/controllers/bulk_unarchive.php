<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once '../../backend/config/db_connection.php';

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit();
}

// Get sale_ids from POST data
$sale_ids = $_POST['sale_ids'] ?? [];

if (empty($sale_ids) || !is_array($sale_ids)) {
    echo json_encode(['success' => false, 'message' => 'No sale IDs provided']);
    exit();
}

// Prepare placeholders for the IN clause
$placeholders = implode(',', array_fill(0, count($sale_ids), '?'));
$query = "UPDATE sales SET is_archived = 0 WHERE sale_id IN ($placeholders)";

$stmt = $conn->prepare($query);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement']);
    exit();
}

// Bind the parameters dynamically
$stmt->bind_param(str_repeat('i', count($sale_ids)), ...$sale_ids);

// Execute the query
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Sales records restored successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to restore sales records.', 'error' => $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
