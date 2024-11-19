<?php
session_start();

// Check if user is logged in
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

    <!-- Stylesheets -->
    <link rel="stylesheet" href="../../frontend/public/styles/sales_record.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.css" rel="stylesheet">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.all.min.js"></script>


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
                <button class="tab" data-tab="physical_store"> <!-- Changed from physical-store to physical_store -->
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
                        <th>Variant ID</th>
                        <th>Product Name</th>
                        <th>Variant (Size/Color)</th>
                        <th>Sale Date</th>
                        <th>Quantity Sold</th>
                        <th>Cost per Item</th>
                        <th>Total Sales</th>
                        <th>Sales Channel</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div id="physical_store" class="tab-content">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Variant ID</th>
                        <th>Product Name</th>
                        <th>Variant (Size/Color)</th>
                        <th>Sale Date</th>
                        <th>Quantity Sold</th>
                        <th>Cost per Item</th>
                        <th>Total Sales</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div id="shopee" class="tab-content">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Variant ID</th>
                        <th>Product Name</th>
                        <th>Variant (Size/Color)</th>
                        <th>Sale Date</th>
                        <th>Quantity Sold</th>
                        <th>Cost per Item</th>
                        <th>Total Sales</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div id="tiktok" class="tab-content">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Variant ID</th>
                        <th>Product Name</th>
                        <th>Variant (Size/Color)</th>
                        <th>Sale Date</th>
                        <th>Quantity Sold</th>
                        <th>Cost per Item</th>
                        <th>Total Sales</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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
                            <label for="name">Product Name:</label>
                            <select id="name" name="name" required>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>"
                                        data-product-id="<?= $product['product_id'] ?>">
                                        <?= $product['text'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="product-id" name="product-id">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="sale-date">Sale Date:</label>
                            <input type="date" id="sale-date" name="sale-date" required>
                        </div>
                        <div class="form-group">
                            <label for="quantity-sold">Quantity Sold:</label>
                            <input type="number" id="quantity-sold" name="quantity-sold" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cost">Cost per Unit:</label>
                            <input type="number" id="cost" name="cost" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="total-price">Total Price:</label>
                            <input type="number" id="total-price" name="total-price" required readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="sales-channel">Sales Channel:</label>
                            <select id="sales-channel" name="sales-channel" required>
                                <option value="physical_store">Physical Store</option>
                                <option value="shopee">Shopee</option>
                                <option value="tiktok">TikTok</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row buttons-row">
                        <button type="button" class="cancel-button">Cancel</button>
                        <button type="submit" class="save-order-button">Save Order</button>
                    </div>
                </form>
            </div>
        </div>








 <!-- JS for fetching and Add in modal -->
 <script>
    $(document).ready(function() {
        let productData = {}; // Store product details locally

        // Initialize Select2 for product selection
        $('#name').select2({
            placeholder: 'Search for a product',
            allowClear: true,
            ajax: {
                url: '../../backend/controllers/fetch_products.php', // Adjust path to your PHP script
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term // Search term
                    };
                },
                processResults: function(data) {
                    // Cache product data locally
                    productData = {};
                    data.items.forEach(function(item) {
                        productData[item.id] = item; // Store by variant_id
                    });
                    return {
                        results: data.items
                    };
                }
            },
            width: '100%',
            dropdownParent: $('.modal-content') // Ensure dropdown stays within the modal
        });

        // Update form fields when a product is selected
        $('#name').on('select2:select', function(e) {
            const selectedVariantId = e.params.data.id; // Get selected variant_id
            const selectedProduct = productData[selectedVariantId]; // Retrieve product details by variant_id

            if (selectedProduct) {
                $('#product-id').val(selectedProduct.product_id); // Set the actual product_id
                $('#cost').val(selectedProduct.price); // Set the cost field
                $('#quantity-sold').val(''); // Clear quantity field
                $('#total-price').val(''); // Clear total price field
            }
        });

        // Calculate total price when quantity is entered
        $('#quantity-sold').on('input', function() {
            const quantity = $(this).val();
            const cost = $('#cost').val();

            if (quantity && cost) {
                $('#total-price').val((quantity * cost).toFixed(2)); // Calculate and display total price
            } else {
                $('#total-price').val(''); // Clear total price if input is invalid
            }
        });

        // Submit the form via AJAX
        $('#new-order-form').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            // Disable submit button and form inputs to prevent double submission
            $('#submit-btn').prop('disabled', true);
            $('#new-order-form input, #new-order-form select').prop('disabled', true);

            const formData = {
                variant_id: $('#name').val(),
                product_id: $('#product-id').val(),
                sale_date: $('#sale-date').val(),
                quantity: $('#quantity-sold').val(),
                price: $('#cost').val(),
                total_price: $('#total-price').val(),
                channel: $('#sales-channel').val()
            };

            console.log('Submitting Sales Data:', formData); // Debugging

            // Send the data to the server
            $.ajax({
                url: '../../backend/controllers/add_sales.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    console.log('Response:', response); // Debugging
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Sales record added successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                        $('#new-order-modal').hide(); // Close modal
                        $('#new-order-form')[0].reset(); // Reset the form

                        // Update all the tables dynamically for all tabs
                        const tabs = ['all-orders', 'physical_store', 'shopee', 'tiktok']; // Add your tab IDs here
                        tabs.forEach(function(tab) {
                            loadSalesData(tab); // Reload data for each tab
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Failed to add sales record.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while adding the sales record.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function() {
                    // Re-enable submit button and form inputs after AJAX completion (success or error)
                    $('#submit-btn').prop('disabled', false);
                    $('#new-order-form input, #new-order-form select').prop('disabled', false);
                }
            });
        });

        // Function to format currency (PHP format)
        function formatCurrency(amount) {
            if (amount) {
                return '₱' + parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            return '₱0.00';
        }

        // Function to format date (MM/DD/YYYY)
        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = ('0' + date.getDate()).slice(-2); // Ensure two digits for day
            const month = ('0' + (date.getMonth() + 1)).slice(-2); // Ensure two digits for month
            const year = date.getFullYear();
            return `${month}/${day}/${year}`;
        }

        // Function to format sales channel (remove underscores and capitalize each word)
        function formatChannel(channel) {
            if (channel) {
                // Replace underscores with spaces and capitalize first letter of each word
                return channel.replace(/_/g, ' ').replace(/\b\w/g, (char) => char.toUpperCase());
            }
            return 'N/A';
        }

        // Function to load sales data for a specific tab
        function loadSalesData(tab) {
            $.ajax({
                url: '../../backend/controllers/fetch_sales.php', // Adjust to your PHP script location
                type: 'GET',
                data: { tab: tab }, // Pass the tab name (e.g., "physical_store", "shopee", "tiktok")
                success: function(response) {
                    if (response.success) {
                        const tbody = $(`#${tab} .sales-table tbody`); // Target table body of the selected tab
                        tbody.empty(); // Clear existing rows

                        // Populate table rows with formatted values
                        response.data.forEach((sale) => {
                            tbody.append(`
                                <tr>
                                    <td>${sale.variant_id}</td>
                                    <td>${sale.product_name}</td>
                                    <td>${sale.variant}</td>
                                    <td>${formatDate(sale.sale_date)}</td>
                                    <td>${sale.quantity}</td>
                                    <td>${formatCurrency(sale.cost_per_item)}</td>
                                    <td>${formatCurrency(sale.total_price)}</td>
                                    <td>${formatChannel(sale.channel)}</td>
                                    <td class="action-column">
                                        <button class="action-button edit" data-id="${sale.sale_id}">Edit</button>
                                        <button class="action-button archive" data-id="${sale.sale_id}">Archive</button>
                                    </td>
                                </tr>
                            `);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to fetch sales data: ' + response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while fetching sales data.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Initial loading of sales data for all tabs
        const tabs = ['all-orders', 'physical_store', 'shopee', 'tiktok']; // Add your tab IDs here
        tabs.forEach(function(tab) {
            loadSalesData(tab); // Load sales data for each tab on page load
        });

        // Event listener for tab switching
        $('.tab').on('click', function() {
            const tab = $(this).data('tab'); // Get the tab name (e.g., "physical_store")

            // Highlight active tab
            $('.tab').removeClass('active');
            $(this).addClass('active');

            // Show selected tab content and hide others
            $('.tab-content').removeClass('active');
            $(`#${tab}`).addClass('active');

            // Load sales data for the selected tab
            loadSalesData(tab);
        });
    });
</script>




<script>
$(document).ready(function() {
    // Archive button functionality
    $(document).on('click', '.archive', function() {
        const saleId = $(this).data('id'); // Get the sale ID from the data-id attribute

        // Show confirmation popup using SweetAlert2
        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to archive this sales record.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, archive it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true // Make the Cancel button on the right side
        }).then((result) => {
            if (result.isConfirmed) {
                // Send the archive request to the backend
                $.ajax({
                    url: '../../backend/controllers/archive_sale.php', // URL of the PHP script handling archiving
                    type: 'POST',
                    data: {
                        sale_id: saleId
                    },
                    dataType: 'json', // Ensure the response is parsed as JSON
                    success: function(response) {
                        console.log("Response from server:", response); // Debugging

                        if (response.success) {
                            // Show success message using SweetAlert2
                            Swal.fire({
                                title: 'Success!',
                                text: 'Sales record archived successfully.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });

                            // Reload the sales data for all tabs after archiving
                            loadSalesDataForAllTabs();
                        } else {
                            // Show error message using SweetAlert2
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to archive sales record.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        // Show error message if the AJAX request fails
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while archiving the sales record.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    });

    // Function to reload data for all tabs
    function loadSalesDataForAllTabs() {
        const tabs = ['all-orders', 'physical_store', 'shopee', 'tiktok']; // List of all tab types
        tabs.forEach(function(tab) {
            loadSalesData(tab); // Reload sales data for each tab
        });
    }
});

// Function to load sales data for a specific tab
function loadSalesData(tab) {
    $.ajax({
        url: '../../backend/controllers/fetch_sales.php', // Adjust to your PHP script location
        type: 'GET',
        data: { tab: tab }, // Pass the tab name (e.g., "physical_store", "shopee", "tiktok")
        success: function(response) {
            if (response.success) {
                const tbody = $(`#${tab} .sales-table tbody`); // Target table body of the selected tab
                tbody.empty(); // Clear existing rows

                // Populate table rows with formatted values
                response.data.forEach((sale) => {
                    tbody.append(`
                        <tr>
                            <td>${sale.variant_id}</td>
                            <td>${sale.product_name}</td>
                            <td>${sale.variant}</td>
                            <td>${formatDate(sale.sale_date)}</td>
                            <td>${sale.quantity}</td>
                            <td>${formatCurrency(sale.cost_per_item)}</td>
                            <td>${formatCurrency(sale.total_price)}</td>
                            <td>${formatChannel(sale.channel)}</td>
                            <td class="action-column">
                                <button class="action-button edit" data-id="${sale.sale_id}">Edit</button>
                                <button class="action-button archive" data-id="${sale.sale_id}">Archive</button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to fetch sales data: ' + response.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function() {
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while fetching sales data.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
}

// Helper functions to format data
function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString(); // Format date to 'MM/DD/YYYY'
}

function formatCurrency(value) {
    return '$' + parseFloat(value).toFixed(2); // Format as currency
}

function formatChannel(channel) {
    switch (channel) {
        case 'physical_store':
            return 'Physical Store';
        case 'shopee':
            return 'Shopee';
        case 'tiktok':
            return 'TikTok';
        default:
            return channel;
    }
}


</script>






<!-- Main JS -->
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

        // Get modal and buttons
        const modal = document.getElementById("new-order-modal");
        const newOrderButton = document.querySelector(".new-order-button");
        const closeButton = document.querySelector(".close-button");
        const cancelButton = document.querySelector(".cancel-button");
        const form = document.getElementById("new-order-form"); // Get the form element
        const productIdInput = document.getElementById("product-id");
        const costInput = document.getElementById("cost");
        const totalPriceInput = document.getElementById("total-price");

        // Show the modal when the "New Sales Order" button is clicked
        newOrderButton.addEventListener('click', function() {
            modal.style.display = "flex"; // Display modal
        });

        // Close the modal when the close button (x) is clicked
        closeButton.addEventListener('click', function() {
            closeModalAndReset();
        });

        // Close the modal when the cancel button is clicked
        cancelButton.addEventListener('click', function() {
            closeModalAndReset();
        });

        // Function to close modal and reset the form
        function closeModalAndReset() {
            modal.style.display = "none"; // Hide modal
            form.reset(); // Reset the form fields

            // Manually reset dynamically updated fields (like readonly inputs)
            productIdInput.value = ""; // Reset hidden product id field
            costInput.value = ""; // Reset cost per unit
            totalPriceInput.value = ""; // Reset total price
        }

        // Prevent modal from closing when clicking outside of it
        window.addEventListener('click', function(event) {
            // Do nothing if clicked inside the modal content
            if (event.target === modal && !event.target.closest(".modal-content")) {
                return; // Don't close the modal if clicked outside
            }
        });
    }

    // Call the initialization function when the page loads or when entering the section
    initializeSalesRecord();
</script>





</body>

</html>













<!-- Css for action buttons -->
<style>
    .action-button {
        background-color: #3CAE85;
        color: #fff;
        padding: 6px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        margin-right: 5px;
        transition: background-color 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    .action-column {
        display: flex;
        gap: 5px;
        flex-wrap: nowrap;
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


<!-- CSS for channel field -->
<style>
    /* Dropdown style */
    #sales-channel {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 12px;
        background-color: #f9f9f9;
        width: 100%;
        transition: border-color 0.3s;
    }

    #sales-channel:focus {
        border-color: #007bff;
        outline: none;
    }

    /* Optional: Custom arrow icon for dropdown */
    #sales-channel option {
        background-color: #fff;
        color: #333;
    }

    /* Aligning the dropdown with the other form inputs */
    .new-order-container form select {
        width: 100%;
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
        font-size: 12px;
        color: #333;
        transition: border-color 0.3s;
    }

    .new-order-container form select:focus {
        border-color: #007bff;
        outline: none;
    }


    /* Dropdown style for Product Name */
    #name {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 12px;
        background-color: #f9f9f9;
        width: 100%;
        transition: border-color 0.3s;
    }

    #name:focus {
        border-color: #007bff;
        outline: none;
    }

    /* Optional: Custom arrow icon for dropdown */
    #name option {
        background-color: #fff;
        color: #333;
    }

    /* Aligning the dropdown with the other form inputs */
    .new-order-container form select {
        width: 100%;
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
        font-size: 12px;
        color: #333;
        transition: border-color 0.3s;
    }

    .new-order-container form select:focus {
        border-color: #007bff;
        outline: none;
    }
</style>




<!-- Css for selection name -->
<style>
    /* Styling the Select2 dropdown to match other input fields */
    .select2-container {
        width: 100% !important;
        /* Ensure full width */
    }

    .select2-container .select2-selection--single {
        height: 40px;
        /* Match input height */
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 12px;
        background-color: #f9f9f9;
        transition: border-color 0.3s ease;
        color: #333;
    }

    .select2-container .select2-selection--single:focus {
        border-color: #007bff;
        outline: none;
    }

    .select2-container .select2-selection__arrow {
        height: 100%;
        /* Align arrow with field */
        top: 0;
        /* Align arrow properly */
        right: 10px;
        /* Spacing from the right */
        width: 30px;
        color: #aaa;
    }

    /* Placeholder styling */
    .select2-container .select2-selection__placeholder {
        color: #aaa;
        /* Placeholder color */
        font-style: italic;
    }

    /* Dropdown menu styling */
    .select2-container .select2-dropdown {
        border-radius: 5px;
        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        border: 1px solid #ccc;
        background-color: #fff;
    }

    /* Dropdown options */
    .select2-container .select2-results__option {
        padding: 8px 12px;
        font-size: 12px;
        color: #333;
    }

    /* Highlighted dropdown option */
    .select2-container .select2-results__option--highlighted {
        background-color: #007bff;
        color: #fff;
    }

    /* Disabled option */
    .select2-container .select2-results__option[aria-disabled="true"] {
        color: #aaa;
        cursor: not-allowed;
    }

    /* Search box inside dropdown */
    .select2-container .select2-search--dropdown .select2-search__field {
        padding: 8px 12px;
        font-size: 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: calc(100% - 24px);
        /* Account for padding */
        margin: 8px auto;
    }

    /* Adjust for grid layout */
    .new-order-container form .form-group select {
        width: 100%;
        /* Ensure the dropdown takes full width */
        height: 40px;
        /* Match input height */
    }
</style>





<!-- Overall Css -->
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

    .sales-table th,
    .sales-table td {
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
        display: none;
        /* Hidden by default */
        position: fixed;
        z-index: 1000;
        /* Ensure it's above everything else */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* Background overlay */
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: #ffffff;
        padding: 15px;
        /* Reduced modal padding */
        border-radius: 10px;
        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
        width: 40%;
        /* Reduced modal width */
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

    .cancel-button,
    .save-order-button {
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



<!-- 
//hindi nagsasave, 
//dapat icheck kung may tamang quantity sa channel -->