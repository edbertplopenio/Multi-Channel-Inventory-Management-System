<?php
// Include database connection file
require_once '../../backend/config/db_connection.php';// Adjust the path based on your project structure

// Set the response header to JSON
header('Content-Type: application/json');

// Get the name and variant_id from the GET parameters
$item_name = isset($_GET['name']) ? $_GET['name'] : '';
$variant_id = isset($_GET['variant_id']) ? $_GET['variant_id'] : '';

// Check if both parameters are provided
if (empty($item_name)) {
    echo json_encode(['exists' => false, 'message' => 'Item name is required.']);
    exit();
}

// Sanitize input to prevent SQL injection
$item_name = mysqli_real_escape_string($conn, $item_name);
$variant_id = mysqli_real_escape_string($conn, $variant_id);

// SQL query to check if the name already exists (excluding the current variant)
$query = "
    SELECT COUNT(*) AS count 
    FROM products p
    JOIN product_variants pv ON p.product_id = pv.product_id
    WHERE p.name = '$item_name' AND pv.variant_id != '$variant_id'";

// Execute the query
$result = mysqli_query($conn, $query);

// Check for query errors
if (!$result) {
    echo json_encode(['exists' => false, 'message' => 'Database query failed: ' . mysqli_error($conn)]);
    exit();
}

// Fetch the result
$row = mysqli_fetch_assoc($result);

// If the count is greater than 0, the name already exists
if ($row['count'] > 0) {
    echo json_encode(['exists' => true, 'message' => 'An item with this name already exists.']);
} else {
    echo json_encode(['exists' => false]);
}

// Close the database connection
mysqli_close($conn);
?>
