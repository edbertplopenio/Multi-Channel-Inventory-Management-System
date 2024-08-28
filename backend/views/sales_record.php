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
    <title>Sales Record</title>
    <link rel="stylesheet" href="../../frontend/public/styles/dashboard.css">
    <link rel="stylesheet" href="../../frontend/public/styles/sales_record.css">
</head>
<body>

    <div class="dashboard-container">
        <div class="header">
            <h1>Sales Record</h1>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h2>Sales Overview</h2>
                <p>Details about sales records...</p>
            </div>
            <div class="card">
                <h2>Recent Transactions</h2>
                <p>List of recent sales transactions...</p>
            </div>
            <div class="card">
                <h2>Sales by Region</h2>
                <p>Analysis of sales by region...</p>
            </div>
            <div class="card half-width">
                <h2>Sales Reports</h2>
                <p>Generate and view detailed sales reports...</p>
            </div>
        </div>
    </div>

</body>
</html>
