<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../config/db_connection.php'); // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product-id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $price = $_POST['price'];
    $dateAdded = $_POST['date-added'];

    // Optional: Handle image file upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = basename($_FILES['image']['name']);
        $targetFilePath = "../../frontend/public/images/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath);
    }

    // Insert into the database
    $query = "INSERT INTO inventory (product_id, name, category, quantity, size, color, price, date_added, image) 
              VALUES ('$productId', '$name', '$category', '$quantity', '$size', '$color', '$price', '$dateAdded', '$image')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
}
?>
