<?php
// Start the session to verify the user is logged in
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include the database connection file
require_once '../../backend/config/db_connection.php'; // Adjust the path as needed

// Verify database connection
if (!isset($conn)) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Get the sale_id from the GET request
$saleId = isset($_GET['sale_id']) ? (int)$_GET['sale_id'] : 0;

if (!$saleId) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing sale ID']);
    exit();
}

try {
    // Query to fetch sale details by sale ID
    $query = "
        SELECT 
            s.sale_id,
            s.variant_id,
            s.sale_date,
            s.quantity,
            s.price AS cost_per_item,
            s.total_price,
            s.channel,
            p.name AS product_name,
            pv.size,
            pv.color
        FROM 
            sales s
        JOIN 
            product_variants pv ON s.variant_id = pv.variant_id
        JOIN 
            products p ON pv.product_id = p.product_id
        WHERE 
            s.sale_id = ?";

    // Prepare the SQL statement
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $saleId);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the sale details
    $saleDetails = $result->fetch_assoc();

    if ($saleDetails) {
        echo json_encode(['success' => true, 'data' => $saleDetails]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sale details not found']);
    }

} catch (Exception $e) {
    // Handle database errors gracefully
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
