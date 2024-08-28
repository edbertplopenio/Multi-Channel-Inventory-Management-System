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
    <title>Inventory Management</title>
    <link rel="stylesheet" href="../../frontend/public/styles/inventory_management.css">
</head>
<body>

    <div class="dashboard-container">
        <div class="header">
            <h1>Inventory Management</h1>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h2>Inventory Overview</h2>
                <p>Details about current inventory...</p>
            </div>
            <div class="card">
                <h2>Stock Levels</h2>
                <p>Current stock levels and trends...</p>
            </div>
            <div class="card">
                <h2>Stock Management</h2>
                <p>Manage and update stock...</p>
            </div>
            <div class="card half-width">
                <h2>Inventory Reports</h2>
                <p>Generate and view reports on inventory...</p>
            </div>
        </div>
    </div>

</body>
</html>
