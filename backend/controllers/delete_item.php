<?php
// Include database connection
include '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data (JSON)
    $data = json_decode(file_get_contents("php://input"), true);
    $productId = isset($data['product_id']) ? intval($data['product_id']) : 0;

    // Check if the product ID is valid
    if ($productId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
        exit;
    }

    // Check if the product exists before attempting to delete
    $checkProductQuery = "SELECT * FROM inventory WHERE product_id = ?";
    $stmt = $conn->prepare($checkProductQuery);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found!']);
    } else {
        // Delete product from inventory table
        $query = "DELETE FROM inventory WHERE product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $productId);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting product: ' . $stmt->error]);
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
