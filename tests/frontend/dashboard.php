<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="your-custom-script.js?v=1.0"></script>



</head>

<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="header">
            <h1>Dashboard Overview</h1>
            <!-- Date Filter -->
            <div class="filter">
                <label for="start-date">Start Date: </label>
                <input type="date" id="start-date">
                <label for="end-date">End Date: </label>
                <input type="date" id="end-date">
                <button onclick="applyFilter()">Apply Filter</button>
                <button onclick="clearFilter()">Clear</button>
            </div>

        </div>

        <!-- Redesigned Small Cards -->
        <div class="card top-selling">
            <div class="icon">📦</div>
            <h2>Top Selling</h2>
            <p class="metric">Product A</p>
            <div class="trend">
                <span>+10% ↑</span> increase
            </div>
        </div>
        <div class="card low-stock">
            <div class="icon">⚠️</div>
            <h2>Low Stock</h2>
            <p class="metric">Product B</p>
            <div class="trend">
                <span>-50% ↓</span> remaining
            </div>
        </div>
        <div class="card slow-moving">
            <div class="icon">🐢</div>
            <h2>Slow Moving</h2>
            <p class="metric">Product C</p>
            <div class="trend">
                <span>-5% ↓</span> this month
            </div>
        </div>
        <div class="card total-sales">
            <div class="icon">💵</div>
            <h2>Total Sales</h2>
            <p class="metric">$15,000</p>
            <div class="trend">
                <span>+20% ↑</span> growth
            </div>
        </div>

        <!-- Other Cards -->
        <div class="card card-sales-dynamic">
            <h2>Sales Dynamics</h2>
            <canvas id="salesDynamicChart"></canvas>
        </div>
        <div class="card card-sales-by-category">
            <h2>Sales by Category</h2>
            <canvas id="salesByCategoryChart"></canvas>
        </div>
        <div class="card card-channels">
            <h2>Sales by Channel</h2>
            <canvas id="salesByChannelChart"></canvas>
        </div>
        <div class="card card-top-selling">
            <h2>Top Selling Products</h2>
            <div class="table-container">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Product A</td>
                            <td>150</td>
                            <td>$4,500</td>
                        </tr>
                        <tr>
                            <td>Product B</td>
                            <td>120</td>
                            <td>$3,600</td>
                        </tr>
                        <tr>
                            <td>Product C</td>
                            <td>80</td>
                            <td>$2,400</td>
                        </tr>
                        <tr>
                            <td>Product D</td>
                            <td>50</td>
                            <td>$1,500</td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
        </div>

        <script>
 document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('salesDynamicChart').getContext('2d');
    const salesDynamicChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: 'Sales Revenue ($)',
                data: [12000, 15000, 13000, 16000, 17000, 14500, 18000],
                borderColor: '#5bc0f8',
                backgroundColor: 'rgba(91, 192, 248, 0.2)',
                borderWidth: 2,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#48a7d7',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: '#333',
                        font: {
                            size: 12
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#555',
                        font: {
                            size: 10
                        }
                    }
                },
                y: {
                    grid: {
                        color: '#eee'
                    },
                    ticks: {
                        color: '#555',
                        font: {
                            size: 10
                        }
                    }
                }
            }
        }
    });
});

</script>


</body>

</html>











<!-- General CSS -->

<style>
    /* General Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        background-color: #f4f7fc;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        overflow: hidden;
    }

    .dashboard-container {
        width: 100%;
        height: 95%;
        display: grid;
        grid-template-rows: auto 1fr 1fr;
        grid-template-columns: repeat(12, minmax(80px, 1fr));
        gap: 15px;
        padding: 20px;
    }

    /* Header */
    .header {
        grid-column: span 12;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 10px;
    }

    .header h1 {
        font-size: 28px;
        font-weight: 600;
        color: #333;
    }

    /* Redesigned Small Cards */
    .card {
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
    }

    .top-selling,
    .low-stock,
    .slow-moving,
    .total-sales {
        height: 125px;
        /* Adjust the height value as needed */
    }


    .card h2 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #7F7F7F;
    }

    .card .metric {
        font-size: 32px;
        font-weight: 700;
    }

    .card .trend {
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        margin-top: 10px;
    }

    .card .trend span {
        margin-left: 5px;
    }

    .card .icon {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 40px;
        opacity: 0.5;
    }

    /* Individual Backgrounds for Small Cards */
    .top-selling {
        background: linear-gradient(145deg, #5bc0f8, #48a7d7);
        grid-column: span 3;
    }

    .low-stock {
        background: linear-gradient(145deg, #ff9999, #ff7373);
        grid-column: span 3;
    }

    .slow-moving {
        background: linear-gradient(145deg, #ffd699, #ffb347);
        grid-column: span 3;
    }

    .total-sales {
        background: linear-gradient(145deg, #a89cf3, #8e83f0);
        grid-column: span 3;
    }

    /* Larger Cards */
    .card-sales-dynamic {
        grid-column: span 8;
        grid-row: span 1;
        height: 245px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card-sales-by-category {
        grid-column: span 4;
        grid-row: span 1;
        height: 245px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card-channels {
        grid-column: span 6;
        grid-row: span 2;
        height: 200px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card-top-selling {
        grid-column: span 6;
        grid-row: span 2;
        height: 200px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Table Styles for Top Selling Products */
    .orders-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 14px;
    }

    .orders-table th,
    .orders-table td {
        padding: 10px;
        text-align: left;
    }

    .orders-table th {
        background-color: #f4f7fc;
        color: #555;
        font-weight: 600;
        border-bottom: 2px solid #ddd;
    }

    .orders-table td {
        background-color: #fff;
        color: #333;
        border-bottom: 1px solid #eee;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .dashboard-container {
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
        }

        .top-selling,
        .low-stock,
        .slow-moving,
        .total-sales {
            grid-column: span 6;
        }

        .card-sales-dynamic,
        .card-sales-by-category,
        .card-channels,
        .card-top-selling {
            grid-column: span 6;
        }
    }

    @media (max-width: 480px) {
        .dashboard-container {
            grid-template-columns: repeat(2, 1fr);
        }

        .card {
            grid-column: span 2;
            height: auto;
        }

        .card-sales-dynamic,
        .card-sales-by-category,
        .card-channels,
        .card-top-selling {
            grid-column: span 2;
        }
    }
</style>




<!-- CSS for filter -->
<style>
    /* Filter Section Styles */
    .filter {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px 20px;
        background: rgba(255, 255, 255, 0.15);
        /* Semi-transparent background */
        border-radius: 12px;
        /* Rounded corners */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        /* Subtle shadow for pop-out effect */
        backdrop-filter: blur(15px);
        /* Glass blur effect */
        -webkit-backdrop-filter: blur(15px);
        /* Safari support */
        border: 1px solid rgba(255, 255, 255, 0.3);
        /* Glassy border */
    }

    /* Labels for the Filter */
    .filter label {
        font-weight: 600;
        font-size: 14px;
        color: #7F7F7F;
        /* White text for contrast */
    }

    /* Input Fields (Date Pickers) */
    .filter input[type="date"] {
        padding: 6px 12px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        /* Glassy border */
        border-radius: 4px;
        font-size: 14px;
        color: #7F7F7F;
        /* White text */
        background-color: rgba(255, 255, 255, 0.2);
        /* Transparent input background */
        outline: none;
        transition: border-color 0.2s, background-color 0.2s;
    }

    .filter input[type="date"]:hover {
        background-color: rgba(255, 255, 255, 0.3);
        /* Highlight on hover */
        border-color: #007bff;
    }

    .filter input[type="date"]:focus {
        border-color: #007bff;
        background-color: rgba(255, 255, 255, 0.3);
        /* Highlight on focus */
    }

    /* Button Styles */
    .filter button {
        padding: 6px 12px;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        /* Button text color */
        background-color: rgba(0, 123, 255, 0.8);
        /* Semi-transparent blue button */
        border: 1px solid rgba(255, 255, 255, 0.5);
        /* Glassy border for buttons */
        border-radius: 4px;
        /* Rounded corners */
        cursor: pointer;
        transition: background-color 0.2s, transform 0.1s;
    }

    .filter button:hover {
        background-color: rgba(0, 86, 179, 0.8);
        /* Darker blue on hover */
    }

    .filter button:active {
        transform: scale(0.98);
        /* Subtle press animation */
    }

    /* Clear Button Styles */
    .filter button:last-child {
        background-color: rgba(255, 255, 255, 0.2);
        /* Glassy button for clear */
        color: #007bff;
        /* Blue text for distinction */
        border: 1px solid rgba(0, 123, 255, 0.3);
        /* Slightly bluish border */
    }

    .filter button:last-child:hover {
        background-color: rgba(255, 255, 255, 0.3);
        /* Lighter on hover */
        color: #0056b3;
    }

    .filter button:last-child:active {
        transform: scale(0.98);
        /* Subtle press animation */
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .filter {
            flex-wrap: wrap;
            /* Allow wrapping for smaller screens */
            justify-content: space-between;
        }

        .filter input[type="date"],
        .filter button {
            flex: 1;
            /* Allow inputs and buttons to adjust width */
            min-width: 150px;
            /* Set a reasonable minimum width */
            margin: 5px 0;
            /* Add some vertical margin */
        }
    }
</style>

<!-- Css for top selling table -->
 <style>
    /* Table Styles for Top Selling Products */
    .orders-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 10px;
        /* Smaller text for a clean look */
        table-layout: fixed;
        /* Fixed column widths */
    }

    /* Table Header */
    .orders-table th {
        background: linear-gradient(145deg, #f4f7fc, #e9eef8);
        /* Soft gradient */
        color: #555;
        /* Subtle text color */
        font-weight: 600;
        font-size: 12px;
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #d1d9e6;
        /* Subtle bottom border */
        position: sticky;
        top: 0;
        /* Keep header fixed */
        z-index: 2;
        /* Ensure header stays above content */
    }

    /* Table Rows */
    .orders-table td {
        font-size: 10px;
        padding: 10px;
        text-align: left;
        background-color: #ffffff;
        /* Clean white background for rows */
        color: #333;
        /* Professional dark text */
        border-bottom: 1px solid #eee;
        /* Light border between rows */
        word-wrap: break-word;
        /* Prevent overflow */
    }

    /* Table Row Hover */
    .orders-table tr:hover td {
        background-color: #f1f6fc;
        /* Highlight row on hover */
    }

    /* Alternate Row Colors for Better Readability */
    .orders-table tbody tr:nth-child(odd) td {
        background-color: #f9fbfd;
        /* Subtle alternate row background */
    }

    /* Table Container with Scroll */
    .table-container {
        max-height: 300px;
        /* Set height for scrollable area */
        overflow-y: auto;
        /* Enable vertical scrolling */
        overflow-x: hidden;
        /* Prevent horizontal scrolling */
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {

        .orders-table th,
        .orders-table td {
            font-size: 8px;
            /* Smaller text for smaller screens */
        }
    }
</style>