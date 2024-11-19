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

$data = json_decode(file_get_contents('php://input'), true);

// Extract data
$variant_id = $data['variant_id'] ?? null;
$product_id = $data['product_id'] ?? null;
$quantity = $data['quantity'] ?? null;
$price = $data['price'] ?? null;
$total_price = $data['total_price'] ?? null;
$channel = $data['channel'] ?? null;
$sale_date = $data['sale_date'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$variant_id || !$product_id || !$quantity || !$price || !$total_price || !$channel || !$sale_date || !$user_id) {
    echo json_encode([
        'success' => false, 
        'message' => 'Missing required fields.',
        'details' => [
            'variant_id' => $variant_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $price,
            'total_price' => $total_price,
            'channel' => $channel,
            'sale_date' => $sale_date,
            'user_id' => $user_id
        ]
    ]);
    exit();
}

// Check if the exact sale record already exists for the same variant, product, sale date, and quantity
$check_sale_query = "SELECT * FROM sales WHERE variant_id = ? AND product_id = ? AND sale_date = ? AND quantity = ? AND price = ? AND total_price = ?";
$check_sale_stmt = $conn->prepare($check_sale_query);
$check_sale_stmt->bind_param('iisidd', $variant_id, $product_id, $sale_date, $quantity, $price, $total_price);
$check_sale_stmt->execute();
$check_sale_result = $check_sale_stmt->get_result();

if ($check_sale_result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Exact sales record already exists for this product on the selected date with the same quantity and price.']);
    exit();
}

// Proceed with inventory lookup
$inventory_query = "SELECT inventory_id, quantity FROM inventory WHERE variant_id = ? AND channel = ?";
$inventory_stmt = $conn->prepare($inventory_query);
$inventory_stmt->bind_param('is', $variant_id, $channel);
$inventory_stmt->execute();
$inventory_result = $inventory_stmt->get_result();

if ($inventory_result->num_rows > 0) {
    $inventory = $inventory_result->fetch_assoc();
    $inventory_id = $inventory['inventory_id'];
    $current_quantity = $inventory['quantity'];
} else {
    echo json_encode(['success' => false, 'message' => 'Inventory record not found.']);
    exit();
}

// Check if there is enough stock to fulfill the sale
if ($quantity > $current_quantity) {
    echo json_encode([
        'success' => false,
        'message' => 'Insufficient stock. Available stock: ' . $current_quantity,
        'available_stock' => $current_quantity
    ]);
    exit();
}

// Insert the sales record
$sales_query = "INSERT INTO sales (inventory_id, variant_id, product_id, quantity, price, total_price, channel, sale_date, user_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$sales_stmt = $conn->prepare($sales_query);
$sales_stmt->bind_param('iiiiddssi', $inventory_id, $variant_id, $product_id, $quantity, $price, $total_price, $channel, $sale_date, $user_id);

// Start by cleaning any output buffers
ob_clean();
header('Content-Type: application/json'); // Ensure the response is JSON

if ($sales_stmt->execute()) {
    // Update the inventory after the sale
    $new_quantity = $current_quantity - $quantity;
    $update_inventory_query = "UPDATE inventory SET quantity = ? WHERE inventory_id = ?";
    $update_inventory_stmt = $conn->prepare($update_inventory_query);
    $update_inventory_stmt->bind_param('ii', $new_quantity, $inventory_id);
    $update_inventory_stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Sales record added successfully, stock updated.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add sales record.']);
}

$sales_stmt->close();
$update_inventory_stmt->close();
$conn->close();
?>
