<?php
session_start();
require_once '../config/db_connection.php';  // Update the path if necessary

error_reporting(E_ERROR | E_PARSE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $price = (float)$_POST['price'];

    // Check if the date is provided, otherwise handle the error
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

    $channels = json_decode($_POST['channels'], true);
    $quantity_physical_store = isset($_POST['quantity-physical-store']) ? (int)$_POST['quantity-physical-store'] : 0;
    $quantity_shopee = isset($_POST['quantity-shopee']) ? (int)$_POST['quantity-shopee'] : 0;
    $quantity_tiktok = isset($_POST['quantity-tiktok']) ? (int)$_POST['quantity-tiktok'] : 0;
    $total_quantity = $quantity_physical_store + $quantity_shopee + $quantity_tiktok;

    if ($total_quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Quantity must be greater than 0.']);
        exit();
    }

    $product_id = 'INV' . time();

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
    } else {
        $image_name = null;
    }

    // SQL Insert Query (without prepared statement)
    $channels_json = json_encode($channels);
    $sql = "INSERT INTO inventory (product_id, name, category, size, color, price, date_added, quantity_physical_store, quantity_shopee, quantity_tiktok, quantity, image, channel)
            VALUES ('$product_id', '$name', '$category', '$size', '$color', $price, '$date_added', $quantity_physical_store, $quantity_shopee, $quantity_tiktok, $total_quantity, '$image_name', '$channels_json')";

    // Execute the SQL Query
    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Item added successfully!',
            'product_id' => $product_id,
            'name' => $name,
            'category' => $category,
            'size' => $size,
            'color' => $color,
            'price' => $price,
            'date_added' => $date_added,
            'quantity_physical_store' => $quantity_physical_store,
            'quantity_shopee' => $quantity_shopee,
            'quantity_tiktok' => $quantity_tiktok,
            'image_name' => $image_name
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add item: ' . mysqli_error($conn)]);
    }

    // Close the connection
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
