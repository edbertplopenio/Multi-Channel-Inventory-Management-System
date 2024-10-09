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
                <input type="text" class="filter-input" placeholder="Type to filter inventory items">
                <i class="fas fa-filter icon-filter"></i>

                <!-- Dropdown with filter options -->
                <div class="filter-dropdown" id="filter-dropdown">
                    <div class="filter-section">
                        <label for="filter-size">Filter by Size:</label>
                        <select id="filter-size">
                            <option value="">All Sizes</option>
                            <option value="XS">XS</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                            <option value="3XL">3XL</option>
                            <option value="4XL">4XL</option>
                        </select>
                    </div>

                    <div class="filter-section">
                        <label for="filter-color">Filter by Color:</label>
                        <select id="filter-color">
                            <option value="">All Colors</option>
                            <option value="White">White</option>
                            <option value="Beige">Beige</option>
                            <option value="Dark Choco">Dark Choco</option>
                            <option value="Fushia Pink">Fushia Pink</option>
                            <option value="Royal Blue">Royal Blue</option>
                            <option value="Black">Black</option>
                            <option value="Tan">Tan</option>
                            <option value="Raw Umber">Raw Umber</option>
                            <option value="Gray">Gray</option>
                            <option value="Pale Mauve">Pale Mauve</option>
                            <option value="Pantone Simply Taupe">Pantone Simply Taupe</option>
                            <option value="Salmon Pink">Salmon Pink</option>
                        </select>
                    </div>

                    <div class="filter-section">
                        <label for="filter-category">Filter by Category:</label>
                        <select id="filter-category">
                            <option value="">All Categories</option>
                            <option value="Pants">Pants</option>
                            <option value="Jackets & Outerwear">Jackets & Outerwear</option>
                            <option value="Tops">Tops</option>
                            <option value="Sets">Sets</option>
                            <option value="Shorts">Shorts</option>
                            <option value="Dresses">Dresses</option>
                        </select>
                    </div>

                    <div class="filter-section">
                        <label for="filter-date">Filter by Date Added:</label>
                        <input type="date" id="filter-date">
                    </div>

                    <div class="filter-section">
                        <label for="filter-channel">Filter by Channel:</label>
                        <select id="filter-channel">
                            <option value="">All Channels</option>
                            <option value="Physical Store">Physical Store</option>
                            <option value="Shopee">Shopee</option>
                            <option value="TikTok">TikTok</option>
                        </select>
                    </div>

                    <div class="filter-section">
                        <button id="apply-filters">Apply Filters</button>
                        <button id="reset-filters">Reset Filters</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="all-inventory" class="tab-content active">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Category</th>
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
                </tbody>
            </table>
        </div>

        <div id="shopee" class="tab-content">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
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
                </tbody>
            </table>
        </div>

        <div id="tiktok" class="tab-content">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
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
                        <input type="text" id="name" name="name" required minlength="2">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category:</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Pants">Pants</option>
                            <option value="Jackets & Outerwear">Jackets & Outerwear</option>
                            <option value="Tops">Tops</option>
                            <option value="Sets">Sets</option>
                            <option value="Shorts">Shorts</option>
                            <option value="Dresses">Dresses</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="size">Size:</label>
                        <select id="size" name="size" required>
                            <option value="">Select Size</option>
                            <option value="XS">XS</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                            <option value="3XL">3XL</option>
                            <option value="4XL">4XL</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="color">Color:</label>
                        <select id="color" name="color" required>
                            <option value="" selected>Select Color</option>
                            <option value="White">White</option>
                            <option value="Beige">Beige</option>
                            <option value="Dark Choco">Dark Choco</option>
                            <option value="Fushia Pink">Fushia Pink</option>
                            <option value="Royal Blue">Royal Blue</option>
                            <option value="Black">Black</option>
                            <option value="Tan">Tan</option>
                            <option value="Raw Umber">Raw Umber</option>
                            <option value="Gray">Gray</option>
                            <option value="Pale Mauve">Pale Mauve</option>
                            <option value="Pantone Simply Taupe">Pantone Simply Taupe</option>
                            <option value="Salmon Pink">Salmon Pink</option>
                            <option value="custom">Add Custom Color</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group channel-group">
                        <label>Channels:</label>
                        <div class="channel-list">
                            <label>
                                <input type="checkbox" name="channel[]" value="Physical Store" class="channel-checkbox">
                                Physical Store
                            </label>
                            <input type="number" name="quantity-physical-store" placeholder="Qty" min="1" class="quantity-input" disabled>

                            <label>
                                <input type="checkbox" name="channel[]" value="Shopee" class="channel-checkbox">
                                Shopee
                            </label>
                            <input type="number" name="quantity-shopee" placeholder="Qty" min="1" class="quantity-input" disabled>

                            <label>
                                <input type="checkbox" name="channel[]" value="TikTok" class="channel-checkbox">
                                TikTok
                            </label>
                            <input type="number" name="quantity-tiktok" placeholder="Qty" min="1" class="quantity-input" disabled>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="number" id="price" name="price" required min="1">
                    </div>

                    <div class="form-group">
                        <label for="date-added">Date Added:</label>
                        <input type="date" id="date-added" name="date-added" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="image">Image:</label>
                        <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/jpg">
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
    let originalData = [];

    // Fetch original data (if using real data, fetch and store)
    function fetchOriginalData() {
        const rows = document.querySelectorAll('.inventory-table tbody tr');
        originalData = Array.from(rows).map(row => row.outerHTML);
    }

    // Restore the original data
    function restoreOriginalData() {
        const tableBody = document.querySelector('.inventory-table tbody');
        tableBody.innerHTML = originalData.join('');
    }

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

    // Close the modal when the cancel button is clicked
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

    // Enable or disable quantity inputs based on channel checkbox
    document.querySelectorAll('.channel-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const quantityInput = this.closest('.channel-list').querySelector(`input[name="quantity-${this.value.toLowerCase().replace(' ', '-')}"]`);
            if (this.checked) {
                quantityInput.removeAttribute('disabled'); // Enable quantity input
            } else {
                quantityInput.setAttribute('disabled', 'disabled'); // Disable quantity input
                quantityInput.value = ""; // Clear quantity input
            }
        });
    });

    // Filter dropdown toggle
    document.querySelector('.icon-filter').addEventListener('click', function() {
        const filterDropdown = document.getElementById('filter-dropdown');
        filterDropdown.classList.toggle('active');  // Toggle the dropdown visibility
    });

    // Apply filters functionality
    document.getElementById('apply-filters').addEventListener('click', function() {
        const selectedSize = document.getElementById('filter-size').value;
        const selectedColor = document.getElementById('filter-color').value;
        const selectedCategory = document.getElementById('filter-category').value;
        const selectedDate = document.getElementById('filter-date').value;
        const selectedChannel = document.getElementById('filter-channel').value;

        const rows = document.querySelectorAll('.inventory-table tbody tr');

        rows.forEach(row => {
            const size = row.querySelector('td:nth-child(5)').textContent;
            const color = row.querySelector('td:nth-child(6)').textContent;
            const category = row.querySelector('td:nth-child(3)').textContent;
            const dateAdded = row.querySelector('td:nth-child(8)').textContent;
            const channel = row.querySelector('td:nth-child(9)').textContent;

            let showRow = true;

            if (selectedSize && size !== selectedSize) {
                showRow = false;
            }

            if (selectedColor && color !== selectedColor) {
                showRow = false;
            }

            if (selectedCategory && category !== selectedCategory) {
                showRow = false;
            }

            if (selectedDate && dateAdded !== selectedDate) {
                showRow = false;
            }

            if (selectedChannel && channel !== selectedChannel) {
                showRow = false;
            }

            row.style.display = showRow ? '' : 'none';
        });

        // Hide the filter dropdown after applying
        document.getElementById('filter-dropdown').classList.remove('active');
    });

    // Reset filters functionality
    document.getElementById('reset-filters').addEventListener('click', function() {
        document.getElementById('filter-size').value = "";
        document.getElementById('filter-color').value = "";
        document.getElementById('filter-category').value = "";
        document.getElementById('filter-date').value = "";
        document.getElementById('filter-channel').value = "";

        // Restore the original data
        restoreOriginalData();

        // Hide the filter dropdown after resetting
        document.getElementById('filter-dropdown').classList.remove('active');
    });

    // Handle form submission to add a new inventory item
    document.getElementById('new-item-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData();  // Using FormData to send multipart data

        formData.append('name', document.getElementById('name').value);
        formData.append('category', document.getElementById('category').value);
        formData.append('size', document.getElementById('size').value);
        formData.append('color', document.getElementById('color').value);
        formData.append('price', document.getElementById('price').value);
        formData.append('date-added', document.getElementById('date-added').value);
        formData.append('image', document.getElementById('image').files[0]);  // Handling image upload

        // Get quantities for each channel
        const channels = [];
        let totalQuantity = 0;

        const physicalStoreQty = parseInt(document.querySelector('input[name="quantity-physical-store"]').value) || 0;
        const shopeeQty = parseInt(document.querySelector('input[name="quantity-shopee"]').value) || 0;
        const tiktokQty = parseInt(document.querySelector('input[name="quantity-tiktok"]').value) || 0;

        if (physicalStoreQty > 0) {
            channels.push({ channel: 'Physical Store', quantity: physicalStoreQty });
            totalQuantity += physicalStoreQty;
        }
        if (shopeeQty > 0) {
            channels.push({ channel: 'Shopee', quantity: shopeeQty });
            totalQuantity += shopeeQty;
        }
        if (tiktokQty > 0) {
            channels.push({ channel: 'TikTok', quantity: tiktokQty });
            totalQuantity += tiktokQty;
        }

        formData.append('quantity', totalQuantity); // Send total quantity
        formData.append('channels', JSON.stringify(channels)); // Send channels as JSON string

        // Send the form data to the backend using fetch
        fetch('../../backend/controllers/add_item.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())  // Ensure we parse the response as JSON
        .then(data => {
            if (data.success) {
                // Show success message using SweetAlert
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Product added successfully!',
                    confirmButtonText: 'OK'
                });

                // Append the new item to the table dynamically
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${data.product_id}</td>
                    <td>${document.getElementById('name').value}</td>
                    <td>${document.getElementById('category').value}</td>
                    <td>${totalQuantity}</td>
                    <td>${document.getElementById('size').value}</td>
                    <td>${document.getElementById('color').value}</td>
                    <td>${document.getElementById('price').value}</td>
                    <td>${document.getElementById('date-added').value}</td>
                    <td>${channels.map(c => c.channel).join(', ')}</td>
                    <td><img src="../../frontend/public/images/${data.image_name || 'image-placeholder.png'}" alt="Image" width="50"></td>
                    <td>
                        <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                        <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
                    </td>
                `;

                document.querySelector('.inventory-table tbody').appendChild(newRow); // Append the new row to the table

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error: ' + data.message,
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong!',
                confirmButtonText: 'OK'
            });
        });

        // Reset the form and close the modal
        document.getElementById('new-item-form').reset();
        modal.style.display = "none"; // Hide modal
    });


    // Call this function to store the original state of the table when the page loads
    fetchOriginalData();
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





        /* Filter input styling */
        .filter-input-container {
            position: relative;
            display: inline-block;
            margin-bottom: 10px; /* Smaller spacing */
        }

        .filter-input {
            padding: 6px 10px; /* Reduced padding */
            font-size: 12px; /* Smaller font size */
            border: 1px solid #ccc;
            border-radius: 18px;
            width: 220px; /* Smaller width */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .filter-input:focus {
            border-color: #0056b3;
            box-shadow: 0 4px 8px rgba(0, 86, 179, 0.3);
        }

        .icon-filter {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px; /* Smaller icon */
            color: #888;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .icon-filter:hover {
            color: #0056b3; /* Blue */
        }

        /* Dropdown styling for filters */
        .filter-dropdown {
            background-color: white;
            border: 1px solid #ccc;
            position: absolute;
            top: 40px;
            right: 0;
            padding: 8px; /* Reduced padding */
            width: 220px; /* Smaller width */
            border-radius: 4px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
        }

        .filter-dropdown.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .filter-section {
            margin-bottom: 10px; /* Smaller spacing */
        }

        .filter-section label {
            font-size: 11px; /* Smaller font size */
            font-weight: 600;
            color: #004085;
            margin-bottom: 5px;
        }

        .filter-section select,
        .filter-section input {
            width: 100%;
            padding: 5px; /* Smaller padding */
            font-size: 11px; /* Smaller font size */
            border: 1px solid #ccc;
            border-radius: 3px;
            background-color: #f8f9fa;
        }

        .filter-section select:focus,
        .filter-section input:focus {
            border-color: #0056b3;
            outline: none;
        }

        #apply-filters, #reset-filters {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 6px; /* Smaller padding */
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 5px;
            font-size: 12px; /* Smaller font size */
            transition: background-color 0.3s ease;
        }

        #apply-filters:hover, #reset-filters:hover {
            background-color: #004085;
        }






</style>
