<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../frontend/public/styles/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Include your existing CSS here */
        .chart-placeholder {
            width: 100%;
            height: 100px;
            background-color: #f4f7fc;
            border-radius: 10px;
        }

        .chart-placeholder.large {
            height: 300px;
        }

        .card.full-width {
            grid-column: span 4;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Analytics</h1>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h2>Top Selling</h2>
                <p class="metric">Product A</p>
                <span class="metric-info"><i class="fas fa-arrow-up"></i> 25 units sold</span>
            </div>
            <div class="card">
                <h2>Low Stock</h2>
                <p class="metric">Product B</p>
                <span class="metric-info"><i class="fas fa-exclamation-triangle"></i> 5 units left</span>
            </div>
            <div class="card">
                <h2>Slow Moving</h2>
                <p class="metric">Product C</p>
                <span class="metric-info"><i class="fas fa-arrow-down"></i> 2 units sold this month</span>
            </div>
            <div class="card">
                <h2>Total Sales</h2>
                <p class="metric">$15,000</p>
                <span class="metric-info"><i class="fas fa-arrow-up"></i> 10% increase since last month</span>
            </div>

            <div class="card full-width">
                <h2>Sales Dynamics</h2>
                <canvas id="salesChart" class="chart-placeholder large"></canvas> <!-- Chart.js Canvas -->
            </div>

            <div class="card full-width">
                <h2>Overall User Activity</h2>
                <div class="chart-placeholder large"></div>
            </div>
        </div>
    </div>

    <script>
        // Dummy data for testing
        var salesData = [
            { date: '2024-10-01', sales_amount: 5000 },
            { date: '2024-10-02', sales_amount: 6000 },
            { date: '2024-10-03', sales_amount: 5500 },
            { date: '2024-10-04', sales_amount: 7000 },
            { date: '2024-10-05', sales_amount: 4500 },
            { date: '2024-10-06', sales_amount: 4000 },
            { date: '2024-10-07', sales_amount: 6500 }
        ];

        // Prepare data for the chart
        var labels = salesData.map(function(item) {
            return item.date;  // Dates from the dummy data
        });

        var salesAmounts = salesData.map(function(item) {
            return item.sales_amount;  // Sales amounts from the dummy data
        });

        // Create the chart
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'line',  // Line chart for sales over time
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sales Amount',
                    data: salesAmounts,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    lineTension: 0.1,  // Smoother line
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'category', // Category scale for dates
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Sales Amount'
                        },
                        beginAtZero: true  // Start Y-axis at 0
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                }
            }
        });
    </script>
</body>
</html>



<style>
    @import url('https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

body {
    background-color: #f4f7fc;
    margin: 0;
    padding: 0;
}

.dashboard-container {
    padding: 30px;
    max-width: 1400px;
    margin: 0 auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.header h1 {
    font-size: 32px;
    color: #333;
    font-weight: 600;
}

.date-picker {
    display: flex;
    gap: 10px;
}

.date-picker input[type="date"] {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.card {
    background-color: #fff;
    padding: 20px;
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    position: relative;
}

.card h2 {
    margin: 0 0 15px 0;
    font-size: 18px;
    color: #333;
}

.metric {
    font-size: 36px;
    font-weight: 600;
    margin: 0;
    color: #333;
}

.metric-info {
    font-size: 14px;
    color: #888;
    display: block;
    margin-top: 8px;
}

.chart-placeholder {
    width: 100%;
    height: 100px;
    background-color: #f4f7fc;
    border-radius: 10px;
}

.chart-placeholder.large {
    height: 200px;
}

.full-width {
    grid-column: span 4;
}

.half-width {
    grid-column: span 2;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table th,
.orders-table td {
    padding: 15px;
    text-align: left;
}

.orders-table th {
    background-color: #f4f7fc;
    color: #666;
    font-weight: 500;
    border-bottom: 2px solid #ddd;
}

.orders-table td {
    background-color: #fff;
    color: #333;
    border-bottom: 1px solid #eee;
}

.orders-table td img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
    vertical-align: middle;
}

</style>