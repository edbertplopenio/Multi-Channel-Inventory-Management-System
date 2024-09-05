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
                <!-- Forecast details container on the left -->
                <div class="forecast-details-container">
                    <div class="forecast-details">
                        <h2>Forecast Details</h2>
                        <p>Select a product to see detailed demand forecasting information here.</p>
                        <!-- Add more detailed forecasting info here -->
                    </div>
                </div>

                <!-- Table container on the right -->
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
                                <tr>
                                    <td>PID002</td>
                                    <td>Product B</td>
                                </tr>
                                <tr>
                                    <td>PID003</td>
                                    <td>Product C</td>
                                </tr>
                                <tr>
                                    <td>PID004</td>
                                    <td>Product D</td>
                                </tr>
                                <tr>
                                    <td>PID005</td>
                                    <td>Product E</td>
                                </tr>
                                <tr>
                                    <td>PID006</td>
                                    <td>Product F</td>
                                </tr>
                                <tr>
                                    <td>PID007</td>
                                    <td>Product G</td>
                                </tr>
                                <tr>
                                    <td>PID008</td>
                                    <td>Product H</td>
                                </tr>
                                <tr>
                                    <td>PID009</td>
                                    <td>Product I</td>
                                </tr>
                                <tr>
                                    <td>PID010</td>
                                    <td>Product J</td>
                                </tr>
                                <tr>
                                    <td>PID011</td>
                                    <td>Product K</td>
                                </tr>
                                <tr>
                                    <td>PID012</td>
                                    <td>Product L</td>
                                </tr>
                                <tr>
                                    <td>PID013</td>
                                    <td>Product M</td>
                                </tr>
                                <tr>
                                    <td>PID014</td>
                                    <td>Product N</td>
                                </tr>
                                <tr>
                                    <td>PID015</td>
                                    <td>Product O</td>
                                </tr>
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
                        <!-- Add more detailed forecasting info here -->
                    </div>
                </div>
                <div class="forecast-table-container">
                    <div class="forecast-table-wrapper">
                        <table class="forecast-table" style="width: 100%;">
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
                                <!-- Add more rows as needed -->
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
                        <!-- Add more detailed forecasting info here -->
                    </div>
                </div>
                <div class="forecast-table-container">
                    <div class="forecast-table-wrapper">
                        <table class="forecast-table" style="width: 100%;">
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
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // JavaScript to handle tab switching
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


