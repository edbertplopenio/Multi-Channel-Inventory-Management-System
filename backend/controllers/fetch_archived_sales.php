<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include the database connection file
require_once '../../backend/config/db_connection.php'; // Adjust the path based on your setup

// Verify database connection
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit();
}

// Get the tab type from the AJAX request
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'archived-sales'; // Default to 'archived-sales'

// Validate the tab parameter (optional in this case since it's only for archived)
$validTabs = ['archived-sales'];
if (!in_array($tab, $validTabs)) {
    echo json_encode(['success' => false, 'message' => 'Invalid tab']);
    exit();
}

// Base SQL query to fetch archived sales data
$query = "
    SELECT 
        s.variant_id,
        p.name AS product_name,
        pv.size,
        pv.color,
        s.sale_date,
        s.quantity,
        s.price AS cost_per_item,
        s.total_price,
        s.channel,
        s.sale_id
    FROM 
        sales s
    JOIN 
        product_variants pv ON s.variant_id = pv.variant_id
    JOIN 
        products p ON pv.product_id = p.product_id
    WHERE 
        s.is_archived = 1"; // Fetch only archived sales

// Prepare the SQL statement
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement']);
    exit();
}

// Execute the statement
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to execute query']);
    exit();
}

// Fetch results
$result = $stmt->get_result();
$sales = [];
while ($row = $result->fetch_assoc()) {
    $sales[] = [
        'variant_id' => $row['variant_id'],
        'product_name' => $row['product_name'],
        'variant' => $row['size'] . ' ' . $row['color'],
        'sale_date' => $row['sale_date'],
        'quantity' => $row['quantity'],
        'cost_per_item' => $row['cost_per_item'],
        'total_price' => $row['total_price'],
        'channel' => $row['channel'],
        'sale_id' => $row['sale_id']
    ];
}

// Set content type to JSON and return the data
header('Content-Type: application/json');
echo json_encode(['success' => true, 'data' => $sales]);

// Close the database connection
$stmt->close();
$conn->close();
?>
