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
    <title>Inventory Management</title>
    <link rel="stylesheet" href="../../frontend/public/styles/inventory_management.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>

    <div class="inventory-container">
        <div class="header">
            <h1>Inventory Management</h1>
            <button class="new-item-button">+ New Inventory Item</button>
        </div>

        <div class="filters">
            <div class="tabs-container">
                <button class="tab active" data-tab="all-inventory">
                    <i class="fas fa-warehouse"></i> All Inventory
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
                <input type="text" class="filter-input" placeholder="Filter inventory items">
                <i class="fas fa-filter icon-filter"></i>
            </div>
        </div>

        <!-- All Inventory Content -->
        <div id="all-inventory" class="tab-content active">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Stock Level</th>
                        <th>Category</th>
                        <th>Reorder Level</th>
                        <th>Supplier</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>INV001</td>
                        <td>Item A</td>
                        <td>50</td>
                        <td>Category 1</td>
                        <td>20</td>
                        <td>Supplier A</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

        <!-- Physical Store Content -->
        <div id="physical-store" class="tab-content">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Stock Level</th>
                        <th>Category</th>
                        <th>Reorder Level</th>
                        <th>Supplier</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>INV002</td>
                        <td>Item B</td>
                        <td>30</td>
                        <td>Category 2</td>
                        <td>15</td>
                        <td>Supplier B</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

        <!-- Shopee Content -->
        <div id="shopee" class="tab-content">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Stock Level</th>
                        <th>Category</th>
                        <th>Reorder Level</th>
                        <th>Supplier</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>INV003</td>
                        <td>Item C</td>
                        <td>20</td>
                        <td>Category 3</td>
                        <td>10</td>
                        <td>Supplier C</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

        <!-- TikTok Content -->
        <div id="tiktok" class="tab-content">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Stock Level</th>
                        <th>Category</th>
                        <th>Reorder Level</th>
                        <th>Supplier</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>INV004</td>
                        <td>Item D</td>
                        <td>0</td>
                        <td>Category 4</td>
                        <td>5</td>
                        <td>Supplier D</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
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
