<?php
// sse_sales_update.php
session_start();
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

require_once '../../backend/config/db_connection.php';

// Check if the user is logged in (optional, based on your authentication setup)
if (!isset($_SESSION['user_email'])) {
    echo "data: Unauthorized access\n\n";
    flush();
    exit();
}

// Continuously send data when new records are available
while (true) {
    // Get the most recent sales records (you can modify this query to fit your needs)
    $query = "SELECT * FROM sales ORDER BY sale_date DESC LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $sales_data = $result->fetch_assoc();
        $json_data = json_encode($sales_data);

        // Send data to the client (in the SSE format)
        echo "data: $json_data\n\n";
        flush();  // Ensure the data is sent to the client

        // Sleep for 1 second before checking for new data
        sleep(1);
    }
}

?>