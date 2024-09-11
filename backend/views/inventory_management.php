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

        <!-- Additional tab contents for physical-store, shopee, tiktok are here -->

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

        // JavaScript to handle SweetAlert2 popup modal
        document.querySelector('.new-item-button').addEventListener('click', () => {
            Swal.fire({
                title: 'Add New Inventory Item',
                html: `
                    <div class="swal-form-container">
                        <form id="new-item-form">
                            <div class="swal-form-group">
                                <label for="product-id" class="swal-label">Product ID</label>
                                <input type="text" id="product-id" name="product-id" class="swal-input" required>
                            </div>
                            <div class="swal-form-group">
                                <label for="product-name" class="swal-label">Product Name</label>
                                <input type="text" id="product-name" name="product-name" class="swal-input" required>
                            </div>
                            <div class="swal-form-group">
                                <label for="description" class="swal-label">Description</label>
                                <textarea id="description" name="description" class="swal-input" required></textarea>
                            </div>
                            <div class="swal-form-group">
                                <label for="category" class="swal-label">Category</label>
                                <input type="text" id="category" name="category" class="swal-input" required>
                            </div>
                            <div class="swal-form-group">
                                <label for="quantity" class="swal-label">Quantity</label>
                                <input type="number" id="quantity" name="quantity" class="swal-input" required>
                            </div>
                            <div class="swal-form-group">
                                <label for="size" class="swal-label">Size</label>
                                <input type="text" id="size" name="size" class="swal-input" required>
                            </div>
                            <div class="swal-form-group">
                                <label for="color" class="swal-label">Color</label>
                                <input type="text" id="color" name="color" class="swal-input" required>
                            </div>
                            <div class="swal-form-group">
                                <label for="price" class="swal-label">Price</label>
                                <input type="number" id="price" name="price" class="swal-input" required>
                            </div>
                            <div class="swal-form-group">
                                <label for="date-added" class="swal-label">Date Added</label>
                                <input type="date" id="date-added" name="date-added" class="swal-input" required>
                            </div>
                            <div class="swal-form-group">
                                <label for="last-updated" class="swal-label">Last Updated</label>
                                <input type="date" id="last-updated" name="last-updated" class="swal-input">
                            </div>
                            <div class="swal-form-group">
                                <label for="image" class="swal-label">Image</label>
                                <input type="file" id="image" name="image" class="swal-input-file">
                            </div>
                        </form>
                    </div>
                `,
                width: '500px',  // Reduced the width further
                padding: '20px',  // Reduced padding
                customClass: {
                    popup: 'custom-swal-popup'
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInUp animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown animate__faster'
                },
                confirmButtonText: 'Add Item',
                preConfirm: () => {
                    const form = document.getElementById('new-item-form');
                    if (form.checkValidity()) {
                        return {
                            productId: form.productId.value,
                            productName: form.productName.value,
                            description: form.description.value,
                            category: form.category.value,
                            quantity: form.quantity.value,
                            size: form.size.value,
                            color: form.color.value,
                            price: form.price.value,
                            dateAdded: form.dateAdded.value,
                            lastUpdated: form.lastUpdated.value,
                            image: form.image.files[0]
                        };
                    } else {
                        Swal.showValidationMessage('Please fill out all required fields.');
                        return false;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log(result.value); // Access the form data
                }
            });
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
}

.inventory-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
    background-color: #ffffff;
    box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05);
    border-radius: 10px;
    height: 95vh;
    display: flex;
    flex-direction: column;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.header h1 {
    font-size: 22px; /* Reduced the header font size */
    color: #333;
    font-weight: 600;
}

.new-item-button {
    background-color: #007bff;
    color: #fff;
    padding: 6px 12px; /* Reduced the button size */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px; /* Reduced the button text size */
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.new-item-button:hover {
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
    padding: 8px 12px; /* Reduced padding */
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 10px 10px 0 0;
    cursor: pointer;
    font-size: 12px; /* Reduced font size */
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
    padding: 8px 12px; /* Reduced input size */
    border: 1px solid #ccc;
    border-radius: 20px;
    width: 220px;
    color: #333;
    font-size: 12px; /* Reduced input font size */
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
    padding: 10px; /* Reduced padding in table */
    text-align: left;
    border-bottom: 1px solid #eee;
}

.inventory-table th {
    background-color: #f4f7fc;
    color: #555;
    font-size: 12px; /* Reduced header font size */
    font-weight: 600;
}

.inventory-table td {
    color: #555;
    font-size: 12px; /* Reduced table font size */
}

.inventory-table img {
    border-radius: 5px;
}

.inventory-table tr:last-child td {
    border-bottom: none;
}

/* Hidden by default, shown when active */
.tab-content {
    display: none;
    padding-top: 20px;
}

.tab-content.active {
    display: block;
    height: 100%;
}

/* SweetAlert2 Custom Styling for Professional Look */
.custom-swal-popup {
    max-width: 500px; /* Reduced popup width */
    border-radius: 8px;
    padding: 15px; /* Reduced padding in popup */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.swal-form-container {
    display: flex;
    flex-direction: column;
    gap: 10px; /* Reduced gap between form elements */
}

.swal-form-group {
    display: flex;
    flex-direction: column;
}

.swal-label {
    font-weight: 500; /* Reduced label weight */
    margin-bottom: 5px;
    color: #333;
    font-size: 12px; /* Reduced label font size */
}

.swal-input, .swal-input-file {
    padding: 6px; /* Reduced input padding */
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 12px; /* Reduced input font size */
    transition: border-color 0.3s ease;
}

.swal-input:focus, .swal-input-file:focus {
    border-color: #007bff;
}

.swal2-confirm {
    background-color: #007bff !important;
    color: #fff !important;
    border-radius: 5px;
    padding: 8px 12px; /* Reduced button padding */
    font-size: 12px; /* Reduced button font size */
    transition: background-color 0.3s ease;
}

.swal2-confirm:hover {
    background-color: #0056b3 !important;
}

.swal2-cancel {
    background-color: #ccc !important;
    color: #333 !important;
    border-radius: 5px;
    padding: 8px 12px; /* Reduced button padding */
    font-size: 12px; /* Reduced button font size */
}

.swal2-cancel:hover {
    background-color: #aaa !important;
}

.action-button {
    background-color: #007bff;
    color: #fff;
    padding: 6px 10px; /* Reduced action button size */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px; /* Reduced action button font size */
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

</style>
