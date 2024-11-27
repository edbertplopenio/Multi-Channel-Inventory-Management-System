<?php
session_start();
require_once '../config/db_connection.php';

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()])); 
}

// Get pagination parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$records_per_page = isset($_GET['records_per_page']) ? intval($_GET['records_per_page']) : 10;
$offset = ($page - 1) * $records_per_page;

// Handle filtering parameters if any (size, color, category, etc.)
$filters = [];
if (isset($_GET['size']) && !empty($_GET['size'])) {
    $filters[] = "pv.size = '" . mysqli_real_escape_string($conn, $_GET['size']) . "'";
}
if (isset($_GET['color']) && !empty($_GET['color'])) {
    $filters[] = "pv.color = '" . mysqli_real_escape_string($conn, $_GET['color']) . "'";
}
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $filters[] = "p.category = '" . mysqli_real_escape_string($conn, $_GET['category']) . "'";
}
if (isset($_GET['date_added']) && !empty($_GET['date_added'])) {
    $filters[] = "pv.date_added = '" . mysqli_real_escape_string($conn, $_GET['date_added']) . "'";
}

// Build the WHERE clause
$whereClause = '';
if (!empty($filters)) {
    $whereClause = ' AND ' . implode(' AND ', $filters);
}

// Default inventory query with pagination
$sql = "
SELECT 
    SQL_CALC_FOUND_ROWS
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
WHERE pv.is_archived = 0 $whereClause
GROUP BY pv.variant_id
LIMIT $records_per_page OFFSET $offset";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die(json_encode(['success' => false, 'message' => 'Error executing query: ' . mysqli_error($conn)]));
}

// Get total records without LIMIT
$total_records_result = mysqli_query($conn, "SELECT FOUND_ROWS() AS total");
$total_records = mysqli_fetch_assoc($total_records_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

$items = [];
if (mysqli_num_rows($result) > 0) {
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
            'quantity_tiktok' => $row['quantity_tiktok'],
        ];
    }
}

echo json_encode([
    'success' => true,
    'items' => $items,
    'total_pages' => $total_pages,
    'current_page' => $page,
    'total_records' => $total_records
]);

mysqli_close($conn);
?>
