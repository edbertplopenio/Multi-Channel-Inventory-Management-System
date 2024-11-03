<?php
// archive_item.php
require_once '../../backend/config/db_connection.php';

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);
$variant_id = $data['item_id'] ?? null;

if (!$variant_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid item ID']);
    exit;
}

// Check the total quantity of the item across all channels
$quantity_check_query = "
    SELECT SUM(quantity) AS total_quantity 
    FROM inventory 
    WHERE variant_id = ?";
$quantity_stmt = $conn->prepare($quantity_check_query);
if (!$quantity_stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Query preparation failed: ' . $conn->error]);
    exit;
}
$quantity_stmt->bind_param("i", $variant_id);
$quantity_stmt->execute();
$quantity_result = $quantity_stmt->get_result();
$quantity_row = $quantity_result->fetch_assoc();

// If the total quantity is greater than 0, prevent archiving
if ($quantity_row['total_quantity'] > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Cannot archive item with remaining stock']);
    $quantity_stmt->close();
    $conn->close();
    exit;
}

// Proceed to archive the item if the total quantity is zero
$archive_query = "UPDATE product_variants SET is_archived = 1, date_archived = CURDATE() WHERE variant_id = ?";
$archive_stmt = $conn->prepare($archive_query);

if (!$archive_stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$archive_stmt->bind_param("i", $variant_id);

if ($archive_stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Item archived successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to archive item: ' . $archive_stmt->error]);
}

// Close statements and connection
$archive_stmt->close();
$quantity_stmt->close();
$conn->close();
