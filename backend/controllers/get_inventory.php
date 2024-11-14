<?php
session_start();
require_once '../config/db_connection.php';

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]));
}

// Check if a specific variant_id is requested
$variantId = isset($_GET['variant_id']) ? intval($_GET['variant_id']) : null;

$sql = "SELECT 
            pv.variant_id,
            p.product_id,
            p.name,
            p.category,
            pv.size,
            pv.color,
            pv.price,
            pv.date_added,
            pv.image,
            SUM(CASE WHEN i.channel = 'physical_store' THEN i.quantity ELSE 0 END) AS quantity_physical_store,
            SUM(CASE WHEN i.channel = 'shopee' THEN i.quantity ELSE 0 END) AS quantity_shopee,
            SUM(CASE WHEN i.channel = 'tiktok' THEN i.quantity ELSE 0 END) AS quantity_tiktok
        FROM inventory i
        JOIN product_variants pv ON i.variant_id = pv.variant_id
        JOIN products p ON pv.product_id = p.product_id
        " . ($variantId ? "WHERE pv.variant_id = $variantId" : "") . "
        GROUP BY pv.variant_id";

$result = mysqli_query($conn, $sql);

$items = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = [
            'variant_id' => $row['variant_id'],
            'product_id' => $row['product_id'],
            'name' => $row['name'],
            'category' => $row['category'],
            'size' => $row['size'],
            'color' => $row['color'],
            'price' => $row['price'],
            'date_added' => $row['date_added'],
            'image' => $row['image'] ?: 'image-placeholder.png',
            'quantity_physical_store' => $row['quantity_physical_store'],
            'quantity_shopee' => $row['quantity_shopee'],
            'quantity_tiktok' => $row['quantity_tiktok']
        ];
    }
}

echo json_encode(['success' => true, 'items' => $items]);

mysqli_close($conn);
?>
