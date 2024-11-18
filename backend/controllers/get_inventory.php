<?php
session_start();
require_once '../config/db_connection.php';

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()])); 
}

// Check if a specific variant_id is requested
$variantId = isset($_GET['variant_id']) ? intval($_GET['variant_id']) : null;

// Check if we are validating item name or combination
$checkType = isset($_GET['check_type']) ? $_GET['check_type'] : null;
$item_name = isset($_GET['name']) ? trim($_GET['name']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$size = isset($_GET['size']) ? trim($_GET['size']) : '';
$color = isset($_GET['color']) ? trim($_GET['color']) : '';

// If checking for item name uniqueness
if ($checkType === 'item_name_unique' && !empty($item_name)) {
    $sql_check_name = "SELECT COUNT(*) AS count FROM products WHERE name = ?";
    $stmt = mysqli_prepare($conn, $sql_check_name);
    mysqli_stmt_bind_param($stmt, 's', $item_name);
    mysqli_stmt_execute($stmt);
    $result_name = mysqli_stmt_get_result($stmt);
    $row_name = mysqli_fetch_assoc($result_name);
    if ($row_name['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Item name already exists.']);
        exit;
    }
}

// If checking for duplicate combination of category, size, and color
if ($checkType === 'duplicate_item_info' && !empty($category) && !empty($size) && !empty($color)) {
    $sql_check_duplicate = "SELECT COUNT(*) AS count FROM product_variants pv
                            JOIN products p ON pv.product_id = p.product_id
                            WHERE p.category = ? AND pv.size = ? AND pv.color = ?";
    $stmt = mysqli_prepare($conn, $sql_check_duplicate);
    mysqli_stmt_bind_param($stmt, 'sss', $category, $size, $color);
    mysqli_stmt_execute($stmt);
    $result_duplicate = mysqli_stmt_get_result($stmt);
    $row_duplicate = mysqli_fetch_assoc($result_duplicate);
    if ($row_duplicate['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'A product with the same category, size, and color already exists.']);
        exit;
    }
}

// Default inventory query
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
        WHERE 1 " . 
        ($variantId ? "AND pv.variant_id = $variantId " : "") .
        ($category ? "AND p.category = '$category' " : "") .  // Added category filtering
        "GROUP BY pv.variant_id";

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
