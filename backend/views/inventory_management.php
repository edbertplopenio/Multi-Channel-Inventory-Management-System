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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        <div id="all-inventory" class="tab-content active">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Price</th>
                        <th>Date Added</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example row -->
                    <tr>
                        <td>INV001</td>
                        <td>Item A</td>
                        <td>This is a sample description of Item A.</td>
                        <td>Category 1</td>
                        <td>50</td>
                        <td>M</td>
                        <td>Red</td>
                        <td>$25.00</td>
                        <td>2023-09-01</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
                        <td>
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <!-- Additional rows would go here -->
                </tbody>
            </table>
        </div>
    </div>

    <div id="new-item-container" class="new-item-container" style="display: none;">
        <div class="header">
            <h1>Add New Inventory Item</h1>
            <button class="back-button">← Back to Inventory</button>
        </div>

        <form id="new-item-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="product-id">Product ID:</label>
                    <input type="text" id="product-id" name="product-id" required>
                </div>

                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="category">Category:</label>
                    <input type="text" id="category" name="category" required>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="size">Size:</label>
                    <input type="text" id="size" name="size" required>
                </div>

                <div class="form-group">
                    <label for="color">Color:</label>
                    <input type="text" id="color" name="color" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="text" id="price" name="price" required>
                </div>

                <div class="form-group">
                    <label for="date-added">Date Added:</label>
                    <input type="date" id="date-added" name="date-added" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" id="image" name="image">
                </div>
            </div>

            <!-- Buttons side-by-side -->
            <div class="form-row buttons-row">
                <button type="button" class="cancel-button">Cancel</button>
                <button type="submit" class="save-item-button">Save Item</button>
            </div>
        </form>
    </div>

    <script>
        // Handle tab switching
        document.querySelectorAll('.tab').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                button.classList.add('active');
                document.getElementById(button.getAttribute('data-tab')).classList.add('active');
            });
        });

        // Handle switching to the new item form
        document.querySelector('.new-item-button').addEventListener('click', function() {
            document.querySelector('.inventory-container').style.display = 'none';
            document.getElementById('new-item-container').style.display = 'block';
        });

        // Handle going back to the inventory list
        document.querySelector('.back-button').addEventListener('click', function() {
            document.querySelector('.inventory-container').style.display = 'block';
            document.getElementById('new-item-container').style.display = 'none';
        });

        // Handle form submission (placeholder, you can replace with actual logic)
        document.getElementById('new-item-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form submission for now

            const productId = document.getElementById('product-id').value;
            const name = document.getElementById('name').value;
            const description = document.getElementById('description').value;
            const category = document.getElementById('category').value;
            const quantity = document.getElementById('quantity').value;
            const size = document.getElementById('size').value;
            const color = document.getElementById('color').value;
            const price = document.getElementById('price').value;
            const dateAdded = document.getElementById('date-added').value;
            const image = document.getElementById('image').files[0];

            // Placeholder for form handling, e.g., adding the new item to inventory
            alert(`Product ${name} added!`);

            document.getElementById('new-item-form').reset(); // Reset the form
            document.querySelector('.inventory-container').style.display = 'block';
            document.getElementById('new-item-container').style.display = 'none';
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
    height: 100vh;
    overflow: hidden;
}

.inventory-container, .new-item-container {
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
    margin-bottom: 30px;
}

.header h1 {
    font-size: 22px;
    color: #333;
    font-weight: 600;
}

.new-item-button {
    background-color: #007bff;
    color: #fff;
    padding: 6px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.new-item-button:hover {
    background-color: #0056b3;
}

.back-button {
    background-color: #007bff;
    color: #fff;
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.back-button:hover {
    background-color: #0056b3;
}

.filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
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

.inventory-table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    border-radius: 10px;
    overflow-y: auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    flex-grow: 1;
}

.inventory-table th, .inventory-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.inventory-table th {
    background-color: #f4f7fc;
    color: #555;
    font-size: 12px;
    font-weight: 600;
}

.inventory-table td {
    color: #555;
    font-size: 12px;
}

.inventory-table img {
    border-radius: 5px;
}

.inventory-table tr:last-child td {
    border-bottom: none;
}

.tab-content {
    display: none;
    padding-top: 20px;
}

.tab-content.active {
    display: block;
    height: 100%;
}

.action-button {
    background-color: #007bff;
    color: #fff;
    padding: 6px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    margin-right: 5px;
    transition: background-color 0.3s ease;
}

.action-button.edit {
    background-color: #ffc107;
}

.action-button.delete {
    background-color: #dc3545;
}

.action-button:hover {
    opacity: 0.9;
}

.new-item-container {
    padding: 20px;
    max-width: 1200px;
    height: 95vh;
    margin: 0 auto;
    background-color: #ffffff;
    box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.new-item-container form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    column-gap: 20px;
    row-gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

.form-group input {
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 12px;
    background-color: #f9f9f9;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    border-color: #007bff;
    outline: none;
}

.buttons-row {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.cancel-button, .save-item-button {
    padding: 12px 20px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.cancel-button {
    background-color: transparent;
    color: #5e5eff;
    border: 2px solid #5e5eff;
    width: 150px;
    text-align: center;
}

.cancel-button:hover {
    background-color: #f0f0ff;
}

.save-item-button {
    background-color: #5e5eff;
    color: white;
    width: 200px;
    text-align: center;
}

.save-item-button:hover {
    background-color: #3e3ecf;
}

</style>