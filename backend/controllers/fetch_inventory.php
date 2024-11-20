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

$variant_id = $_GET['variant_id'] ?? null;
$channel = $_GET['channel'] ?? null;

if (!$variant_id || !$channel) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
    exit();
}

// Query to get the available stock for the selected variant and channel
$inventory_query = "SELECT quantity FROM inventory WHERE variant_id = ? AND channel = ?";
$inventory_stmt = $conn->prepare($inventory_query);
$inventory_stmt->bind_param('is', $variant_id, $channel);
$inventory_stmt->execute();
$inventory_result = $inventory_stmt->get_result();

if ($inventory_result->num_rows > 0) {
    $inventory_row = $inventory_result->fetch_assoc();
    $available_stock = $inventory_row['quantity'];
    echo json_encode(['success' => true, 'available_stock' => $available_stock]);
} else {
    echo json_encode(['success' => false, 'message' => 'Inventory record not found.']);
}

$inventory_stmt->close();
$conn->close();
?>
