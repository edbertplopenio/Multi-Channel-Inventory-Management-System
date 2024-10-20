<?php
require_once '../config/db_connection.php';  // Update the path if necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'];

    // Query to check if product exists and fetch related data from products table
    $sql = "SELECT product_id, category, base_price FROM products WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch the existing product ID, category, and price
        $stmt->bind_result($product_id, $category, $price);
        $stmt->fetch();

        // Query to get the existing sizes and colors for variants
        $sql_variants = "SELECT DISTINCT size, color FROM product_variants WHERE product_id = ?";
        $stmt_variants = $conn->prepare($sql_variants);
        $stmt_variants->bind_param('s', $product_id);
        $stmt_variants->execute();
        $result_variants = $stmt_variants->get_result();

        $existing_sizes = [];
        $existing_colors = [];

        while ($row = $result_variants->fetch_assoc()) {
            $existing_sizes[] = $row['size'];
            $existing_colors[] = $row['color'];
        }

        echo json_encode([
            'exists' => true, 
            'product_id' => $product_id, 
            'category' => $category, 
            'price' => $price, 
            'existing_sizes' => $existing_sizes, 
            'existing_colors' => $existing_colors
        ]);
    } else {
        echo json_encode(['exists' => false]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
