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
    <link rel="stylesheet" href="../../frontend/public/styles/sales_record.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>

    <div class="sales-record-container">
        <div class="header">
            <h1>Sales Record</h1>
            <button class="new-order-button">+ New sales order</button>
        </div>

        <div class="filters">
            <div class="tabs-container">
                <button class="tab active" data-tab="all-orders">
                    <i class="fas fa-list"></i> All orders
                </button>
                <button class="tab" data-tab="physical-store">
                    <i class="fas fa-store"></i> Physical Store
                </button>
                <button class="tab" data-tab="shopee">
                    <i class="fas fa-shopping-bag"></i> Shopee
                </button>
                <button class="tab" data-tab="tiktok">
                    <i class="fas fa-music"></i> TikTok
                </button>
            </div>
            <div class="filter-input-container">
                <input type="text" class="filter-input" placeholder="Filter sales orders">
                <i class="fas fa-filter icon-filter"></i>
            </div>
        </div>

        <!-- All Orders Content -->
        <div id="all-orders" class="tab-content active">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Sale Date</th>
                        <th>Quantity</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Quantity Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>PID001</td>
                        <td>Product A</td>
                        <td>2024-08-01</td>
                        <td>10</td>
                        <td>Category 1</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
                        <td>8</td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

        <!-- Physical Store Content -->
        <div id="physical-store" class="tab-content">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Sale Date</th>
                        <th>Quantity</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Quantity Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>PID002</td>
                        <td>Product B</td>
                        <td>2024-08-02</td>
                        <td>20</td>
                        <td>Category 2</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
                        <td>18</td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

        <!-- Shopee Content -->
        <div id="shopee" class="tab-content">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Sale Date</th>
                        <th>Quantity</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Quantity Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>PID003</td>
                        <td>Product C</td>
                        <td>2024-08-03</td>
                        <td>30</td>
                        <td>Category 3</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
                        <td>28</td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

        <!-- TikTok Content -->
        <div id="tiktok" class="tab-content">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Sale Date</th>
                        <th>Quantity</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Quantity Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>PID004</td>
                        <td>Product D</td>
                        <td>2024-08-04</td>
                        <td>15</td>
                        <td>Category 4</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
                        <td>12</td>
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
