<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

require_once '../../backend/config/db_connection.php';

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// Log incoming request for debugging
file_put_contents('debug.log', "Incoming Request: " . file_get_contents('php://input') . "\n", FILE_APPEND);

// Fetch input data
$data = json_decode(file_get_contents('php://input'), true);

// Extract fields
$sale_id = $data['sale_id'] ?? null;
$quantity = $data['quantity'] ?? null;
$sale_date = $data['sale_date'] ?? null;
$total_price = $data['total_price'] ?? null;
$channel = $data['channel'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// Validate required fields
if (!$sale_id || !$quantity || !$sale_date || !$total_price || !$channel) {
    file_put_contents('debug.log', "Missing required fields: " . print_r($data, true) . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit();
}

try {
    // Fetch the current sale record for comparison
    $current_query = "SELECT * FROM sales WHERE sale_id = ?";
    $current_stmt = $conn->prepare($current_query);
    $current_stmt->bind_param('i', $sale_id);
    $current_stmt->execute();
    $current_result = $current_stmt->get_result();

    if ($current_result->num_rows === 0) {
        file_put_contents('debug.log', "No sale record found for sale_id: $sale_id\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Sale record not found.']);
        exit();
    }

    $current_data = $current_result->fetch_assoc();
    file_put_contents('debug.log', "Current Sale Record: " . print_r($current_data, true) . "\n", FILE_APPEND);

    // Validate if there are actual changes
    if (
        $quantity == $current_data['quantity'] &&
        $sale_date == $current_data['sale_date'] &&
        $total_price == $current_data['total_price'] &&
        $channel == $current_data['channel']
    ) {
        echo json_encode(['success' => false, 'message' => 'No changes detected in the sale record.']);
        exit();
    }

    // Fetch the inventory record
    $inventory_query = "SELECT inventory_id, quantity FROM inventory WHERE variant_id = ? AND channel = ?";
    $inventory_stmt = $conn->prepare($inventory_query);
    $inventory_stmt->bind_param('is', $current_data['variant_id'], $channel);
    $inventory_stmt->execute();
    $inventory_result = $inventory_stmt->get_result();

    if ($inventory_result->num_rows === 0) {
        file_put_contents('debug.log', "Inventory record not found for variant_id: {$current_data['variant_id']} and channel: $channel\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Inventory record not found.']);
        exit();
    }

    $inventory_data = $inventory_result->fetch_assoc();
    file_put_contents('debug.log', "Current Inventory Record: " . print_r($inventory_data, true) . "\n", FILE_APPEND);

    // Calculate updated stock
    $available_quantity = $inventory_data['quantity'] + $current_data['quantity']; // Add back the previous quantity
    if ($quantity > $available_quantity) {
        file_put_contents('debug.log', "Insufficient stock. Available: $available_quantity, Requested: $quantity\n", FILE_APPEND);
        echo json_encode([
            'success' => false,
            'message' => "Insufficient stock. Available stock for '$channel': $available_quantity.",
        ]);
        exit();
    }

    // Update the sale record
    $update_query = "
        UPDATE sales
        SET 
            quantity = ?, 
            sale_date = ?, 
            total_price = ?, 
            channel = ?
        WHERE sale_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('isdsi', $quantity, $sale_date, $total_price, $channel, $sale_id);

    if ($update_stmt->execute() && $update_stmt->affected_rows > 0) {
        // Update inventory
        $new_inventory_quantity = $available_quantity - $quantity;
        $update_inventory_query = "UPDATE inventory SET quantity = ? WHERE inventory_id = ?";
        $update_inventory_stmt = $conn->prepare($update_inventory_query);
        $update_inventory_stmt->bind_param('ii', $new_inventory_quantity, $inventory_data['inventory_id']);
        $update_inventory_stmt->execute();

        file_put_contents('debug.log', "Sale and Inventory Updated Successfully.\n", FILE_APPEND);
        echo json_encode(['success' => true, 'message' => 'Sale record and inventory updated successfully.']);
    } else {
        file_put_contents('debug.log', "No changes made to the sale record for sale_id: $sale_id\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'No changes were made to the sale record.']);
    }
} catch (Exception $e) {
    file_put_contents('debug.log', "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

// Close statements and connection
$current_stmt->close();
$inventory_stmt->close();
$conn->close();
?>
