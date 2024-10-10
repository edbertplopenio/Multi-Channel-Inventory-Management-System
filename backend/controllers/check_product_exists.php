<?php
require_once '../config/db_connection.php';  // Update the path if necessary

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);  // Read JSON input
    $name = $data['name'];

    // Query to check if product exists
    $sql = "SELECT product_id FROM inventory WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch the existing product ID
        $stmt->bind_result($product_id);
        $stmt->fetch();
        echo json_encode(['exists' => true, 'product_id' => $product_id]);
    } else {
        echo json_encode(['exists' => false]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
