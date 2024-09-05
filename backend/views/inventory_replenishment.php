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
    <title>Inventory Replenishment</title>
    <link rel="stylesheet" href="../../frontend/public/styles/inventory_replenishment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>

    <div class="inventory-replenishment-container">
        <div class="header">
            <h1>Inventory Replenishment</h1>
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
                <input type="text" class="filter-input" placeholder="Filter inventory">
                <i class="fas fa-filter icon-filter"></i>
            </div>
        </div>

        <div class="inventory-content">
            <!-- Physical Store Content -->
            <div id="physical-store" class="tab-content active">
                <!-- Replenishment details container on the left -->
                <div class="inventory-details-container">
                    <div class="inventory-details">
                        <h2>Replenishment Details</h2>
                        <p>Select a product to see detailed inventory replenishment information here.</p>
                        <!-- Add more detailed replenishment info here -->
                    </div>
                </div>

                <!-- Table container on the right -->
                <div class="inventory-table-container">
                    <div class="inventory-table-wrapper">
                        <table class="inventory-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Stock Level</th>
                                    <th>Reorder Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PID001</td>
                                    <td>Product A</td>
                                    <td>50</td>
                                    <td>20</td>
                                </tr>
                                <tr>
                                    <td>PID002</td>
                                    <td>Product B</td>
                                    <td>30</td>
                                    <td>15</td>
                                </tr>
                                <tr>
                                    <td>PID003</td>
                                    <td>Product C</td>
                                    <td>70</td>
                                    <td>25</td>
                                </tr>
                                <tr>
                                    <td>PID004</td>
                                    <td>Product D</td>
                                    <td>15</td>
                                    <td>10</td>
                                </tr>
                                <tr>
                                    <td>PID005</td>
                                    <td>Product E</td>
                                    <td>5</td>
                                    <td>3</td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shopee Content -->
            <div id="shopee" class="tab-content">
                <div class="inventory-details-container">
                    <div class="inventory-details">
                        <h2>Replenishment Details</h2>
                        <p>Select a product to see detailed inventory replenishment information here.</p>
                        <!-- Add more detailed replenishment info here -->
                    </div>
                </div>
                <div class="inventory-table-container">
                    <div class="inventory-table-wrapper">
                        <table class="inventory-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Stock Level</th>
                                    <th>Reorder Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PID002</td>
                                    <td>Product B</td>
                                    <td>30</td>
                                    <td>15</td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TikTok Content -->
            <div id="tiktok" class="tab-content">
                <div class="inventory-details-container">
                    <div class="inventory-details">
                        <h2>Replenishment Details</h2>
                        <p>Select a product to see detailed inventory replenishment information here.</p>
                        <!-- Add more detailed replenishment info here -->
                    </div>
                </div>
                <div class="inventory-table-container">
                    <div class="inventory-table-wrapper">
                        <table class="inventory-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Stock Level</th>
                                    <th>Reorder Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PID003</td>
                                    <td>Product C</td>
                                    <td>70</td>
                                    <td>25</td>
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


