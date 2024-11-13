<?php
require_once '../../backend/config/db_connection.php';

$variantId = $_POST['variant_id'];
$name = $_POST['name'];
$category = $_POST['category'];
$size = $_POST['size'];
$color = $_POST['color'];
$price = $_POST['price'];
$image = $_FILES['image'] ?? null; // Handle file upload if provided

$response = ['success' => false, 'message' => 'Failed to update item.'];

// Retrieve product_id from product_variants to update name and category in the products table
$sqlGetProductId = "SELECT product_id FROM product_variants WHERE variant_id = ?";
$stmt = $conn->prepare($sqlGetProductId);
$stmt->bind_param("i", $variantId);
$stmt->execute();
$stmt->bind_result($productId);
$stmt->fetch();
$stmt->close();

// Update name and category in products table if product_id is found
if ($productId) {
    $sqlUpdateProduct = "UPDATE products SET name = ?, category = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sqlUpdateProduct);
    $stmt->bind_param("ssi", $name, $category, $productId);
    $stmt->execute();
    $stmt->close();
}

// Prepare SQL query to update product variant details in product_variants table
$sqlUpdateVariant = "UPDATE product_variants SET size = ?, color = ?, price = ? WHERE variant_id = ?";
$stmt = $conn->prepare($sqlUpdateVariant);
$stmt->bind_param("ssdi", $size, $color, $price, $variantId);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Item updated successfully.';
} else {
    $response['message'] = 'Database update failed.';
}
$stmt->close();

echo json_encode($response);
?>
