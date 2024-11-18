<?php
require_once '../../backend/config/db_connection.php';

// Read the JSON input data
$data = json_decode(file_get_contents("php://input"), true);
$variant_id = $data['variant_id'] ?? null;

if (!$variant_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
    exit;
}

// Update the archived status of the product variant
$query = "UPDATE product_variants SET is_archived = 0, date_archived = NULL WHERE variant_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $variant_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Item unarchived successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to unarchive item: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
