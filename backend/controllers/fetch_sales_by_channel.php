<?php
// fetch_sales_by_channel.php
include_once '../config/db_connection.php'; // Adjust the path if needed.

$query = "
    SELECT channel, SUM(total_price) as total_sales
    FROM sales
    GROUP BY channel
";

$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[$row['channel']] = (float)$row['total_sales'];
}

echo json_encode($data);
?>
