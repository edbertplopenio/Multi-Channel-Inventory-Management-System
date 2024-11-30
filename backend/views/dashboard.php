<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>




</head>

<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="header">
            <h1>Dashboard Overview</h1>
            <!-- Date Filter -->
            <!-- <div class="filter">
                <label for="start-date">Start Date: </label>
                <input type="date" id="start-date">
                <label for="end-date">End Date: </label>
                <input type="date" id="end-date">
                <button onclick="applyFilter()">Apply Filter</button>
                <button onclick="clearFilter()">Clear</button>
            </div> -->

        </div>

        <!-- Redesigned Small Cards -->
        <div class="card top-selling">
            <div class="icon">📦</div>
            <h2>Top Selling</h2>
            <p class="metric"></p> <!-- Empty initially, will be populated by JS -->
            <div class="trend">
                <span></span> <!-- Empty initially, will be populated by JS -->
            </div>
        </div>

        <div class="card low-stock">
            <div class="icon">⚠️</div>
            <h2>Low Stock</h2>
            <p class="metric"></p> <!-- Empty initially, will be populated by JS -->
            <div class="trend">
                <span></span> <!-- Empty initially, will be populated by JS -->
            </div>
        </div>

        <div class="card slow-moving">
            <div class="icon">🐢</div>
            <h2>Slow Moving</h2>
            <p class="metric"></p> <!-- Empty initially, will be populated by JS -->
            <div class="trend">
                <span></span> <!-- Empty initially, will be populated by JS -->
            </div>
        </div>

        <div class="card total-sales">
            <div class="icon">💵</div>
            <h2>Total Sales</h2>
            <p class="metric"></p> <!-- Empty initially, will be populated by JS -->
            <div class="trend">
                <span></span> <!-- Empty initially, will be populated by JS -->
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
                        </tr>
                    </thead>
                    <tbody id="top-selling-products">
                        <!-- Data will be inserted dynamically here -->
                    </tbody>
                </table>
            </div>
        </div>


        <script>
            // Apply Filter Function
function applyFilter() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;

    // Validate the date range
    if (!startDate || !endDate) {
        alert("Please select both start and end dates.");
        return;
    }

    // Call the functions to fetch the data based on the filter
    fetchMetrics(startDate, endDate);
    fetchTopSellingProducts(startDate, endDate);
    fetchSalesByCategory(startDate, endDate);
    fetchSalesByChannel(startDate, endDate);
}

// Clear Filter Function
function clearFilter() {
    // Clear the filter inputs
    document.getElementById('start-date').value = '';
    document.getElementById('end-date').value = '';

    // Call the functions to fetch data without the filter
    fetchMetrics();
    fetchTopSellingProducts();
    fetchSalesByCategory();
    fetchSalesByChannel();
}

        </script>


        <script>
            // Function to fetch product metrics (Top Selling, Low Stock, Slow Moving, Total Sales)
            function fetchMetrics() {
                // Make AJAX call to PHP script that fetches the product metrics data
                $.ajax({
                    url: '../../backend/controllers/fetch_metrics.php', // Change this path if needed
                    method: 'GET',
                    success: function(data) {
                        // Handle top-selling data
                        if (data.top_selling && data.top_selling.length > 0) {
                            const topSellingProduct = data.top_selling[0]; // Get the top-selling product
                            $('.top-selling .metric').text(topSellingProduct.name);
                            $('.top-selling .trend span').text(`+${topSellingProduct.total_sales} ↑ increase`);
                        }

                        // Handle low-stock data
                        if (data.low_stock && data.low_stock.length > 0) {
                            const lowStockProduct = data.low_stock[0]; // Get the low-stock product
                            $('.low-stock .metric').text(lowStockProduct.name);
                            $('.low-stock .trend span').text(`${lowStockProduct.total_quantity} remaining`);
                        } else {
                            // Handle case when there are no low-stock products
                            $('.low-stock .metric').text('No low-stock products');
                            $('.low-stock .trend span').text('N/A');
                        }

                        // Handle slow-moving data
                        if (data.slow_moving && data.slow_moving.length > 0) {
                            const slowMovingProduct = data.slow_moving[0]; // Get the slow-moving product
                            $('.slow-moving .metric').text(slowMovingProduct.name);
                            $('.slow-moving .trend span').text(`${slowMovingProduct.total_sales} sold this month`);
                        }

                        // Handle total sales data
                        if (data.total_sales !== undefined) {
                            $('.total-sales .metric').text(`₱${parseFloat(data.total_sales).toLocaleString()}`);
                            $('.total-sales .trend span').text(`+20% ↑ growth`); // Adjust growth as needed
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching metrics data:', error);
                    }
                });
            }

            // Fetch metrics when the page is ready
            $(document).ready(function() {
                fetchMetrics();
            });
        </script>




        <script>
            // Function to fetch top selling products from the backend
            function fetchTopSellingProducts() {
                // Example of a PHP endpoint that returns the top-selling products data
                $.ajax({
                    url: '../../backend/controllers/fetch_top_selling.php', // Change this to your PHP endpoint
                    method: 'GET',
                    success: function(data) {
                        const products = JSON.parse(data); // Assuming the response is in JSON format
                        const tbody = document.getElementById('top-selling-products');
                        tbody.innerHTML = ''; // Clear existing rows

                        // Limit to 5 products
                        const top5Products = products.slice(0, 5);

                        if (top5Products.length > 0) {
                            // Loop through the top 5 products data and create table rows
                            top5Products.forEach(product => {
                                const row = document.createElement('tr');
                                const productCell = document.createElement('td');
                                productCell.textContent = product.product_name || 'No name'; // Debug check for product name

                                const unitsSoldCell = document.createElement('td');
                                unitsSoldCell.textContent = product.units_sold;

                                row.appendChild(productCell);
                                row.appendChild(unitsSoldCell);
                                tbody.appendChild(row);
                            });
                        } else {
                            const row = document.createElement('tr');
                            const noDataCell = document.createElement('td');
                            noDataCell.colSpan = 2; // Adjust colspan to match the new number of columns
                            noDataCell.textContent = "No data available";
                            row.appendChild(noDataCell);
                            tbody.appendChild(row);
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching top selling products:', error);
                    }
                });
            }

            // Fetch top selling products when the page is ready
            $(document).ready(function() {
                fetchTopSellingProducts();
            });
        </script>




        <!-- JS for Sales by category -->
        <script>
            function initializeCategoryChart(salesData) {
                const ctx = document.getElementById('salesByCategoryChart')?.getContext('2d');
                if (ctx) {
                    // Extract category names and sales data from the fetched data
                    const labels = salesData.map(item => item.category);
                    const data = salesData.map(item => item.total_sales);

                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels, // Dynamic category names
                            datasets: [{
                                label: 'Sales by Category',
                                data: data, // Dynamic sales data
                                backgroundColor: [
                                    '#5bc0f8', // Electronics
                                    '#ff7373', // Furniture
                                    '#ffd699', // Clothing
                                    '#a89cf3', // Books
                                    '#8e83f0' // Others
                                ],
                                borderWidth: 1,
                                hoverOffset: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    align: 'start',
                                    labels: {
                                        color: '#333',
                                        font: {
                                            size: 12,
                                            weight: 'normal'
                                        },
                                        padding: 10,
                                        usePointStyle: true
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            const value = tooltipItem.raw;
                                            return `₱${value.toLocaleString()}`; // Format as PHP currency
                                        }
                                    }
                                }

                            },
                            layout: {
                                padding: {
                                    top: 10,
                                    bottom: 10,
                                    left: 10,
                                    right: 10
                                }
                            }
                        }
                    });
                } else {
                    console.warn('Canvas for category chart not found.');
                }
            }

            // Fetch data from the backend and initialize the chart
            $(document).ready(function() {
                $.ajax({
                    url: '../../backend/controllers/fetch_sales_by_category.php', // Your backend endpoint
                    method: 'GET',
                    success: function(response) {
                        const salesData = JSON.parse(response);
                        initializeCategoryChart(salesData); // Pass the data to the chart function
                    },
                    error: function(error) {
                        console.error('Error fetching sales data:', error);
                    }
                });
            });
        </script>


        <!-- JS for sales by channel -->
        <script>
            function initializeChannelChart() {
                const ctx = document.getElementById('salesByChannelChart')?.getContext('2d');

                if (ctx) {
                    // Fetch data from the server
                    $.ajax({
                        url: '../../backend/controllers/fetch_sales_by_channel.php', // Adjust the path if necessary
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            // Map response to chart data
                            const channels = ['physical_store', 'shopee', 'tiktok'];
                            const salesData = channels.map(channel => response[channel] || 0); // Default to 0 if no data

                            // Initialize the chart with fetched data
                            new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: ['Physical Store', 'Shopee', 'TikTok'], // Channels
                                    datasets: [{
                                        label: 'Sales by Channel (₱)',
                                        data: salesData, // Use dynamic data
                                        backgroundColor: [
                                            '#48a7d7', // Physical Store
                                            '#ff7373', // Shopee
                                            '#ffd699' // TikTok
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'right',
                                            align: 'start',
                                            labels: {
                                                generateLabels: function(chart) {
                                                    const data = chart.data;
                                                    return data.labels.map((label, index) => ({
                                                        text: label, // Only show the channel name, no sales amount
                                                        fillStyle: data.datasets[0].backgroundColor[index],
                                                        hidden: chart.getDatasetMeta(0).data[index].hidden,
                                                        lineWidth: 1,
                                                        strokeStyle: data.datasets[0].backgroundColor[index],
                                                        pointStyle: 'circle'
                                                    }));
                                                },
                                                color: '#333',
                                                font: {
                                                    size: 12,
                                                    weight: 'normal'
                                                },
                                                padding: 15,
                                                usePointStyle: true
                                            }
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(tooltipItem) {
                                                    const value = tooltipItem.raw;
                                                    return `₱${value.toLocaleString()}`; // Format as Philippine Peso
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
                                                    size: 12
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
                                                    size: 12
                                                },
                                                callback: function(value) {
                                                    return `₱${value}`; // Format as Philippine Peso
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        },
                        error: function(error) {
                            console.error('Error fetching sales data:', error);
                        }
                    });
                } else {
                    console.warn('Canvas for channel chart not found.');
                }
            }

            // Ensure the chart is initialized after loading
            $(document).ready(function() {
                initializeChannelChart();
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

    /* Top Selling Card */
    .card.top-selling h2 {
        color: #ffffff;
        /* White text for the h2 element */
    }

    /* Low Stock Card */
    .card.low-stock h2 {
        color: #ffffff;
        /* White text for the h2 element */
    }

    /* Slow Moving Card */
    .card.slow-moving h2 {
        color: #ffffff;
        /* White text for the h2 element */
    }

    /* Total Sales Card */
    .card.total-sales h2 {
        color: #ffffff;
        /* White text for the h2 element */
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
        color: #333;
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
        top: 5px;
        right: 20px;
        font-size: 40px;
        opacity: 0.8;
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