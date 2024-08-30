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
            <button class="new-forecast-button">+ New Forecast</button>
        </div>

        <div class="filters">
            <div class="tabs-container">
                <button class="tab active" data-tab="overview">
                    <i class="fas fa-chart-line"></i> Overview
                </button>
                <button class="tab" data-tab="future-demand">
                    <i class="fas fa-chart-bar"></i> Future Demand
                </button>
                <button class="tab" data-tab="market-analysis">
                    <i class="fas fa-chart-pie"></i> Market Analysis
                </button>
                <button class="tab" data-tab="reports">
                    <i class="fas fa-file-alt"></i> Reports
                </button>
            </div>
            <div class="filter-input-container">
                <input type="text" class="filter-input" placeholder="Filter forecasts">
                <i class="fas fa-filter icon-filter"></i>
            </div>
        </div>

        <!-- Overview Content -->
        <div id="overview" class="tab-content active">
            <table class="forecast-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Forecast Date</th>
                        <th>Projected Sales</th>
                        <th>Actual Sales</th>
                        <th>Accuracy (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>PID001</td>
                        <td>Product A</td>
                        <td>2024-08-01</td>
                        <td>100</td>
                        <td>95</td>
                        <td>95%</td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

        <!-- Future Demand Content -->
        <div id="future-demand" class="tab-content">
            <table class="forecast-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Forecast Date</th>
                        <th>Projected Sales</th>
                        <th>Accuracy (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>PID002</td>
                        <td>Product B</td>
                        <td>2024-08-02</td>
                        <td>120</td>
                        <td>98%</td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

        <!-- Market Analysis Content -->
        <div id="market-analysis" class="tab-content">
            <table class="forecast-table">
                <thead>
                    <tr>
                        <th>Market ID</th>
                        <th>Market Name</th>
                        <th>Analysis Date</th>
                        <th>Market Growth (%)</th>
                        <th>Projected Demand</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>MKT001</td>
                        <td>Market A</td>
                        <td>2024-08-03</td>
                        <td>15%</td>
                        <td>130</td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

        <!-- Reports Content -->
        <div id="reports" class="tab-content">
            <table class="forecast-table">
                <thead>
                    <tr>
                        <th>Report ID</th>
                        <th>Report Name</th>
                        <th>Generated Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>RPT001</td>
                        <td>Monthly Forecast Report</td>
                        <td>2024-08-04</td>
                        <td><span class="status completed">Completed</span></td>
                        <td>
                            <button class="action-button view"><i class="fas fa-eye"></i> View</button>
                            <button class="action-button download"><i class="fas fa-download"></i> Download</button>
                        </td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
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


