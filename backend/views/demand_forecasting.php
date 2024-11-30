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
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <div class="demand-forecasting-container">
        <div class="header">
            <h1>Demand Forecasting</h1>
        </div>

        <div class="filters">
            <div class="tabs-container">
                <!-- New All Inventory Tab -->
                <button class="tab active" data-tab="all-inventory">
                    <i class="fas fa-boxes"></i> All Inventory
                </button>
                <button class="tab" data-tab="physical-store">
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
            <!-- All Inventory Content -->
            <div id="all-inventory" class="tab-content active">
                <div class="forecast-details-container">
                    <div class="forecast-details">
<h2 id="forecastTitle">Select a product to see detailed demand forecasting information here.</h2>
<p id="forecastDescription"></p>

                        <canvas id="allInventoryChart"></canvas>
                    </div>
                </div>

                <div class="forecast-table-container">
                    <div class="forecast-table-wrapper">
                        <table class="forecast-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Total Stock</th>
                                    <th>Predicted Demand</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Physical Store Content -->
            <div id="physical-store" class="tab-content">
                <div class="forecast-details-container">
                    <div class="forecast-details">
                    <h2 id="forecastTitle">Select a product to see detailed demand forecasting information here.</h2>
<p id="forecastDescription"></p>
                        <canvas id="physicalStoreChart"></canvas>
                    </div>
                </div>

                <div class="forecast-table-container">
                    <div class="forecast-table-wrapper">
                        <table class="forecast-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Total Stock</th>
                                    <th>Predicted Demand</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shopee Content -->
            <div id="shopee" class="tab-content">
                <div class="forecast-details-container">
                    <div class="forecast-details">
                    <h2 id="forecastTitle">Select a product to see detailed demand forecasting information here.</h2>
<p id="forecastDescription"></p>
                        <canvas id="shopeeChart"></canvas>
                    </div>
                </div>
                <div class="forecast-table-container">
                    <div class="forecast-table-wrapper">
                        <table class="forecast-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Total Stock</th>
                                    <th>Predicted Demand</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TikTok Content -->
            <div id="tiktok" class="tab-content">
                <div class="forecast-details-container">
                    <div class="forecast-details">
                    <h2 id="forecastTitle">Select a product to see detailed demand forecasting information here.</h2>
<p id="forecastDescription"></p>
                        <canvas id="tiktokChart"></canvas>
                    </div>
                </div>
                <div class="forecast-table-container">
                    <div class="forecast-table-wrapper">
                        <table class="forecast-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Total Stock</th>
                                    <th>Predicted Demand</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
    // Event listener for filter input
    $('.filter-input').on('input', function() {
        const filterValue = $(this).val().toLowerCase();  // Get the filter value and convert to lowercase
        const tab = $('.tab.active').data('tab'); // Get the active tab

        // Loop through each row in the table of the active tab
        $(`#${tab} .forecast-table tbody tr`).each(function() {
            const productName = $(this).find('td:nth-child(2)').text().toLowerCase(); // Get the product name in lowercase
            const productId = $(this).find('td:first').text().toLowerCase(); // Get the product ID in lowercase

            // Check if the filter value is included in the product name or product ID
            if (productName.includes(filterValue) || productId.includes(filterValue)) {
                $(this).show();  // Show the row if it matches the filter
            } else {
                $(this).hide();  // Hide the row if it doesn't match
            }
        });
    });
});

    </script>


    <!-- Chart Initialization Script -->
    <script>
        let allInventoryChart, physicalStoreChart, shopeeChart, tiktokChart;

        function initializeCharts() {
            const chartOptions = {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                            label: 'Actual Sales',
                            data: [],
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Predicted Demand',
                            data: [],
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Sales Amount'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Time'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Sales and Predicted Demand'
                        }
                    }
                }
            };

            allInventoryChart = new Chart(document.getElementById('allInventoryChart'), JSON.parse(JSON.stringify(chartOptions)));
            physicalStoreChart = new Chart(document.getElementById('physicalStoreChart'), JSON.parse(JSON.stringify(chartOptions)));
            shopeeChart = new Chart(document.getElementById('shopeeChart'), JSON.parse(JSON.stringify(chartOptions)));
            tiktokChart = new Chart(document.getElementById('tiktokChart'), JSON.parse(JSON.stringify(chartOptions)));
        }

        initializeCharts();
    </script>


    <!-- jQuery Library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Data Fetching and Chart Updating Script -->
    <script>
    // Function to fetch and update chart data
    function updateChart(tab, productId) {
        $.ajax({
            url: '../../backend/controllers/fetch_demand_forecasting_table.php', // Corrected path
            type: 'GET',
            data: {
                product_id: productId,
                tab: tab
            },
            dataType: 'json', // Ensure the response is parsed as JSON
            success: function(response) {
                // Check if response contains the necessary data
                if (!response.labels || !response.actual_sales || !response.predicted_demand) {
                    console.error('Invalid response format:', response);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Received invalid data format from the server.',
                        icon: 'error',
                    });
                    return;
                }

                let chart;

                switch (tab) {
                    case 'all-inventory':
                        chart = allInventoryChart;
                        break;
                    case 'physical-store':
                        chart = physicalStoreChart;
                        break;
                    case 'shopee':
                        chart = shopeeChart;
                        break;
                    case 'tiktok':
                        chart = tiktokChart;
                        break;
                    default:
                        return;
                }

                chart.data.labels = response.labels;
                chart.data.datasets[0].data = response.actual_sales;
                chart.data.datasets[1].data = response.predicted_demand;
                chart.update();
            },
            error: function(xhr, status, error) {
                console.error(`Error fetching forecast for product ${productId} in tab ${tab}:`, xhr.responseText || error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while fetching the forecast data. Please try again later.',
                    icon: 'error',
                });
            }
        });
    }

    $(document).ready(function() {
        // Handle tab switching
        $('.tab').on('click', function() {
            const tab = $(this).data('tab');

            // Remove active class from all tabs and contents
            $('.tab').removeClass('active');
            $('.tab-content').removeClass('active');

            // Add active class to the clicked tab and the corresponding content
            $(this).addClass('active');
            $(`#${tab}`).addClass('active');

            // Load data for the selected tab
            loadDemandData(tab);
        });

        // Load data for the default active tab on page load
        const defaultTab = $('.tab.active').data('tab') || 'all-inventory';
        $(`#${defaultTab}`).addClass('active'); // Ensure the content is active
        loadDemandData(defaultTab);

        // Delegate click event to dynamically added table rows with row selection
        $('.forecast-table').on('click', 'tr', function() {
            $('.forecast-table tr').removeClass('selected');
            $(this).addClass('selected');

            const tab = $('.tab.active').data('tab');  // Get the active tab
            const productId = $(this).find('td:first').text().trim();  // Get the Product ID
            const productName = $(this).find('td:nth-child(2)').text().trim();  // Get the Product Name

            if (productId) {
                // Update the header with the product name in the correct tab
                $(`#${tab} #forecastTitle`).text(`Demand Forecasting for ${productName}`);

                // Update the chart with the selected product data
                updateChart(tab, productId);
            }
        });
    });
</script>




    <!-- Styling for Selected Rows -->
    <style>
        .forecast-table tr.selected {
            background-color: #f0f8ff;
        }
    </style>

    <!-- Loading Table Data Script -->
    <script>
        // Function to load demand data for a specific tab
        function loadDemandData(tab) {
            $.ajax({
                url: '../../backend/controllers/fetch_demand_forecasting_table.php',
                type: 'GET',
                data: {
                    tab: tab
                },
                dataType: 'json', // Ensure the response is parsed as JSON
                success: function(response) {
                    const tbody = $(`#${tab} .forecast-table tbody`);
                    tbody.empty(); // Clear existing rows

                    if (Array.isArray(response) && response.length > 0) {
                        response.forEach((item) => {
                            tbody.append(`
                        <tr>
                            <td>${item.product_id}</td>
                            <td>${item.product_name}</td>
                            <td>${item.total_stock}</td>
                            <td>${item.predicted_demand}</td>
                        </tr>
                    `);
                        });
                    } else {
                        // Add a placeholder row if no data is available
                        tbody.append(`
                    <tr>
                        <td colspan="4" style="text-align: center;">No data available</td>
                    </tr>
                `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(`Error fetching data for tab ${tab}:`, xhr.responseText || error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while fetching demand data. Please try again later.',
                        icon: 'error',
                    });
                },
            });
        }
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
        width: 30%;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    .forecast-table-wrapper {
        overflow-y: auto;
        max-height: 600px;
        /* Adjust this value to control the height of the table container */
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
        top: 0;
        /* Ensures the header stays at the top */
        z-index: 2;
        /* Keeps header above scrolling content */
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .forecast-table tbody {
        display: block;
        overflow-y: auto;
        max-height: 450px;
        /* Adjust this value to control the height of the table body */
    }

    .forecast-table thead,
    .forecast-table tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        /* Ensures columns in thead and tbody align */
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


<style>
    /* Hide Product ID, Total Stock, and Predicted Demand columns */
    .forecast-table th:nth-child(1),
    /* Product ID */
    .forecast-table th:nth-child(3),
    /* Total Stock */
    .forecast-table th:nth-child(4)

    /* Predicted Demand */
        {
        display: none;
    }

    .forecast-table td:nth-child(1),
    /* Product ID */
    .forecast-table td:nth-child(3),
    /* Total Stock */
    .forecast-table td:nth-child(4)

    /* Predicted Demand */
        {
        display: none;
    }
</style>