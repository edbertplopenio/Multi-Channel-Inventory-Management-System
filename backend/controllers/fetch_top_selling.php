<?php
// Include database connection
include_once '../config/db_connection.php'; // Adjust the path if needed.

// SQL query to fetch top-selling products along with units sold and revenue
$query = "
    SELECT p.name AS product_name, 
           SUM(s.quantity) AS units_sold, 
           SUM(s.total_price) AS revenue
    FROM sales s
    JOIN product_variants pv ON s.variant_id = pv.variant_id
    JOIN products p ON pv.product_id = p.product_id
    WHERE s.is_archived = 0
    GROUP BY p.name
    ORDER BY revenue DESC
    LIMIT 10
";

// Execute the query
$result = $conn->query($query);

// Initialize an array to store the top-selling products data
$top_selling_products = array();

// Fetch the data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $top_selling_products[] = $row;
    }
}

// Return the data as JSON for use in frontend
echo json_encode($top_selling_products);
?>
