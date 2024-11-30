<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: ../../frontend/public/login.html");
    exit();
}

require_once '../../backend/config/db_connection.php';
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Number of items per page
$limit = 100;

// Get the current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query to fetch the archived inventory
$sql_all_inventory = "
    SELECT pv.variant_id, p.name, p.category, pv.size, pv.color, pv.price, pv.date_added, pv.image, pv.is_archived, pv.date_archived
    FROM product_variants pv
    JOIN products p ON pv.product_id = p.product_id
    WHERE pv.is_archived = 1
    LIMIT $limit OFFSET $offset
";

$result_all_inventory = mysqli_query($conn, $sql_all_inventory);

// Count the total number of archived items
$sql_count = "SELECT COUNT(*) as total FROM product_variants WHERE is_archived = 1";
$result_count = mysqli_query($conn, $sql_count);
$total = mysqli_fetch_assoc($result_count)['total'];
$total_pages = ceil($total / $limit);

// Prepare the inventory data
$inventory = [];
while ($row = mysqli_fetch_assoc($result_all_inventory)) {
    $inventory[] = $row;
}

// Prepare the response data
$response = [
    'inventory' => $inventory,
    'total_pages' => $total_pages,
    'current_page' => $page
];

// Output as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
