<?php
session_start();
require_once '../config/db_connection.php'; // Include your database connection

// Check if the database connection is successful
if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]));
}

// Fetch inventory data with related product and variant details
$sql = "SELECT 
            p.product_id,
            p.name,
            p.category,
            pv.size,
            pv.color,
            pv.price,
            pv.date_added,
            pv.image,
            i.channel,
            i.quantity
        FROM inventory i
        JOIN product_variants pv ON i.variant_id = pv.variant_id
        JOIN products p ON pv.product_id = p.product_id";

$result = mysqli_query($conn, $sql);

$items = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = [
            'product_id' => $row['product_id'],
            'name' => $row['name'],
            'category' => $row['category'],
            'quantity' => $row['quantity'],
            'size' => $row['size'],
            'color' => $row['color'],
            'price' => $row['price'],
            'date_added' => $row['date_added'],
            'channel' => $row['channel'], // Directly get the channel from the row
            'image' => $row['image'] ?: 'image-placeholder.png' // Provide a default image if none is found
        ];
    }
    echo json_encode(['success' => true, 'items' => $items]);
} else {
    echo json_encode(['success' => false, 'message' => 'No inventory items found.']);
}

// Close the database connection
mysqli_close($conn);
?>
