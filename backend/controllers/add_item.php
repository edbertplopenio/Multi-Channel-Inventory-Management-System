<?php
// Start the session
session_start();

// Include the database connection file
require_once '../config/db_connection.php';  // Update the path if necessary

// Disable error reporting for notices and warnings to avoid interfering with JSON output
error_reporting(E_ERROR | E_PARSE);

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data from the request
    $name = $_POST['name'];
    $category = $_POST['category'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $price = (float)$_POST['price']; // Ensure price is a float

    // Convert the date to the format MySQL expects (YYYY-MM-DD)
    $date_added = date('Y-m-d', strtotime($_POST['date_added']));

    // Log the date to confirm it's processed correctly
    error_log('Date Added: ' . $date_added);

    $channels = json_decode($_POST['channels'], true);  // Channels as a JSON string

    // Handle quantity for each channel
    $quantity_physical_store = isset($_POST['quantity-physical-store']) ? (int)$_POST['quantity-physical-store'] : 0;
    $quantity_shopee = isset($_POST['quantity-shopee']) ? (int)$_POST['quantity-shopee'] : 0;
    $quantity_tiktok = isset($_POST['quantity-tiktok']) ? (int)$_POST['quantity-tiktok'] : 0;

    // Calculate the total quantity
    $total_quantity = $quantity_physical_store + $quantity_shopee + $quantity_tiktok;

    // Validate that the total quantity is greater than 0
    if ($total_quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Quantity for at least one channel must be greater than 0.']);
        exit();
    }

    // Generate a unique product ID in the format "INV" + timestamp
    $product_id = 'INV' . time();

    // Handle image upload if an image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = $_FILES['image'];
        $image_name = time() . '_' . basename($image['name']);  // Generate unique image name
        $upload_dir = '../../frontend/public/images/';  // Define the image upload directory
        $image_path = $upload_dir . $image_name;

        // Move the uploaded file to the designated directory
        if (!move_uploaded_file($image['tmp_name'], $image_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
            exit();
        }
    } else {
        $image_name = null;  // If no image is provided
    }

    // Prepare an SQL query to insert the new item into the inventory
    $sql = "INSERT INTO inventory (product_id, name, category, size, color, price, date_added, quantity_physical_store, quantity_shopee, quantity_tiktok, quantity, image, channel)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $sql);

    // Check if statement preparation was successful
    if ($stmt) {
        // Convert the channels array to a JSON string for storage
        $channels_json = json_encode($channels);

        // Bind the parameters to the SQL query
        mysqli_stmt_bind_param($stmt, "sssssdiiiisss", 
            $product_id, $name, $category, $size, $color, $price, $date_added, 
            $quantity_physical_store, $quantity_shopee, $quantity_tiktok, $total_quantity, $image_name, $channels_json);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // If successful, return the inserted data along with the product_id and image_name for the frontend to use
            echo json_encode([
                'success' => true, 
                'message' => 'Item added successfully!',
                'product_id' => $product_id,
                'name' => $name,    // Returning the form values for table rendering
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
            echo json_encode(['success' => false, 'message' => 'Failed to add item.']);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: Failed to prepare SQL statement.']);
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
