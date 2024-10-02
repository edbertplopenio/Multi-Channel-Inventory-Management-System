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

        <div id="physical-store" class="tab-content">
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
                    <tr>
                        <td>INV002</td>
                        <td>Item B</td>
                        <td>This is a sample description of Item B.</td>
                        <td>Category 2</td>
                        <td>30</td>
                        <td>L</td>
                        <td>Blue</td>
                        <td>$15.00</td>
                        <td>2023-09-02</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
                        <td>
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="shopee" class="tab-content">
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
                    <tr>
                        <td>INV003</td>
                        <td>Item C</td>
                        <td>This is a sample description of Item C.</td>
                        <td>Category 3</td>
                        <td>20</td>
                        <td>S</td>
                        <td>Green</td>
                        <td>$30.00</td>
                        <td>2023-09-03</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
                        <td>
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="tiktok" class="tab-content">
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
                    <tr>
                        <td>INV004</td>
                        <td>Item D</td>
                        <td>This is a sample description of Item D.</td>
                        <td>Category 4</td>
                        <td>15</td>
                        <td>XL</td>
                        <td>Yellow</td>
                        <td>$40.00</td>
                        <td>2023-09-04</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
                        <td>
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- New Item Modal -->
    <div id="new-item-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <div class="header">
                <h1>Add New Inventory Item</h1>
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
    </div>

    <script>
        function initializeInventoryManagement() {
            // Handle tab switching with event delegation
            document.querySelector('.tabs-container').addEventListener('click', function(event) {
                if (event.target.classList.contains('tab')) {
                    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    event.target.classList.add('active');
                    document.getElementById(event.target.getAttribute('data-tab')).classList.add('active');
                }
            });

            // Show the modal when the "New Inventory Item" button is clicked
            const modal = document.getElementById("new-item-modal");
            const newItemButton = document.querySelector(".new-item-button");
            const closeButton = document.querySelector(".close-button");

            newItemButton.addEventListener('click', function() {
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
            document.getElementById('new-item-form').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent form submission for now

                const productId = document.getElementById('product-id').value;
                const name = document.getElementById('name').value;
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
                modal.style.display = "none"; // Hide modal
            });
        }

        // Call the initialization function when the page loads or when entering the section
        initializeInventoryManagement();
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
    margin: 0 auto;
    background-color: #ffffff;
    box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05); /* Same as inventory-container */
    border-radius: 10px;
    height: 95vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.new-item-container form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    column-gap: 20px;
    row-gap: 20px;
    padding: 20px 0;
}

.new-item-container .header h1 {
    font-size: 18px; /* Decreased for a smaller, more compact look */
    color: #333;
    font-weight: 600;
    margin-bottom: 20px;
    text-align: center;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 600;
    color: #333;
    font-size: 12px; /* Decreased label font size */
}

.form-group input {
    padding: 8px; /* Decreased input padding */
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 12px; /* Decreased input font size */
    background-color: #f9f9f9;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-group input:focus {
    border-color: #007bff;
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
    outline: none;
}

.form-group input[type="file"] {
    padding: 5px;
}

.buttons-row {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.cancel-button, .save-item-button {
    padding: 10px 15px; /* Decreased button padding */
    font-size: 12px; /* Decreased button font size */
    font-weight: 600;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.cancel-button {
    background-color: transparent;
    color: #007bff;
    border: 2px solid #007bff;
    width: 200px; /* Reduced button width */
}

.cancel-button:hover {
    background-color: #f0f0ff;
}

.save-item-button {
    background-color: #007bff;
    color: white;
    width: 200px; /* Reduced button width */
}

.save-item-button:hover {
    background-color: #0056b3;
}

/* Modal styles */
.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
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
    font-size: 18px; /* Reduced close button size */
    font-weight: bold;
    cursor: pointer;
}

.close-button:hover {
    color: #ff0000;
}
</style>
