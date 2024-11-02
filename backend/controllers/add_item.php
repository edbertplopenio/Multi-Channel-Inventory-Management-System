<?php
session_start();
require_once '../config/db_connection.php';  // Ensure this path is correct

error_reporting(E_ALL);
ini_set('display_errors', 1); // Display errors for debugging

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and assign variables
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? '');
    $size = mysqli_real_escape_string($conn, $_POST['size'] ?? '');
    $color = mysqli_real_escape_string($conn, $_POST['color'] ?? '');
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $existing_product_id = $_POST['existing_product_id'] ?? null;

    // Validate and format date
    if (!empty($_POST['date_added'])) {
        $date_added = date('Y-m-d', strtotime($_POST['date_added']));
        if (!$date_added || $date_added === '1970-01-01') {
            echo json_encode(['success' => false, 'message' => 'Invalid date format']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Date added is required.']);
        exit();
    }

    // Parse the channels JSON array (ensure it's correctly formatted in the request)
    $channels = isset($_POST['channels']) ? $_POST['channels'] : [];
    if (!is_array($channels)) {
        echo json_encode(['success' => false, 'message' => 'Invalid channel data.']);
        exit();
    }

    // Quantities for each channel
    $quantity_physical_store = isset($_POST['quantity-physical-store']) ? (int)$_POST['quantity-physical-store'] : 0;
    $quantity_shopee = isset($_POST['quantity-shopee']) ? (int)$_POST['quantity-shopee'] : 0;
    $quantity_tiktok = isset($_POST['quantity-tiktok']) ? (int)$_POST['quantity-tiktok'] : 0;
    $total_quantity = $quantity_physical_store + $quantity_shopee + $quantity_tiktok;

    if ($total_quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Quantity must be greater than 0.']);
        exit();
    }

    // Default image placeholder
    $image_name = 'image-placeholder.png';

    // Handle the image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = $_FILES['image'];
        $image_name = time() . '_' . basename($image['name']);
        $upload_dir = '../../frontend/public/images/';
        $image_path = $upload_dir . $image_name;
        if (!move_uploaded_file($image['tmp_name'], $image_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
            exit();
        }
    }

    // Check if the product already exists in the `products` table
    $sql_check_product = "SELECT product_id FROM products WHERE name = '$name' AND category = '$category' LIMIT 1";
    $result_check = mysqli_query($conn, $sql_check_product);
    
    if (mysqli_num_rows($result_check) > 0) {
        $row = mysqli_fetch_assoc($result_check);
        $product_id = $row['product_id'];
    } else {
        $sql_insert_product = "INSERT INTO products (name, category, base_price, image) 
                               VALUES ('$name', '$category', $price, '$image_name')";
        if (!mysqli_query($conn, $sql_insert_product)) {
            echo json_encode(['success' => false, 'message' => 'Failed to add product: ' . mysqli_error($conn)]);
            exit();
        }
        $product_id = mysqli_insert_id($conn);
    }

    // Begin a transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert into product_variants
        $sql = "INSERT INTO product_variants (product_id, size, color, price, date_added, image) 
                VALUES ('$product_id', '$size', '$color', $price, '$date_added', '$image_name')";
        if (!mysqli_query($conn, $sql)) {
            throw new Exception('Failed to add product variant: ' . mysqli_error($conn));
        }

        $variant_id = mysqli_insert_id($conn);

        // Insert into inventory
        $sql_inventory = "INSERT INTO inventory (variant_id, channel, quantity) VALUES 
                          ($variant_id, 'physical_store', $quantity_physical_store),
                          ($variant_id, 'shopee', $quantity_shopee),
                          ($variant_id, 'tiktok', $quantity_tiktok)";
        if (!mysqli_query($conn, $sql_inventory)) {
            throw new Exception('Failed to add inventory: ' . mysqli_error($conn));
        }

// Commit the transaction
mysqli_commit($conn);

echo json_encode([
    'success' => true,
    'product_id' => $product_id,
    'variant_id' => $variant_id, // Make sure this is returned
    'name' => $name, 
    'category' => $category, 
    'size' => $size, 
    'color' => $color, 
    'price' => $price, 
    'date_added' => $date_added, 
    'channels' => $channels, 
    'quantity_physical_store' => $quantity_physical_store, // Ensure this is accurate
    'quantity_shopee' => $quantity_shopee,                 // Ensure this is accurate
    'quantity_tiktok' => $quantity_tiktok,                 // Ensure this is accurate
    'total_quantity' => $total_quantity,
    'image' => $image_name
]);



    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_rollback($conn);
        error_log($e->getMessage()); // Log the error message
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    // Close the connection
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
