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
    <title>Inventory Replenishment</title>
    <link rel="stylesheet" href="../../frontend/public/styles/dashboard.css">
    <link rel="stylesheet" href="../../frontend/public/styles/inventory_replenishment.css">
</head>
<body>

    <div class="dashboard-container">
        <div class="header">
            <h1>Inventory Replenishment</h1>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h2>Replenishment Overview</h2>
                <p>Details about inventory replenishment...</p>
            </div>
            <div class="card">
                <h2>Order Status</h2>
                <p>Current status of replenishment orders...</p>
            </div>
            <div class="card">
                <h2>Supplier Information</h2>
                <p>Details about suppliers and contacts...</p>
            </div>
            <div class="card half-width">
                <h2>Replenishment Reports</h2>
                <p>Generate and view replenishment reports...</p>
            </div>
        </div>
    </div>

</body>
</html>
