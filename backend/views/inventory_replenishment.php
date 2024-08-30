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
            <button class="new-order-button">+ New Order</button>
        </div>

        <div class="filters">
            <div class="tabs-container">
                <button class="tab active" data-tab="overview">
                    <i class="fas fa-box"></i> Overview
                </button>
                <button class="tab" data-tab="order-status">
                    <i class="fas fa-truck"></i> Order Status
                </button>
                <button class="tab" data-tab="suppliers">
                    <i class="fas fa-industry"></i> Suppliers
                </button>
                <button class="tab" data-tab="reports">
                    <i class="fas fa-file-alt"></i> Reports
                </button>
            </div>
            <div class="filter-input-container">
                <input type="text" class="filter-input" placeholder="Filter orders">
                <i class="fas fa-filter icon-filter"></i>
            </div>
        </div>

        <!-- Overview Content -->
        <div id="overview" class="tab-content active">
            <table class="replenishment-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Reorder Date</th>
                        <th>Quantity Ordered</th>
                        <th>Supplier</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>PID001</td>
                        <td>Product A</td>
                        <td>2024-08-01</td>
                        <td>100</td>
                        <td>Supplier X</td>
                        <td><span class="status pending">Pending</span></td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

        <!-- Order Status Content -->
        <div id="order-status" class="tab-content">
            <table class="replenishment-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product Name</th>
                        <th>Order Date</th>
                        <th>Expected Arrival</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>ORD001</td>
                        <td>Product B</td>
                        <td>2024-08-02</td>
                        <td>2024-08-10</td>
                        <td><span class="status shipped">Shipped</span></td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

        <!-- Suppliers Content -->
        <div id="suppliers" class="tab-content">
            <table class="replenishment-table">
                <thead>
                    <tr>
                        <th>Supplier ID</th>
                        <th>Supplier Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>SUP001</td>
                        <td>Supplier X</td>
                        <td>+1234567890</td>
                        <td>supplier@example.com</td>
                        <td><span class="status active">Active</span></td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

        <!-- Reports Content -->
        <div id="reports" class="tab-content">
            <table class="replenishment-table">
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
                        <td>Monthly Replenishment Report</td>
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

