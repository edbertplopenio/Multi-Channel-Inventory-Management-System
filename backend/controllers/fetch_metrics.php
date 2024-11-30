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

// Fetch top-selling products (top 5 by quantity sold)
$queryTopSelling = "
    SELECT p.name, SUM(s.quantity) AS total_sales
    FROM sales s
    JOIN product_variants pv ON s.variant_id = pv.variant_id
    JOIN products p ON pv.product_id = p.product_id
    GROUP BY p.product_id
    ORDER BY total_sales DESC
    LIMIT 5"; // Adjust limit as needed

// Fetch low-stock products (threshold: 10 items)
// Fetch low-stock products (threshold: 10 items for overall product)
$queryLowStock = "
    SELECT p.name, SUM(i.quantity) AS total_quantity
    FROM inventory i
    JOIN product_variants pv ON i.variant_id = pv.variant_id
    JOIN products p ON pv.product_id = p.product_id
    GROUP BY p.product_id
    HAVING total_quantity <= 10"; // Adjust threshold as necessary



// Fetch slow-moving products (sold less than 5 units in the last month)
// Fetch slow-moving products (sold less than 5 units in the last month for overall product)
$querySlowMoving = "
    SELECT p.name, SUM(s.quantity) AS total_sales
    FROM sales s
    JOIN product_variants pv ON s.variant_id = pv.variant_id
    JOIN products p ON pv.product_id = p.product_id
    WHERE s.sale_date >= CURDATE() - INTERVAL 1 MONTH
    GROUP BY p.product_id
    HAVING total_sales < 5"; // Adjust the threshold as necessary


// Fetch total sales in the last month
$queryTotalSales = "
    SELECT SUM(total_price) AS total_sales
    FROM sales
    WHERE sale_date >= CURDATE() - INTERVAL 1 MONTH";

// Execute queries
$topSelling = $conn->query($queryTopSelling)->fetch_all(MYSQLI_ASSOC);
$lowStock = $conn->query($queryLowStock)->fetch_all(MYSQLI_ASSOC);
$slowMoving = $conn->query($querySlowMoving)->fetch_all(MYSQLI_ASSOC);
$totalSales = $conn->query($queryTotalSales)->fetch_assoc();

// Prepare response data
$response = [
    'top_selling' => $topSelling,
    'low_stock' => $lowStock,
    'slow_moving' => $slowMoving,
    'total_sales' => $totalSales['total_sales']
];

// Set the content type to JSON
header('Content-Type: application/json');

// Return the response as JSON
echo json_encode($response);

// Close the database connection
$conn->close();
?>
