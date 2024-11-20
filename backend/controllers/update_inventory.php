<?php
session_start();

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

require_once '../../backend/config/db_connection.php';

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$variant_id = $data['variant_id'] ?? null;
$channel = $data['channel'] ?? null;
$quantity = $data['quantity'] ?? null;

if (!$variant_id || !$channel || !$quantity) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
    exit();
}

// Update inventory by subtracting the sold quantity
$update_inventory_query = "UPDATE inventory SET quantity = quantity - ? WHERE variant_id = ? AND channel = ?";
$update_inventory_stmt = $conn->prepare($update_inventory_query);
$update_inventory_stmt->bind_param('iis', $quantity, $variant_id, $channel);

if ($update_inventory_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Inventory updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update inventory.']);
}

$update_inventory_stmt->close();
$conn->close();
?>
