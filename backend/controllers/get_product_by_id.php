<?php
require_once '../config/db_connection.php';

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Query to get product details
    $sql = "SELECT name, category, price, image, size, color FROM inventory WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // Get all existing sizes and colors for this product
        $sizes = [];
        $colors = [];
        $sql_variants = "SELECT size, color FROM inventory WHERE name = ?";
        $stmt_variants = $conn->prepare($sql_variants);
        $stmt_variants->bind_param('s', $product['name']);
        $stmt_variants->execute();
        $result_variants = $stmt_variants->get_result();
        
        while ($row = $result_variants->fetch_assoc()) {
            $sizes[] = $row['size'];
            $colors[] = $row['color'];
        }

        echo json_encode([
            'success' => true,
            'product' => [
                'name' => $product['name'],
                'category' => $product['category'],
                'price' => $product['price'],
                'image' => $product['image'],
                'sizes' => $sizes,
                'colors' => $colors
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
