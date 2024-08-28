<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: ../../frontend/public/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="../../frontend/public/styles/dashboard.css">
    <link rel="stylesheet" href="../../frontend/public/styles/user_management.css">
</head>
<body>

    <div class="dashboard-container">
        <div class="header">
            <h1>User Management</h1>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h2>User Overview</h2>
                <p>Details about user management...</p>
            </div>
            <div class="card">
                <h2>Manage Roles</h2>
                <p>Assign and manage user roles...</p>
            </div>
            <div class="card">
                <h2>User Permissions</h2>
                <p>Overview of user permissions...</p>
            </div>
            <div class="card half-width">
                <h2>User Activity</h2>
                <p>View and track user activity...</p>
            </div>
        </div>
    </div>

</body>
</html>
