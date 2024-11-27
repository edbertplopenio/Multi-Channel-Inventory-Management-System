<?php
// fetch_archived_inventory.php

session_start();
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once '../../backend/config/db_connection.php';
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Get parameters with default values
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = isset($_GET['per_page']) && is_numeric($_GET['per_page']) ? (int) $_GET['per_page'] : 10;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Calculate OFFSET
$offset = ($page - 1) * $per_page;

// Base SQL query
$sql = "
    SELECT pv.variant_id, p.name, p.category, pv.size, pv.color, pv.price, pv.date_added, pv.image,
    pv.is_archived, pv.date_archived
    FROM product_variants pv
    JOIN products p ON pv.product_id = p.product_id
    WHERE pv.is_archived = 1
";

// Apply search filter if provided
if (!empty($search)) {
    $sql .= " AND (p.name LIKE '%$search%' OR p.category LIKE '%$search%' OR pv.size LIKE '%$search%' OR pv.color LIKE '%$search%')";
}

// Get total items for pagination
$sql_count = "SELECT COUNT(*) as total FROM (" . $sql . ") as count_table";
$result_count = mysqli_query($conn, $sql_count);
$total_items = 0;
if ($result_count && $row_count = mysqli_fetch_assoc($result_count)) {
    $total_items = $row_count['total'];
}

// Append LIMIT and OFFSET for pagination
$sql .= " ORDER BY pv.date_added DESC LIMIT $per_page OFFSET $offset";

$result = mysqli_query($conn, $sql);

$items = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Handle image path
        $image = !empty($row['image']) ? '../../frontend/public/images/' . $row['image'] : '../../frontend/public/images/image-placeholder.png';
        $row['image_url'] = $image;
        $items[] = $row;
    }
}

$total_pages = ceil($total_items / $per_page);

echo json_encode([
    'success' => true,
    'data' => $items,
    'pagination' => [
        'current_page' => $page,
        'per_page' => $per_page,
        'total_items' => $total_items,
        'total_pages' => $total_pages
    ]
]);

mysqli_close($conn);
?>
