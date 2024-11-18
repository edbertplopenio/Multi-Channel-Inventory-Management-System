<?php
include '../config/db_connection.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['first-name']);
    $lastName = trim($_POST['last-name']);
    $email = trim($_POST['email']);
    $cellphone = trim($_POST['cellphone']);
    $role = trim($_POST['role']);
    $password = $_POST['password'];

    if (empty($firstName) || empty($lastName) || empty($email) || empty($cellphone) || empty($role) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit();
    }

    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();
    if ($checkEmail->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email is already registered.']);
        exit();
    }
    $checkEmail->close();

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageDestination = '../../frontend/public/images/users/' . $imageName;

        if (!move_uploaded_file($imageTmpName, $imageDestination)) {
            echo json_encode(['status' => 'error', 'message' => 'Error uploading image.']);
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, cellphone, role, password, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssss", $firstName, $lastName, $email, $cellphone, $role, $hashedPassword, $imageName);

    if ($stmt->execute()) {
        $newUserId = $stmt->insert_id;
        $imageUrl = $imageName ? "../../frontend/public/images/users/" . $imageName : null;

        echo json_encode([
            'status' => 'success', 
            'message' => 'User added successfully',
            'user' => [
                'id' => $newUserId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'role' => $role,
                'image' => $imageUrl
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding user: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
