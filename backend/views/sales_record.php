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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>



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
                <button class="tab" data-tab="physical_store">
                    <i class="fas fa-store"></i> Physical Store
                </button>
                <button class="tab" data-tab="shopee">
                    <i class="fas fa-shopping-bag"></i> Shopee
                </button>
                <button class="tab" data-tab="tiktok">
                    <i class="fas fa-music"></i> TikTok
                </button>
                <!-- New tab for Archived Sales -->
                <button class="tab" data-tab="archived-sales">
                    <i class="fas fa-archive"></i> Archived Sales
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

        <!-- Archived Sales Content -->
        <div id="archived-sales" class="tab-content">
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



        <!-- New Sales Order Modal -->
        <div id="new-order-modal" class="modal">
            <div class="modal-content">
                <div class="header">
                    <h1>Add New Sales Order</h1>
                    <div class="top-right-button">
                        <button id="upload-excel-button" class="upload-excel-button">Upload Excel</button>
                        <input type="file" id="excel-upload" name="excel-upload" accept=".xls,.xlsx" style="display: none;">
                    </div>
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

                <!-- Excel preview table -->
                <div id="excel-preview" style="margin-top: 20px; display: none;">
                    <h3>Excel Preview:</h3>
                    <table id="preview-table" border="1" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr id="preview-header"></tr>
                        </thead>
                        <tbody id="preview-body"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


        <!-- Excel JS -->
        <script>
            // Select the button, file input, and preview table elements
            const uploadButton = document.getElementById('upload-excel-button');
            const excelUploadInput = document.getElementById('excel-upload');
            const excelPreviewDiv = document.getElementById('excel-preview');
            const previewTableHeader = document.getElementById('preview-header');
            const previewTableBody = document.getElementById('preview-body');

            // Trigger file input click when button is clicked
            uploadButton.addEventListener('click', () => {
                console.log('Upload button clicked'); // Debugging
                excelUploadInput.click(); // Opens the file dialog
            });

            // Listen for file selection
            excelUploadInput.addEventListener('change', (event) => {
                console.log('File selected:', event.target.files[0]); // Debugging
                handleExcelUpload(event);
            });

            function handleExcelUpload(event) {
                const file = event.target.files[0]; // Get the uploaded file

                if (!file) {
                    alert('No file selected. Please upload an Excel file.');
                    return;
                }

                console.log('Processing file:', file.name); // Debugging

                // Check if the file is an Excel file
                const validExtensions = ['xls', 'xlsx'];
                const fileExtension = file.name.split('.').pop().toLowerCase();

                if (!validExtensions.includes(fileExtension)) {
                    alert('Invalid file type. Please upload an Excel file (.xls or .xlsx).');
                    excelUploadInput.value = ''; // Clear the input
                    return;
                }

                // Parse the Excel file
                const reader = new FileReader();

                reader.onload = function(e) {
                    console.log('File loaded successfully'); // Debugging
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {
                        type: 'array'
                    });

                    // Get the first sheet
                    const sheetName = workbook.SheetNames[0];
                    const sheet = workbook.Sheets[sheetName];

                    // Convert sheet to JSON
                    const sheetData = XLSX.utils.sheet_to_json(sheet, {
                        header: 1
                    });
                    console.log('Sheet data:', sheetData); // Debugging

                    // Display the data in a table
                    displayExcelPreview(sheetData);
                };

                reader.onerror = function() {
                    alert('Error reading the file. Please try again.');
                };

                reader.readAsArrayBuffer(file);
            }

            function displayExcelPreview(data) {
                // Clear previous table content
                previewTableHeader.innerHTML = '';
                previewTableBody.innerHTML = '';

                // Ensure there's data in the Excel sheet
                if (data.length === 0) {
                    alert('The Excel file is empty.');
                    return;
                }

                console.log('Displaying data in table'); // Debugging

                // Display the header row
                const headerRow = data[0];
                headerRow.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    previewTableHeader.appendChild(th);
                });

                // Display the rest of the rows
                for (let i = 1; i < data.length; i++) {
                    const row = data[i];
                    const tr = document.createElement('tr');

                    row.forEach(cell => {
                        const td = document.createElement('td');
                        td.textContent = cell || ''; // Display empty cells as blank
                        tr.appendChild(td);
                    });

                    previewTableBody.appendChild(tr);
                }

                // Show the preview table
                excelPreviewDiv.style.display = 'block';
            }
        </script>





        <!-- Hindi pa nagana yung edit -->
        <!-- Ayusin ang upload excel -->



        <div id="edit-order-modal" class="modal">
            <div class="modal-content">
                <div class="header">
                    <h1>Edit Sales Order</h1>
                </div>

                <form id="edit-order-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit-name">Product Name:</label>
                            <select id="edit-name" name="name" disabled>
                                <!-- Dynamic product options will be loaded here -->
                            </select>
                            <input type="hidden" id="edit-product-id" name="product-id">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit-sale-date">Sale Date:</label>
                            <input type="date" id="edit-sale-date" name="sale-date" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-quantity-sold">Quantity Sold:</label>
                            <input type="number" id="edit-quantity-sold" name="quantity-sold" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit-cost">Cost per Unit:</label>
                            <input type="number" id="edit-cost" name="cost" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-total-price">Total Price:</label>
                            <input type="number" id="edit-total-price" name="total-price" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit-sales-channel">Sales Channel:</label>
                            <select id="edit-sales-channel" name="sales-channel" required>
                                <option value="physical_store">Physical Store</option>
                                <option value="shopee">Shopee</option>
                                <option value="tiktok">TikTok</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row buttons-row">
                        <button type="button" class="cancel-button">Cancel</button>
                        <button type="submit" class="save-edit-button">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>


        <script>
            function reloadAllTabs() {
                const tabs = ['all-orders', 'physical_store', 'shopee', 'tiktok']; // Add all your tab IDs here
                tabs.forEach((tab) => loadSalesData(tab));
            }


            $('#edit-order-form').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    sale_id: $('#edit-product-id').val(),
                    sale_date: $('#edit-sale-date').val(),
                    quantity: $('#edit-quantity-sold').val(),
                    total_price: $('#edit-total-price').val(),
                    channel: $('#edit-sales-channel').val(),
                };

                $.ajax({
                    url: '../../backend/controllers/update_sale.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    success: function(response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }

                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message || 'Sale updated successfully.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                            }).then(() => {
                                $('#edit-order-modal').hide(); // Close the modal
                                reloadAllTabs(); // Dynamically refresh all tabs
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to update the sale.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", xhr.responseText);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while updating the sale.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    },
                });
            });
        </script>

        <!-- Close button edit -->
        <script>
            $(document).ready(function() {
                // Handle the Cancel button click to close the modal
                $('.cancel-button').on('click', function() {
                    closeModalAndReset();
                });

                // Prevent modal from closing when clicking outside the modal content
                $('#edit-order-modal').on('click', function(event) {
                    // If the click is outside the modal content, do nothing
                    if ($(event.target).closest('.modal-content').length === 0) {
                        event.stopPropagation(); // Prevent the default click behavior
                    }
                });

                // Function to close the modal and reset the form
                function closeModalAndReset() {
                    $('#edit-order-modal').hide(); // Hide modal
                    $('#edit-order-form')[0].reset(); // Reset form fields
                    $('#edit-name').empty(); // Clear Select2 dropdown
                }
            });
        </script>


        <script>
            $(document).ready(function() {
                function resetNewOrderModal() {
                    $('#new-order-form')[0].reset(); // Reset the form fields
                    $('#name').val(null).trigger('change'); // Clear Select2 dropdown
                    $('#product-id').val(''); // Reset hidden product-id field
                    $('#cost').val(''); // Reset cost field
                    $('#quantity-sold').val(''); // Reset quantity sold
                    $('#total-price').val(''); // Reset total price field
                }

                // Handle the Cancel button click
                $('.cancel-button').on('click', function() {
                    resetNewOrderModal(); // Reset the modal fields
                    $('#new-order-modal').hide(); // Hide the modal
                });

                // Prevent the modal from closing when clicking outside of it
                $('#new-order-modal').on('click', function(event) {
                    if ($(event.target).closest('.modal-content').length === 0) {
                        // Do nothing if clicking outside modal content
                        event.stopPropagation();
                    }
                });
            });
        </script>


        <!-- Open Edit modal and retrieve -->
        <script>
            $(document).on('click', '.edit', function() {
                const saleId = $(this).data('id'); // Get sale_id from data-id attribute
                console.log("Editing sale with ID:", saleId); // Debugging

                // Fetch the sales record from the backend
                $.ajax({
                    url: '../../backend/controllers/fetch_sale_details.php',
                    type: 'GET',
                    data: {
                        sale_id: saleId
                    },
                    success: function(response) {
                        console.log("Response from fetch_sale_details:", response); // Debugging

                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }

                        if (response.success) {
                            const sale = response.data;

                            // Populate modal fields
                            $('#edit-product-id').val(sale.sale_id); // Correct sale_id
                            $('#edit-sale-date').val(new Date(sale.sale_date).toISOString().split('T')[0]);
                            $('#edit-quantity-sold').val(sale.quantity);
                            $('#edit-cost').val(sale.cost_per_item);
                            $('#edit-total-price').val(sale.total_price);
                            $('#edit-sales-channel').val(sale.channel);

                            // Dynamically populate and select the product name in the dropdown
                            const productOption = new Option(
                                `${sale.product_name} - ${sale.size} ${sale.color}`,
                                sale.variant_id,
                                true, // Mark as selected
                                true // Add to the dropdown
                            );
                            $('#edit-name').empty().append(productOption).trigger('change'); // Update and refresh Select2 dropdown

                            // Show the modal
                            $('#edit-order-modal').css('display', 'flex');
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to load sale data.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", xhr.responseText);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while fetching sale data.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    },
                });
            });
        </script>











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
                    dropdownParent: $('#new-order-modal') // Ensure dropdown stays within the modal
                });

                // Close dropdown after selection and update fields
                $('#name').on('select2:select', function(e) {
                    const selectedVariantId = e.params.data.id; // Get selected variant_id
                    const selectedProduct = productData[selectedVariantId]; // Retrieve product details by variant_id

                    if (selectedProduct) {
                        $('#product-id').val(selectedProduct.product_id); // Set the actual product_id
                        $('#cost').val(selectedProduct.price); // Set the cost field
                        $('#quantity-sold').val(''); // Clear quantity field
                        $('#total-price').val(''); // Clear total price field
                    }

                    // Close the dropdown programmatically
                    $(this).select2('close');
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
                        channel: $('#sales-channel').val(),
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

                            // Parse the response if it is a string
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }

                            if (response.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message || 'Sales record added successfully!',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
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
                                    confirmButtonText: 'OK',
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', xhr.responseText);

                            // Parse the error response if possible
                            let errorMessage = 'An error occurred while adding the sales record.';
                            if (xhr.responseText) {
                                try {
                                    const errorResponse = JSON.parse(xhr.responseText);
                                    errorMessage = errorResponse.message || errorMessage;
                                } catch (e) {
                                    console.error('Failed to parse error response:', e);
                                }
                            }

                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK',
                            });
                        },
                        complete: function() {
                            // Re-enable submit button and form inputs after AJAX completion (success or error)
                            $('#submit-btn').prop('disabled', false);
                            $('#new-order-form input, #new-order-form select').prop('disabled', false);
                        },
                    });
                });


                // Function to format currency (PHP format)
                function formatCurrency(amount) {
                    if (amount) {
                        return '₱' + parseFloat(amount).toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
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
                // Function to load sales data for a specific tab
                // Function to load sales data for a specific tab
                function loadSalesData(tab) {
                    $.ajax({
                        url: '../../backend/controllers/fetch_sales.php',
                        type: 'GET',
                        data: {
                            tab: tab
                        },
                        success: function(response) {
                            if (response.success) {
                                const tbody = $(`#${tab} .sales-table tbody`);
                                tbody.empty(); // Clear existing rows

                                // Populate table rows
                                response.data.forEach((sale) => {
                                    tbody.append(`
                        <tr>
                            <td>${sale.variant_id}</td>
                            <td>${sale.product_name}</td>
                            <td>${sale.variant}</td>
                            <td>${new Date(sale.sale_date).toLocaleDateString()}</td>
                            <td>${sale.quantity}</td>
                            <td>₱${parseFloat(sale.cost_per_item).toFixed(2)}</td>
                            <td>₱${parseFloat(sale.total_price).toFixed(2)}</td>
                            <td>${sale.channel.replace('_', ' ').toUpperCase()}</td>
                            <td>
                                <button class="action-button edit" data-id="${sale.sale_id}">Edit</button>
                                <button class="action-button archive" data-id="${sale.sale_id}">Archive</button>
                            </td>
                        </tr>
                    `);
                                });

                                console.log(`Data refreshed for tab: ${tab}`);
                                reinitializeActions(); // Reinitialize event listeners
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Failed to fetch sales data.',
                                    icon: 'error',
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while fetching sales data.',
                                icon: 'error',
                            });
                        },
                    });
                }

                // Specify only the tabs to be loaded (exclude 'archived-sales')
                const tabsToLoad = ['all-orders', 'physical_store', 'shopee', 'tiktok'];

                // Initial loading of sales data for the specified tabs
                tabsToLoad.forEach((tab) => {
                    loadSalesData(tab); // Load sales data for each specified tab on page load
                });

                // Event listener for tab switching
                $('.tab').on('click', function() {
                    const tab = $(this).data('tab'); // Get the tab name (e.g., "physical_store")

                    // Ensure the tab being switched to is one of the specified tabs
                    if (tabsToLoad.includes(tab)) {
                        // Highlight active tab
                        $('.tab').removeClass('active');
                        $(this).addClass('active');

                        // Show selected tab content and hide others
                        $('.tab-content').removeClass('active');
                        $(`#${tab}`).addClass('active');

                        // Load sales data for the selected tab
                        loadSalesData(tab);
                    } else {
                        console.log(`Tab "${tab}" is excluded from data loading.`);
                    }
                });

            });
        </script>

















        <!-- Js for archiving -->
        <script>
            function reinitializeActions() {
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
                function loadSalesData(tab, limit = 50, offset = 0) {
                    $.ajax({
                        url: '../../backend/controllers/fetch_sales.php',
                        type: 'GET',
                        data: {
                            tab: tab,
                            limit: limit,
                            offset: offset
                        },
                        success: function(response) {
                            if (response.success) {
                                const tbody = $(`#${tab} .sales-table tbody`);
                                tbody.empty(); // Clear existing rows

                                response.data.forEach((sale) => {
                                    tbody.append(`
                        <tr>
                            <td>${sale.variant_id}</td>
                            <td>${sale.product_name}</td>
                            <td>${sale.variant}</td>
                            <td>${new Date(sale.sale_date).toLocaleDateString()}</td>
                            <td>${sale.quantity}</td>
                            <td>₱${parseFloat(sale.cost_per_item).toFixed(2)}</td>
                            <td>₱${parseFloat(sale.total_price).toFixed(2)}</td>
                            <td>${sale.channel.replace('_', ' ').toUpperCase()}</td>
                            <td>
                                <button class="edit" data-id="${sale.sale_id}">Edit</button>
                                <button class="archive" data-id="${sale.sale_id}">Archive</button>
                            </td>
                        </tr>
                    `);
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Failed to fetch sales data.',
                                    icon: 'error',
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while fetching sales data.',
                                icon: 'error',
                            });
                        },
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
            }
        </script>




        <!-- JS for restoring tab -->
        <script>
            $(document).ready(function() {
                // Function to load archived sales data
                function loadArchivedSalesData() {
                    $.ajax({
                        url: '../../backend/controllers/fetch_archived_sales.php', // Path to the new fetch_archived_sales.php
                        type: 'GET',
                        dataType: 'json', // Ensure we expect JSON in response
                        success: function(response) {
                            if (response.success) {
                                const tbody = $('#archived-sales .sales-table tbody'); // Table for the archived sales tab
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
                                    <button class="action-button restore" data-id="${sale.sale_id}">Restore</button>
                                </td>
                            </tr>
                        `);
                                });

                                // Add event listener for the "Restore" button
                                $('.restore').on('click', function() {
                                    const saleId = $(this).data('id');
                                    restoreSale(saleId);
                                });

                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Failed to fetch archived sales data: ' + response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while fetching archived sales data.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }

                // Function to restore the sale
                function restoreSale(saleId) {
                    $.ajax({
                        url: '../../backend/controllers/restore_sale.php', // PHP script for restoring the sale
                        type: 'POST',
                        data: {
                            sale_id: saleId
                        },
                        dataType: 'json', // Ensure we expect JSON in response
                        success: function(response) {
                            console.log(response); // Log the response for debugging

                            if (response.success) {
                                // Show success message
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message, // Display the success message
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });

                                // Remove the restored sale from the archived sales table
                                $(`button[data-id="${saleId}"]`).closest('tr').remove();

                                // Optionally, reload the sales data in the "all-orders" tab or any other relevant tab
                                loadSalesData('all-orders'); // Reload the "all-orders" tab or any other tab where the sale should appear
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Failed to restore sale.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while restoring the sale.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }

                // Initial call to load the archived sales data
                loadArchivedSalesData();

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
            });
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










<!-- CSS for excel -->
<style>
    /* Position the upload button at the top-right corner */
    .top-right-button {
        position: absolute;
        top: 15px;
        right: 15px;
    }

    .upload-excel-button {
        background-color: #007bff;
        /* Blue background */
        color: white;
        /* White text */
        border: none;
        border-radius: 5px;
        padding: 10px 15px;
        font-size: 14px;
        cursor: pointer;
    }

    .upload-excel-button:hover {
        background-color: #0056b3;
        /* Darker blue on hover */
    }

    .upload-excel-label {
        cursor: pointer;
        display: inline-block;
    }
</style>


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


<!-- CSS for the edit modal -->
<style>
    /* Overall Modal Styling */
    .modal {
        display: none;
        /* Hidden by default */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* Semi-transparent background */
        justify-content: center;
        /* Horizontally center */
        align-items: center;
        /* Vertically center */
    }

    .modal-content {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        width: 50%;
        /* Adjusted width for the modal */
        max-width: 90%;
        /* Ensure it doesn't stretch too wide on smaller screens */
        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        position: relative;
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

    /* Header Style */
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

    /* Form Row and Group Styling */
    .form-row {
        margin-bottom: 15px;
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

    /* Input Styling */
    .form-group input,
    .form-group select {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 12px;
        background-color: #f9f9f9;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #007bff;
        outline: none;
    }

    #sales-channel,
    #name {
        width: 100%;
        padding: 8px;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    #sales-channel:focus,
    #name:focus {
        border-color: #007bff;
        outline: none;
    }

    /* Action Buttons (Cancel & Save) */
    .buttons-row {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .cancel-button,
    .save-edit-button {
        padding: 10px 15px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        width: 48%;
    }

    .cancel-button {
        background-color: transparent;
        color: #007bff;
        border: 2px solid #007bff;
    }

    .cancel-button:hover {
        background-color: #f0f0ff;
    }

    .save-edit-button {
        background-color: #007bff;
        color: white;
    }

    .save-edit-button:hover {
        background-color: #0056b3;
    }

    /* Dropdown Styling */
    #sales-channel,
    #name {
        padding: 8px;
        border-radius: 5px;
        font-size: 12px;
        background-color: #f9f9f9;
        width: 100%;
        transition: border-color 0.3s;
    }

    #sales-channel:focus,
    #name:focus {
        border-color: #007bff;
        outline: none;
    }

    .select2-container .select2-selection--single {
        height: 40px;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 12px;
        background-color: #f9f9f9;
        transition: border-color 0.3s ease;
    }

    /* Dropdown Menu Customizations */
    .select2-container .select2-selection__arrow {
        height: 100%;
        top: 0;
        right: 10px;
        width: 30px;
    }

    .select2-container .select2-results__option {
        padding: 8px 12px;
        font-size: 12px;
    }

    .select2-container .select2-results__option--highlighted {
        background-color: #007bff;
        color: white;
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


<style>
    .tab[data-tab="archived-sales"] {
        background-color: #3CAE85;
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

    .tab[data-tab="archived-sales"]:hover {
        background-color: #3CAE85;
    }

    .tab[data-tab="archived-sales"].active {
        background-color: white;
        color: #3CAE85;
        z-index: 2;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
    }
</style>




<!-- 
//hindi nagsasave, 
//dapat icheck kung may tamang quantity sa channel -->