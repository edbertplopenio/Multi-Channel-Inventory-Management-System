<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: ../../frontend/public/login.html");
    exit();
}

// Check if the request is an AJAX request
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header('HTTP/1.1 403 Forbidden');
    exit('Direct access not allowed.');
}

// Include the database connection file
require_once '../../backend/config/db_connection.php'; // Adjust path to your actual database config

if (!$conn) {
    die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]));
}

// Get the search term from the AJAX request
$searchTerm = isset($_GET['q']) ? $_GET['q'] : '';

// SQL query to fetch product and variant details
$query = "
    SELECT 
        p.product_id, 
        p.name AS product_name, 
        pv.variant_id, 
        pv.size, 
        pv.color, 
        pv.price 
    FROM 
        products p
    JOIN 
        product_variants pv ON p.product_id = pv.product_id
    WHERE 
        pv.is_archived = 0 
        AND (
            p.name LIKE ? OR
            pv.size LIKE ? OR
            pv.color LIKE ?
        )
    ORDER BY 
        p.name ASC, pv.size ASC, pv.color ASC";

// Prepare the SQL statement
$stmt = $conn->prepare($query);
$searchParam = "%$searchTerm%";
$stmt->bind_param('sss', $searchParam, $searchParam, $searchParam);

// Execute the statement
if (!$stmt->execute()) {
    echo json_encode(['error' => 'Failed to execute query: ' . $stmt->error]);
    exit();
}

$result = $stmt->get_result();

// Create an array to hold the products and variants
// Create an array to hold the products and variants
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = [
        'id' => $row['variant_id'],  // variant_id for the select2
        'text' => $row['product_name'] . ' - ' . $row['size'] . ' ' . $row['color'],  // Display name in select2
        'product_id' => $row['product_id'],  // Add product_id to the response
        'price' => $row['price'] // Include price for cost field
    ];
}


// Set the content type to JSON
header('Content-Type: application/json');

// Return the products as a JSON response
echo json_encode(['items' => $products]);

// Close the database connection
$stmt->close();
$conn->close();
?>
