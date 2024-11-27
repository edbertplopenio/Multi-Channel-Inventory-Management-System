<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include the database connection file
require_once '../../backend/config/db_connection.php'; // Adjust the path based on your setup

// Verify database connection
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit();
}

// Get and sanitize input parameters
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'all-orders';
$validTabs = ['all-orders', 'physical_store', 'shopee', 'tiktok'];

if (!in_array($tab, $validTabs)) {
    echo json_encode(['success' => false, 'message' => 'Invalid tab']);
    exit();
}

// Optional pagination parameters
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base WHERE clause
$whereClause = "WHERE s.is_archived = 0";
$params = [];
$types = '';

// Add filter for specific channels
if ($tab !== 'all-orders') {
    $whereClause .= " AND s.channel = ?";
    $params[] = $tab;
    $types .= 's';
}

// Prepare count query to get total records
$count_query = "
    SELECT COUNT(*) as total_records
    FROM 
        sales s
    JOIN 
        product_variants pv ON s.variant_id = pv.variant_id
    JOIN 
        products p ON pv.product_id = p.product_id
    $whereClause
";

$stmt = $conn->prepare($count_query);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_records = $row['total_records'];
$stmt->close();

// Now, prepare the main data query with LIMIT and OFFSET
$query = "
    SELECT 
        s.variant_id,
        p.name AS product_name,
        pv.size,
        pv.color,
        s.sale_date,
        s.quantity,
        s.price AS cost_per_item,
        s.total_price,
        s.channel,
        s.sale_id
    FROM 
        sales s
    JOIN 
        product_variants pv ON s.variant_id = pv.variant_id
    JOIN 
        products p ON pv.product_id = p.product_id
    $whereClause
    ORDER BY s.sale_date DESC
    LIMIT ? OFFSET ?
";

// Add limit and offset to parameters
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare SQL statement: ' . $conn->error]);
    exit();
}

// Bind parameters
$stmt->bind_param($types, ...$params);

// Execute the statement
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to execute query: ' . $stmt->error]);
    exit();
}

// Fetch results
$result = $stmt->get_result();
$sales = [];

while ($row = $result->fetch_assoc()) {
    $sales[] = [
        'variant_id' => $row['variant_id'],
        'product_name' => $row['product_name'],
        'variant' => $row['size'] . ' ' . $row['color'],
        'sale_date' => $row['sale_date'],
        'quantity' => $row['quantity'],
        'cost_per_item' => $row['cost_per_item'],
        'total_price' => $row['total_price'],
        'channel' => $row['channel'],
        'sale_id' => $row['sale_id']
    ];
}

// Set content type to JSON and return the data along with total records
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'data' => $sales,
    'total_records' => $total_records,
    'records_per_page' => $limit,
    'current_page' => $page,
]);

// Close resources
$stmt->close();
$conn->close();
?>
