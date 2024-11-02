<?php
require_once '../../backend/config/db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$variant_id = $data['variant_id'] ?? null;

if (!$variant_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid item ID']);
    exit;
}

$query = "UPDATE product_variants SET is_archived = 0, date_archived = NULL WHERE variant_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $variant_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Item unarchived successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to unarchive item: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
