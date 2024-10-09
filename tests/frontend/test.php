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
                        <th>Quantity</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Price</th>
                        <th>Date Added</th>
                        <th>Channel</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div id="physical-store" class="tab-content">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
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
                </tbody>
            </table>
        </div>

        <div id="shopee" class="tab-content">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
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
                </tbody>
            </table>
        </div>

        <div id="tiktok" class="tab-content">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
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
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="size">Size:</label>
                        <select id="size" name="size" required>
                            <option value="">Select Size</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="color">Color:</label>
                        <select id="color" name="color" required>
                            <option value="" selected>Select Color</option>
                            <option value="Red">Red</option>
                            <option value="Blue">Blue</option>
                            <option value="Green">Green</option>
                            <option value="Yellow">Yellow</option>
                            <option value="custom">Add Custom Color</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group channel-group">
                        <label>Channels:</label>
                        <div class="channel-list">
                            <label>
                                <input type="checkbox" name="channel[]" value="Physical Store">
                                Physical Store
                            </label>
                            <input type="number" name="quantity-physical-store" placeholder="Qty" min="0">

                            <label>
                                <input type="checkbox" name="channel[]" value="Shopee">
                                Shopee
                            </label>
                            <input type="number" name="quantity-shopee" placeholder="Qty" min="0">

                            <label>
                                <input type="checkbox" name="channel[]" value="TikTok">
                                TikTok
                            </label>
                            <input type="number" name="quantity-tiktok" placeholder="Qty" min="0">
                        </div>
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

    // Close the modal when the cancel button is clicked, not when clicking outside
    closeButton.addEventListener('click', function() {
        modal.style.display = "none"; // Hide modal
    });

    // Close the modal if the cancel button is clicked
    document.querySelector('.cancel-button').addEventListener('click', function() {
        modal.style.display = "none"; // Hide modal
    });

    // Prevent modal from closing when clicking outside of the content
    window.addEventListener('click', function(event) {
        if (event.target !== modal && !modal.contains(event.target)) {
            return; // Do nothing when clicking outside
        }
    });

    // Handle the custom color addition with validation and duplicate check
    document.getElementById('color').addEventListener('change', function(event) {
        if (event.target.value === 'custom') {
            Swal.fire({
                title: 'Enter a custom color',
                input: 'text',
                inputLabel: 'Custom Color',
                inputPlaceholder: 'e.g., Purple, #ff0000, rgb(255,0,0)',
                showCancelButton: true,
                allowOutsideClick: false, // Disable closing by clicking outside
                inputValidator: (value) => {
                    const isColorValid = validateColor(value);
                    const colorAlreadyExists = checkIfColorExists(value);

                    if (!isColorValid) {
                        return 'Invalid color format! Please enter a valid color name, hex code, or rgb value.';
                    }
                    if (colorAlreadyExists) {
                        return 'This color already exists in the dropdown!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const customColor = result.value;

                    // Add the custom color to the dropdown if confirmed
                    const colorDropdown = document.getElementById('color');
                    const newOption = document.createElement('option');
                    newOption.value = customColor;
                    newOption.text = customColor;
                    colorDropdown.add(newOption);

                    // Select the newly added color
                    colorDropdown.value = customColor;
                } else {
                    // Reset dropdown to default if cancelled
                    document.getElementById('color').value = ""; 
                }
            });
        }
    });

    // Function to check if color already exists in the dropdown
    function checkIfColorExists(color) {
        const colorDropdown = document.getElementById('color');
        for (let i = 0; i < colorDropdown.options.length; i++) {
            if (colorDropdown.options[i].value.toLowerCase() === color.toLowerCase()) {
                return true;
            }
        }
        return false;
    }

    // Function to validate color names, hex codes, and rgb values
    function validateColor(value) {
        const hexColorRegex = /^#([0-9A-F]{3}){1,2}$/i;
        const rgbColorRegex = /^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$/;
        const namedColors = [
            "red", "blue", "green", "yellow", "purple", "black", "white", "gray", "orange", "pink", "brown", "cyan", "magenta"
        ];

        // Check if value is a valid hex code, rgb value, or named color
        return hexColorRegex.test(value) || rgbColorRegex.test(value) || namedColors.includes(value.toLowerCase());
    }
}

// Call the initialization function when the page loads
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

.form-group input, .form-group select {
    padding: 8px; /* Decreased input padding */
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 12px; /* Decreased input font size */
    background-color: #f9f9f9;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-group input:focus, .form-group select:focus {
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

.channel-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.channel-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: center;
}

.channel-list label {
    display: flex;
    align-items: center;
    gap: 5px;
}

.channel-list input[type="number"] {
    width: 60px;
    padding: 5px;
    font-size: 12px;
}
</style>
