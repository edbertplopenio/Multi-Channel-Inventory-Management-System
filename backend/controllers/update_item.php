<?php
require_once '../../backend/config/db_connection.php';

$response = ['success' => false, 'message' => ''];

// Check if the required fields are set
if (isset($_POST['variant_id'], $_POST['name'], $_POST['category'], $_POST['size'], $_POST['color'], $_POST['price'], $_POST['date_added'])) {
    
    $variant_id = $_POST['variant_id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $price = $_POST['price'];
    $date_added = $_POST['date_added'];

    // Quantities for each channel
    $quantity_physical_store = $_POST['quantity_physical_store'] ?? 0;
    $quantity_shopee = $_POST['quantity_shopee'] ?? 0;
    $quantity_tiktok = $_POST['quantity_tiktok'] ?? 0;

    // Default to using the current image if no new image is uploaded
    $imageName = $_POST['current_image'] ?? '';

    // Image upload handling (if a new image is provided)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $imagePath = "../../frontend/public/images/" . $imageName;

        // Move uploaded file to target directory and log outcome
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            error_log("Image uploaded successfully: " . $imageName);
        } else {
            $response['message'] = "Error uploading image.";
            error_log("Failed to upload image: " . $_FILES['image']['error']);
            echo json_encode($response);
            exit;
        }
    }

    // Step 1: Get product_id from product_variants table to update products
    $product_id_query = "SELECT product_id FROM product_variants WHERE variant_id = ?";
    $stmt = $conn->prepare($product_id_query);
    if (!$stmt) {
        $response['message'] = "SQL Error: " . $conn->error;
        echo json_encode($response);
        exit;
    }
    $stmt->bind_param("i", $variant_id);
    $stmt->execute();
    $stmt->bind_result($product_id);
    $stmt->fetch();
    $stmt->close();

    if (!$product_id) {
        $response['message'] = "Invalid variant ID.";
        echo json_encode($response);
        exit;
    }

    // Step 2: Update products table with new name and category
    $updateProductQuery = "UPDATE products SET name = ?, category = ? WHERE product_id = ?";
    $productStmt = $conn->prepare($updateProductQuery);
    if (!$productStmt) {
        $response['message'] = "SQL Error: " . $conn->error;
        echo json_encode($response);
        exit;
    }
    $productStmt->bind_param("ssi", $name, $category, $product_id);
    if ($productStmt->execute()) {
        error_log("Product updated with name and category.");
    } else {
        error_log("Failed to update product: " . $productStmt->error);
    }
    $productStmt->close();

    // Step 3: Update product_variants table with other details and image if provided
    $updateQuery = "
        UPDATE product_variants 
        SET size = ?, color = ?, price = ?, date_added = ?, image = IF(? != '', ?, image)
        WHERE variant_id = ?";
    
    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) {
        $response['message'] = "SQL Error: " . $conn->error;
        echo json_encode($response);
        exit;
    }
    $stmt->bind_param("ssdsisi", $size, $color, $price, $date_added, $imageName, $imageName, $variant_id);
    if ($stmt->execute()) {
        error_log("Product variant updated with image: " . $imageName);
    } else {
        error_log("Failed to update product variant: " . $stmt->error);
    }
    $stmt->close();

    // Step 4: Update quantities in the inventory table
    $updateInventoryQuery = "
        UPDATE inventory
        SET quantity = CASE channel
            WHEN 'physical_store' THEN ? 
            WHEN 'shopee' THEN ? 
            WHEN 'tiktok' THEN ? 
            ELSE quantity END
        WHERE variant_id = ?";
    $inventoryStmt = $conn->prepare($updateInventoryQuery);
    if (!$inventoryStmt) {
        $response['message'] = "Inventory SQL Error: " . $conn->error;
        echo json_encode($response);
        exit;
    }
    $inventoryStmt->bind_param("iiis", $quantity_physical_store, $quantity_shopee, $quantity_tiktok, $variant_id);
    if ($inventoryStmt->execute()) {
        error_log("Inventory quantities updated successfully for variant ID: " . $variant_id);
        $response['success'] = true;

        // Include the image URL in the response to update the frontend
        $response['new_image_url'] = "../../frontend/public/images" . $imageName;
    } else {
        error_log("Failed to update inventory quantities: " . $inventoryStmt->error);
    }
    $inventoryStmt->close();

} else {
    $response['message'] = "Missing required fields.";
}

$conn->close();
echo json_encode($response);
?>