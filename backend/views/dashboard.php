<?php
session_start();

// Check if the user is logged in
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
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../frontend/public/styles/dashboard.css">
</head>
<body>

    <div class="dashboard-container">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?>!</h1>
            <p>Your role is: <?php echo htmlspecialchars($_SESSION['user_role']); ?></p>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h2>Total Revenue</h2>
                <p>$XX,XXX</p>
            </div>
            <div class="card">
                <h2>Invoices</h2>
                <p>X,XXX</p>
            </div>
            <div class="card">
                <h2>Clients</h2>
                <p>X,XXX</p>
            </div>
            <div class="card">
                <h2>Loyalty</h2>
                <p>XX%</p>
            </div>
            <div class="card">
                <h2>Monthly Revenue</h2>
                <p>$XX,XXX</p>
            </div>
            <div class="card">
                <h2>Activities</h2>
                <p>Recent activity details...</p>
            </div>
            <div class="card">
                <h2>Recent Invoices</h2>
                <p>Invoice details...</p>
            </div>
            <div class="card">
                <h2>New Features</h2>
                <p>Details about new features...</p>
            </div>
            <div class="card half-width">
                <h2>Support</h2>
                <p>Contact our support team...</p>
            </div>
            <div class="card half-width">
                <h2>Notifications</h2>
                <p>Check your latest notifications...</p>
            </div>
        </div>
    </div>

</body>
</html>
