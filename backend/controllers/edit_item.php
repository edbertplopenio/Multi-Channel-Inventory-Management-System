<?php
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

    // Optional: Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = basename($_FILES['image']['name']);
        $targetFilePath = "../../frontend/public/images/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath);
    }

    // Update the item in the database
    $query = "UPDATE inventory SET name='$name', category='$category', quantity='$quantity', size='$size', color='$color', 
              price='$price', date_added='$dateAdded', image='$image' WHERE product_id='$productId'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
}
?>
