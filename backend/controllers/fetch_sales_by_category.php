<?php
// Assuming you have a database connection in db_connection.php
include_once '../config/db_connection.php'; // Adjust the path if needed.

// Query the sales data grouped by category (You may need to adjust this based on your table structure)
$sql = "SELECT p.category, SUM(s.total_price) AS total_sales
        FROM sales s
        JOIN product_variants pv ON s.variant_id = pv.variant_id
        JOIN products p ON pv.product_id = p.product_id
        GROUP BY p.category";

$result = $conn->query($sql);

$salesData = [];
while ($row = $result->fetch_assoc()) {
    $salesData[] = [
        'category' => $row['category'],
        'total_sales' => $row['total_sales']
    ];

}

// Return the data as JSON
echo json_encode($salesData);
?>
