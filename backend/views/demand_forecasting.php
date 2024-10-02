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
    <link rel="stylesheet" href="../../frontend/public/styles/demand_forecasting.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>

    <div class="demand-forecasting-container">
        <div class="header">
            <h1>Demand Forecasting</h1>
        </div>

        <div class="filters">
            <div class="tabs-container">
                <button class="tab active" data-tab="physical-store">
                    <i class="fas fa-store"></i> Physical Store
                </button>
                <button class="tab" data-tab="shopee">
                    <i class="fas fa-shopping-cart"></i> Shopee
                </button>
                <button class="tab" data-tab="tiktok">
                    <i class="fas fa-music"></i> TikTok
                </button>
            </div>
            <div class="filter-input-container">
                <input type="text" class="filter-input" placeholder="Filter forecasts">
                <i class="fas fa-filter icon-filter"></i>
            </div>
        </div>

        <div class="forecast-content">
            <!-- Physical Store Content -->
            <div id="physical-store" class="tab-content active">
                <div class="forecast-details-container">
                    <div class="forecast-details">
                        <h2>Forecast Details</h2>
                        <p>Select a product to see detailed demand forecasting information here.</p>
                    </div>
                </div>

                <div class="forecast-table-container">
                    <div class="forecast-table-wrapper">
                        <table class="forecast-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PID001</td>
                                    <td>Product A</td>
                                </tr>
                                <!-- More rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shopee Content -->
            <div id="shopee" class="tab-content">
                <div class="forecast-details-container">
                    <div class="forecast-details">
                        <h2>Forecast Details</h2>
                        <p>Select a product to see detailed demand forecasting information here.</p>
                    </div>
                </div>
                <div class="forecast-table-container">
                    <div class="forecast-table-wrapper">
                        <table class="forecast-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PID002</td>
                                    <td>Product B</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TikTok Content -->
            <div id="tiktok" class="tab-content">
                <div class="forecast-details-container">
                    <div class="forecast-details">
                        <h2>Forecast Details</h2>
                        <p>Select a product to see detailed demand forecasting information here.</p>
                    </div>
                </div>
                <div class="forecast-table-container">
                    <div class="forecast-table-wrapper">
                        <table class="forecast-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PID003</td>
                                    <td>Product C</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle tab switching
        document.querySelectorAll('.tab').forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all tabs
                document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));

                // Hide all content sections
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                // Add active class to clicked tab and corresponding content
                button.classList.add('active');
                document.getElementById(button.getAttribute('data-tab')).classList.add('active');
            });
        });
    </script>

</body>

</html>

<style>
    @import url("https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700");

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Inter", sans-serif;
}

body {
  background-color: #f4f7fc;
  margin: 0;
  padding: 0;
  height: 100vh;
}

.demand-forecasting-container {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
  background-color: #ffffff;
  box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05);
  border-radius: 10px;
  height: 95vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.header h1 {
  font-size: 22px;
  color: #333;
  font-weight: 600;
}

.filters {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.tabs-container {
  display: flex;
  align-items: flex-end;
  gap: 5px;
}

.tab {
  padding: 8px 12px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 10px 10px 0 0;
  cursor: pointer;
  font-size: 12px;
  transition: background-color 0.3s, color 0.3s;
  font-weight: 500;
  box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
  z-index: 1;
  position: relative;
}

.tab.active {
  background-color: white;
  color: #007bff;
  z-index: 2;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
}

.tab i {
  margin-right: 8px;
}

.tab:hover {
  background-color: #0056b3;
}

.filter-input-container {
  display: flex;
  align-items: center;
  position: relative;
}

.filter-input {
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 20px;
  width: 220px;
  color: #333;
  font-size: 12px;
}

.icon-filter {
  position: absolute;
  right: 16px;
  color: #aaa;
}

.forecast-content {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  flex: 1;
  width: 100%;
  overflow: auto;
}

.forecast-details-container {
  width: 75%;
  background-color: #f4f7fc;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.05);
}

.forecast-details h2 {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 10px;
}

.forecast-details p {
  font-size: 14px;
  color: #555;
}

.forecast-table-container {
  width: 25%;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
}

.forecast-table-wrapper {
  overflow-y: auto;
  max-height: 600px; /* Adjust this value to control the height of the table container */
}

.forecast-table {
  width: 100%;
  border-collapse: collapse;
  background-color: #fff;
}

.forecast-table thead th {
  background-color: #f4f7fc;
  color: #555;
  font-size: 12px;
  font-weight: 600;
  position: sticky;
  top: 0; /* Ensures the header stays at the top */
  z-index: 2; /* Keeps header above scrolling content */
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.forecast-table tbody {
  display: block;
  overflow-y: auto;
  max-height: 450px; /* Adjust this value to control the height of the table body */
}

.forecast-table thead,
.forecast-table tbody tr {
  display: table;
  width: 100%;
  table-layout: fixed; /* Ensures columns in thead and tbody align */
}

.forecast-table tbody td {
  padding: 10px;
  font-size: 12px;
  color: #555;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.forecast-table tr:last-child td {
  border-bottom: none;
}

.tab-content {
  display: none;
  padding-top: 20px;
  width: 100%;
}

.tab-content.active {
  display: flex;
  gap: 20px;
}
</style>
