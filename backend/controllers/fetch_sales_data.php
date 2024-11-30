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

// Fetch sales data for each month in the last year
$querySalesData = "
    SELECT MONTHNAME(s.sale_date) AS month, SUM(s.total_price) AS sales_revenue
    FROM sales s
    WHERE s.sale_date >= CURDATE() - INTERVAL 12 MONTH
    GROUP BY MONTH(s.sale_date)
    ORDER BY s.sale_date ASC";

$result = $conn->query($querySalesData);

// Prepare response data
$salesData = [];
while ($row = $result->fetch_assoc()) {
    $salesData[] = $row;
}

// Close the database connection
$conn->close();

// Return the sales data as JSON
header('Content-Type: application/json');
echo json_encode($salesData);
?>
