<?php
include '../config/db_connection.php'; // Include the database connection

// Test database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the requested tab via a query parameter
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'all-inventory';

$data = [];

// Switch query based on the tab
switch ($tab) {
    case 'physical-store':
        $sql = "
            SELECT 
                p.product_id,
                p.name AS product_name,
                SUM(i.quantity) AS total_stock,
                'Predicted Demand Placeholder' AS predicted_demand
            FROM products p
            JOIN product_variants pv ON p.product_id = pv.product_id
            JOIN inventory i ON pv.variant_id = i.variant_id
            WHERE i.channel = 'physical_store'
            GROUP BY p.product_id;
        ";
        break;

    case 'shopee':
        $sql = "
            SELECT 
                p.product_id,
                p.name AS product_name,
                SUM(i.quantity) AS total_stock,
                'Predicted Demand Placeholder' AS predicted_demand
            FROM products p
            JOIN product_variants pv ON p.product_id = pv.product_id
            JOIN inventory i ON pv.variant_id = i.variant_id
            WHERE i.channel = 'shopee'
            GROUP BY p.product_id;
        ";
        break;

    case 'tiktok':
        $sql = "
            SELECT 
                p.product_id,
                p.name AS product_name,
                SUM(i.quantity) AS total_stock,
                'Predicted Demand Placeholder' AS predicted_demand
            FROM products p
            JOIN product_variants pv ON p.product_id = pv.product_id
            JOIN inventory i ON pv.variant_id = i.variant_id
            WHERE i.channel = 'tiktok'
            GROUP BY p.product_id;
        ";
        break;

    default:
        // Default to 'all-inventory'
        $sql = "
            SELECT 
                p.product_id,
                p.name AS product_name,
                SUM(i.quantity) AS total_stock,
                'Predicted Demand Placeholder' AS predicted_demand
            FROM products p
            JOIN product_variants pv ON p.product_id = pv.product_id
            JOIN inventory i ON pv.variant_id = i.variant_id
            GROUP BY p.product_id;
        ";
        break;
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
