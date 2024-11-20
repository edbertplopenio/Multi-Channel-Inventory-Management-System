<?php
include '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $cellphone = $_POST['cellphone']; // Added Cellphone
    $role = $_POST['role'];

    // Check if email already exists for other users
    $checkEmailQuery = "SELECT * FROM users WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param('si', $email, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists for another user!', 'id' => $id]);
        exit;
    }

    // Handle image upload if a file is provided
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_dir = '../../frontend/public/images/users/';
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($image_extension), $allowed_extensions)) {
            $new_image_name = uniqid('user_', true) . '.' . $image_extension;
            if (move_uploaded_file($image_tmp, $image_dir . $new_image_name)) {
                $image = $new_image_name;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.', 'id' => $id]);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid image format.', 'id' => $id]);
            exit;
        }
    }

    // Prepare the SQL query for updating user details
    if ($image) {
        $query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, cellphone = ?, role = ?, image = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssssi', $first_name, $last_name, $email, $cellphone, $role, $image, $id);
    } else {
        $query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, cellphone = ?, role = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssssi', $first_name, $last_name, $email, $cellphone, $role, $id);
    }

    if ($stmt->execute()) {
        $imagePath = $image ? "../../frontend/public/images/users/" . $image : null;
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'user' => [
                    'id' => $id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'cellphone' => $cellphone, // Added Cellphone
                    'role' => $role,
                    'image' => $imagePath
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No changes made.', 'id' => $id]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error, 'id' => $id]);
    }

    $stmt->close();
    $conn->close();
}
?>
