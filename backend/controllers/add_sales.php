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

// Check if the sale record already exists for the same variant, product, and sale date
$check_sale_query = "SELECT * FROM sales WHERE variant_id = ? AND product_id = ? AND sale_date = ?";
$check_sale_stmt = $conn->prepare($check_sale_query);
$check_sale_stmt->bind_param('iis', $variant_id, $product_id, $sale_date);
$check_sale_stmt->execute();
$check_sale_result = $check_sale_stmt->get_result();

if ($check_sale_result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Sales record already exists for this product on the selected date.']);
    exit();
}

// Proceed with inventory lookup
$inventory_query = "SELECT inventory_id FROM inventory WHERE variant_id = ? AND channel = ?";
$inventory_stmt = $conn->prepare($inventory_query);
$inventory_stmt->bind_param('is', $variant_id, $channel);
$inventory_stmt->execute();
$inventory_result = $inventory_stmt->get_result();

if ($inventory_result->num_rows > 0) {
    $inventory_id = $inventory_result->fetch_assoc()['inventory_id'];
} else {
    echo json_encode(['success' => false, 'message' => 'Inventory record not found.']);
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
    echo json_encode(['success' => true, 'message' => 'Sales record added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add sales record.']);
}

$sales_stmt->close();
$conn->close();

?>
