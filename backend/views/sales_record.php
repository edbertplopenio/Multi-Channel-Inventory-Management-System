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
            <button class="new-order-button">+ New Sales Order</button>
        </div>

        <div class="filters">
            <div class="tabs-container">
                <button class="tab active" data-tab="all-orders">
                    <i class="fas fa-list"></i> All Orders
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
                        <th>Stock Level</th>
                        <th>Cost</th>
                        <th>Quantity Sold</th>
                        <th>Sales Channel</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>PID001</td>
                        <td>Product A</td>
                        <td>2024-08-01</td>
                        <td>100</td>
                        <td>$50.00</td>
                        <td>10</td>
                        <td>Shopee</td>
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
                        <th>Stock Level</th>
                        <th>Cost</th>
                        <th>Quantity Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows for Physical Store -->
                    <tr>
                        <td>PID002</td>
                        <td>Product B</td>
                        <td>2024-08-02</td>
                        <td>50</td>
                        <td>$30.00</td>
                        <td>5</td>
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
                        <th>Stock Level</th>
                        <th>Cost</th>
                        <th>Quantity Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows for Shopee -->
                    <tr>
                        <td>PID003</td>
                        <td>Product C</td>
                        <td>2024-08-03</td>
                        <td>200</td>
                        <td>$40.00</td>
                        <td>15</td>
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
                        <th>Stock Level</th>
                        <th>Cost</th>
                        <th>Quantity Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows for TikTok -->
                    <tr>
                        <td>PID004</td>
                        <td>Product D</td>
                        <td>2024-08-04</td>
                        <td>150</td>
                        <td>$60.00</td>
                        <td>8</td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>

    </div>

    <!-- New Sales Order Modal -->
    <div id="new-order-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <div class="header">
                <h1>Add New Sales Order</h1>
            </div>

            <form id="new-order-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="product-id">Product ID:</label>
                        <input type="text" id="product-id" name="product-id" required>
                    </div>

                    <div class="form-group">
                        <label for="name">Product Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="sale-date">Sale Date:</label>
                        <input type="date" id="sale-date" name="sale-date" required>
                    </div>

                    <div class="form-group">
                        <label for="stock-level">Stock Level:</label>
                        <input type="number" id="stock-level" name="stock-level" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cost">Cost:</label>
                        <input type="number" id="cost" name="cost" required>
                    </div>

                    <div class="form-group">
                        <label for="quantity-sold">Quantity Sold:</label>
                        <input type="number" id="quantity-sold" name="quantity-sold" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="sales-channel">Sales Channel:</label>
                        <input type="text" id="sales-channel" name="sales-channel" required>
                    </div>
                </div>

                <!-- Buttons side-by-side -->
                <div class="form-row buttons-row">
                    <button type="button" class="cancel-button">Cancel</button>
                    <button type="submit" class="save-order-button">Save Order</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function initializeSalesRecord() {
            // Handle tab switching
            document.querySelector('.tabs-container').addEventListener('click', function(event) {
                if (event.target.classList.contains('tab')) {
                    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    event.target.classList.add('active');
                    document.getElementById(event.target.getAttribute('data-tab')).classList.add('active');
                }
            });

            // Show the modal when the "New Sales Order" button is clicked
            const modal = document.getElementById("new-order-modal");
            const newOrderButton = document.querySelector(".new-order-button");
            const closeButton = document.querySelector(".close-button");

            newOrderButton.addEventListener('click', function() {
                modal.style.display = "flex"; // Display modal
            });

            // Close the modal when the close button is clicked
            closeButton.addEventListener('click', function() {
                modal.style.display = "none"; // Hide modal
            });

            // Close the modal if the user clicks outside of it
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });

            // Handle form submission (placeholder, you can replace with actual logic)
            document.getElementById('new-order-form').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent form submission for now

                const productId = document.getElementById('product-id').value;
                const name = document.getElementById('name').value;
                const saleDate = document.getElementById('sale-date').value;
                const stockLevel = document.getElementById('stock-level').value;
                const cost = document.getElementById('cost').value;
                const quantitySold = document.getElementById('quantity-sold').value;
                const salesChannel = document.getElementById('sales-channel').value;

                // Placeholder for form handling, e.g., adding the new sales order
                alert(`Order for ${name} added!`);

                document.getElementById('new-order-form').reset(); // Reset the form
                modal.style.display = "none"; // Hide modal
            });
        }

        // Call the initialization function when the page loads or when entering the section
        initializeSalesRecord();
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
    height: 100vh;
}

.sales-record-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
    background-color: #ffffff;
    box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05);
    border-radius: 10px;
    height: 95vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.header h1 {
    font-size: 22px;
    color: #333;
    font-weight: 600;
}

.new-order-button {
    background-color: #007bff;
    color: #fff;
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.new-order-button:hover {
    background-color: #0056b3;
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
    width: 200px;
    color: #333;
    font-size: 12px;
}

.icon-filter {
    position: absolute;
    right: 16px;
    color: #aaa;
}

.sales-table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.sales-table th, .sales-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.sales-table th {
    background-color: #f4f7fc;
    color: #555;
    font-size: 12px;
    font-weight: 600;
}

.sales-table td {
    color: #555;
    font-size: 12px;
}

.sales-table tr:last-child td {
    border-bottom: none;
}

/* Hidden by default, shown when active */
.tab-content {
    display: none;
    padding-top: 20px;
    height: 100%;
}

.tab-content.active {
    display: block;
}

.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1000; /* Ensure it's above everything else */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Background overlay */
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #ffffff;
    padding: 15px; /* Reduced modal padding */
    border-radius: 10px;
    box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
    width: 40%; /* Reduced modal width */
}

.close-button {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
}

.close-button:hover {
    color: #ff0000;
}

.new-order-container form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    column-gap: 20px;
    row-gap: 20px;
    padding: 20px 0;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 600;
    color: #333;
    font-size: 12px;
}

.form-group input {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 12px;
    background-color: #f9f9f9;
    transition: border-color 0.3s;
}

.form-group input:focus {
    border-color: #007bff;
    outline: none;
}

.buttons-row {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.cancel-button, .save-order-button {
    padding: 10px 15px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.cancel-button {
    background-color: transparent;
    color: #007bff;
    border: 2px solid #007bff;
    width: 200px;
}

.cancel-button:hover {
    background-color: #f0f0ff;
}

.save-order-button {
    background-color: #007bff;
    color: white;
    width: 200px;
}

.save-order-button:hover {
    background-color: #0056b3;
}
</style>
