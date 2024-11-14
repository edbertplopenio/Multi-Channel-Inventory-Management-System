<?php
// get_variant_quantities.php
header('Content-Type: application/json');
include 'db_connection.php'; // Include your DB connection script

$variant_id = intval($_GET['variant_id']);

// Query to get quantities by channel for the specified variant
$query = "SELECT channel, quantity FROM inventory WHERE variant_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $variant_id);
$stmt->execute();
$result = $stmt->get_result();

$quantities = [];
while ($row = $result->fetch_assoc()) {
    $quantities[$row['channel']] = $row['quantity'];
}

echo json_encode($quantities);
