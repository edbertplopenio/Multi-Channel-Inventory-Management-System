<?php
session_start();
require_once '../config/db_connection.php';  // Ensure this path is correct

error_reporting(E_ALL);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $size = $_POST['size'] ?? '';
    $color = $_POST['color'] ?? '';
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $existing_product_id = $_POST['existing_product_id'] ?? null;

    // Check if the date is provided and in correct format
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

    // Decode the channels JSON from the request
    $channels = isset($_POST['channels']) ? json_decode($_POST['channels'], true) : [];
    $quantity_physical_store = isset($_POST['quantity-physical-store']) ? (int)$_POST['quantity-physical-store'] : 0;
    $quantity_shopee = isset($_POST['quantity-shopee']) ? (int)$_POST['quantity-shopee'] : 0;
    $quantity_tiktok = isset($_POST['quantity-tiktok']) ? (int)$_POST['quantity-tiktok'] : 0;
    $total_quantity = $quantity_physical_store + $quantity_shopee + $quantity_tiktok;

    if ($total_quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Quantity must be greater than 0.']);
        exit();
    }

    $image_name = 'image-placeholder.png';  // Set a default value for image

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

    // Check if the product exists in the `products` table
    $sql_check_product = "SELECT product_id FROM products WHERE name = '$name' AND category = '$category' LIMIT 1";
    $result_check = mysqli_query($conn, $sql_check_product);
    
    if (mysqli_num_rows($result_check) > 0) {
        // If product exists, use the existing product_id
        $row = mysqli_fetch_assoc($result_check);
        $product_id = $row['product_id'];
    } else {
        // If product doesn't exist, insert it into the `products` table
        $sql_insert_product = "INSERT INTO products (name, category, base_price, image) 
                               VALUES ('$name', '$category', $price, '$image_name')";
        if (!mysqli_query($conn, $sql_insert_product)) {
            echo json_encode(['success' => false, 'message' => 'Failed to add product: ' . mysqli_error($conn)]);
            exit();
        }
        $product_id = mysqli_insert_id($conn); // Get the newly created product ID
    }

    // Start a transaction to ensure atomicity
    mysqli_begin_transaction($conn);

    try {
        // Insert into product_variants
        $sql = "INSERT INTO product_variants (product_id, size, color, price, date_added, image) 
                VALUES ('$product_id', '$size', '$color', $price, '$date_added', '$image_name')";
        if (!mysqli_query($conn, $sql)) {
            throw new Exception('Failed to add product variant: ' . mysqli_error($conn));
        }

        $variant_id = mysqli_insert_id($conn);  // Get the inserted variant ID

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
            'name' => $name, 
            'category' => $category, 
            'size' => $size, 
            'color' => $color, 
            'price' => $price, 
            'date_added' => $date_added, 
            'channels' => $channels, 
            'quantity_physical_store' => $quantity_physical_store,
            'quantity_shopee' => $quantity_shopee,
            'quantity_tiktok' => $quantity_tiktok,
            'total_quantity' => $total_quantity,
            'image' => $image_name
        ]);

    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    // Close the connection
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
