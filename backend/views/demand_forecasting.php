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
    <title>Demand Forecasting</title>
    <link rel="stylesheet" href="../../frontend/public/styles/dashboard.css">
    <link rel="stylesheet" href="../../frontend/public/styles/demand_forecasting.css">
</head>
<body>

    <div class="dashboard-container">
        <div class="header">
            <h1>Demand Forecasting</h1>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h2>Forecast Overview</h2>
                <p>Details about demand forecasting...</p>
            </div>
            <div class="card">
                <h2>Future Demand</h2>
                <p>Projections for future demand...</p>
            </div>
            <div class="card">
                <h2>Market Analysis</h2>
                <p>Insights from market analysis...</p>
            </div>
            <div class="card half-width">
                <h2>Forecast Reports</h2>
                <p>Generate and view forecast reports...</p>
            </div>
        </div>
    </div>

</body>
</html>
