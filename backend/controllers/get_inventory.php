<?php
session_start();
require_once '../config/db_connection.php'; // Include your database connection

// Fetch inventory data from the database
$sql = "SELECT * FROM inventory";
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
            'channel' => json_decode($row['channel'], true), // Decode the channels
            'image' => $row['image']
        ];
    }
    echo json_encode(['success' => true, 'items' => $items]);
} else {
    echo json_encode(['success' => false, 'message' => 'No inventory items found.']);
}
?>
