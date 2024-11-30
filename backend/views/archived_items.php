<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: ../../frontend/public/login.html");
    exit();
}
require_once '../../backend/config/db_connection.php';
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$sql_all_inventory = "
    SELECT pv.variant_id, p.name, p.category, pv.size, pv.color, pv.price, pv.date_added, pv.image,
    pv.is_archived, pv.date_archived
    FROM product_variants pv
    JOIN products p ON pv.product_id = p.product_id
    WHERE pv.is_archived = 1";
$result_all_inventory = mysqli_query($conn, $sql_all_inventory);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Archived Inventory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>

<body>
    <div class="inventory-container">
        <div class="header">
            <h1>Archived Inventory</h1>
        </div>
        <div class="filters">
            <div class="tabs-container">
                <button class="tab inactive" data-tab="all-inventory"><i class="fas fa-warehouse"></i> All Inventory</button>
            </div>
            <div class="filter-input-container">
                <input type="text" class="filter-input" placeholder="Type to filter archived items">
                <i class="fas fa-filter icon-filter"></i>
            </div>
        </div>

        <!-- All Inventory Tab -->
        <div id="all-inventory" class="tab-content active" data-type="variant">
            <table class="inventory-table inventory-table-all">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all-all"></th> <!-- Checkbox for selecting all items -->
                        <th>Variant ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Price</th>
                        <th>Date Added</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result_all_inventory) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_all_inventory)): ?>
                            <tr data-id="<?php echo $row['variant_id']; ?>">
                                <td><input type="checkbox" class="select-item-all" value="<?php echo $row['variant_id']; ?>"></td> <!-- Individual checkbox -->
                                <td><?php echo $row['variant_id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['size']; ?></td>
                                <td><?php echo $row['color']; ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><?php echo $row['date_added']; ?></td>
                                <td><img src="../../frontend/public/images/<?php echo $row['image'] ?: 'image-placeholder.png'; ?>" alt="Image" width="50"></td>
                                <td>
                                    <button class="unarchive-button" data-id="<?php echo $row['variant_id']; ?>">Unarchive</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">No archived items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
                   <!-- Pagination Controls -->
    <div class="pagination" id="pagination-controls">
        <!-- Pagination links will be populated here via AJAX -->
    </div>
        </div>


    </div>


    <script>
$(document).ready(function() {
    let currentPage = 1; // Default page
    const inventoryTbody = $('.inventory-table tbody'); // Table body to insert rows
    const paginationControls = $('#pagination-controls'); // Pagination container

    // Function to fetch inventory data for a specific page
 // Function to fetch inventory data for a specific page
// Function to fetch inventory data for a specific page
function fetchInventory(page) {
    $.ajax({
        url: '../../backend/controllers/fetch_archive.php',
        type: 'GET',
        data: { page: page },
        dataType: 'json',
        success: function(response) {
            // Clear the existing table rows
            inventoryTbody.empty();

            // Populate the table rows
            response.inventory.forEach(item => {
                inventoryTbody.append(`
                    <tr data-id="${item.variant_id}">
                        <td><input type="checkbox" class="select-item-all" value="${item.variant_id}"></td>
                        <td>${item.variant_id}</td>
                        <td>${item.name}</td>
                        <td>${item.category}</td>
                        <td>${item.size}</td>
                        <td>${item.color}</td>
                        <td>${item.price}</td>
                        <td>${item.date_added}</td>
                        <td><img src="../../frontend/public/images/${item.image || 'image-placeholder.png'}" alt="Image" width="50"></td>
                        <td><button class="unarchive-button" data-id="${item.variant_id}">Unarchive</button></td>
                    </tr>
                `);
            });

            // Reapply the "select all" checkbox functionality after new rows are added
            updateSelectAllCheckbox();

            // Reapply the event listeners for the newly appended checkboxes
            addCheckboxListeners();  // Rebind checkbox change listeners

            // Update pagination controls
            generatePagination(response.total_pages, response.current_page);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching inventory:', error);
        }
    });
}


    // Function to generate pagination controls
    function generatePagination(totalPages, currentPage) {
        paginationControls.empty(); // Clear previous pagination controls

        if (totalPages <= 1) {
            return; // No pagination needed
        }

        let pagination = '<div class="pagination">';

        // Previous button
        if (currentPage > 1) {
            pagination += `<a href="#" data-page="1">&laquo; First</a>`;
            pagination += `<a href="#" data-page="${currentPage - 1}">Previous</a>`;
        }

        // Current page indicator
        pagination += `<span>Page ${currentPage} of ${totalPages}</span>`;

        // Next button
        if (currentPage < totalPages) {
            pagination += `<a href="#" data-page="${currentPage + 1}">Next</a>`;
            pagination += `<a href="#" data-page="${totalPages}">Last &raquo;</a>`;
        }

        pagination += '</div>';

        paginationControls.append(pagination);

        // Event listener for pagination links
        $('.pagination a').on('click', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            currentPage = page; // Update the current page
            fetchInventory(page); // Fetch the new data
        });
    }

    // Initial fetch for the first page
    fetchInventory(currentPage);
});
</script>


</div>



<script>
    const selectAllCheckbox = document.getElementById('select-all-all');
    const individualCheckboxes = document.querySelectorAll('.select-item-all');
    const selectionBar = document.getElementById('selection-bar');
    const selectedCount = document.getElementById('selected-count');

    let selectedItems = [];

    // Function to update the selection bar visibility and count
    function updateSelectionBar() {
        const selectedCountText = `${selectedItems.length} items selected`;
        selectedCount.textContent = selectedCountText;

        // Show the selection bar if items are selected, else hide it
        if (selectedItems.length > 0) {
            selectionBar.style.display = 'block';
        } else {
            selectionBar.style.display = 'none';
        }

        // Update the "Select All" checkbox based on individual selections
        updateSelectAllCheckbox();
    }

    // Select/Deselect all checkboxes (across all pages)
    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = selectAllCheckbox.checked;

        // Get all checkboxes in the table (including dynamically rendered ones)
        const allCheckboxes = document.querySelectorAll('.select-item-all');
        
        // Check or uncheck all checkboxes
        allCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
            toggleItemSelection(checkbox); // Update the selectedItems array and the selection bar
        });
    });

    // Function to update the "Select All" checkbox state
    function updateSelectAllCheckbox() {
        const allCheckboxes = document.querySelectorAll('.select-item-all');
        const selectedCheckboxes = document.querySelectorAll('.select-item-all:checked');

        // If all checkboxes are selected, check the "Select All" checkbox, else uncheck it
        if (allCheckboxes.length === selectedCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false; // Clear indeterminate state
        } else if (selectedCheckboxes.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true; // Set indeterminate state (some selected)
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false; // Clear indeterminate state
        }
    }

    // Toggle individual item selection
    individualCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleItemSelection(checkbox);
        });
    });

    // Add/remove item ID from selectedItems array
    function toggleItemSelection(checkbox) {
        const itemId = checkbox.value;
        if (checkbox.checked) {
            if (!selectedItems.includes(itemId)) {
                selectedItems.push(itemId); // Add item to selection
            }
        } else {
            selectedItems = selectedItems.filter(id => id !== itemId); // Remove item from selection
        }
        updateSelectionBar(); // Update the selection bar visibility and count
    }

    // Function to reset all checkboxes
    function resetCheckboxes() {
        const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
        allCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectionBar(); // Ensure the selection bar is updated after resetting checkboxes
    }

    // Function to add listeners for individual checkbox changes
    function addCheckboxListeners() {
        const allCheckboxes = document.querySelectorAll('.select-item-all');

        allCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                toggleItemSelection(checkbox); // Toggle item selection
                updateSelectAllCheckbox(); // Update the "Select All" checkbox state
            });
        });
    }

    // Batch Unarchive button functionality
    document.getElementById('unarchive-button').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
        const selectedIds = new Set(
            Array.from(checkboxes)
            .filter(checkbox => !checkbox.id.includes('select-all')) // Exclude "Select All" checkboxes
            .map(checkbox => checkbox.value) // Get the variant IDs
        );

        let unarchivedItemCount = 0;

        if (selectedIds.size === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Items Selected',
                text: 'Please select items to unarchive.',
                confirmButtonText: 'OK'
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `Are you sure you want to unarchive ${selectedIds.size} item(s)?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, unarchive them!'
        }).then((result) => {
            if (result.isConfirmed) {
                let unarchivePromises = Array.from(selectedIds).map(itemId => {
                    return fetch('../../backend/controllers/unarchive_item.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                variant_id: itemId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Item successfully unarchived, remove the row from the table
                                const row = document.querySelector(`tr[data-id="${itemId}"]`);
                                if (row) {
                                    row.remove(); // Remove the row from the table
                                }

                                unarchivedItemCount++; // Increment the count of successfully unarchived items
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: `Error unarchiving item ${itemId}: ${data.message || 'Unknown error'}`,
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Unexpected Error',
                                text: `An unexpected error occurred for item ${itemId}. Please try again later.`,
                                confirmButtonText: 'OK'
                            });
                        });
                });

                // Wait for all unarchive promises to complete
                Promise.all(unarchivePromises).then(() => {
                    if (unarchivedItemCount > 0) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Unarchiving Completed',
                            text: `${unarchivedItemCount} item(s) have been successfully unarchived.`,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'No Items Unarchived',
                            text: 'No items were unarchived.',
                            confirmButtonText: 'OK'
                        });
                    }

                    // Update the selection bar after unarchiving
                    selectedItems = []; // Clear the selected items array
                    updateSelectionBar(); // Ensure the selection bar is hidden if no items are selected

                    // After unarchiving, ensure the "Select All" checkbox is unchecked if no items are selected
                    updateSelectAllCheckbox();
                });
            }
        });
    });

    // Initialize the page
    addCheckboxListeners();
    initializeCheckboxes();
</script>




<!-- Selection bar and Unarchiving -->
<div id="selection-bar" style="display: none;">
    <span id="selected-count">0 items selected</span>
    <button id="unarchive-button">Unarchive</button>
</div>

<!-- Filter -->
<script>
    // Utility function for debouncing
    function debounce(func, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Function to filter archived inventory rows
    function filterArchivedRows(event) {
        const keyword = event.target.value.toLowerCase().trim();
        const container = event.target.closest('.inventory-container');
        const tabContent = container.querySelector('.tab-content.active');

        if (!tabContent) return;

        const rows = tabContent.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cells = Array.from(row.querySelectorAll('td:not(:has(img, input, button))')); // Skip non-text elements
            let rowMatches = false;

            // Check if any cell matches the keyword
            cells.forEach(cell => {
                const originalText = cell.getAttribute('data-original-text') || cell.textContent;
                if (!cell.hasAttribute('data-original-text')) {
                    cell.setAttribute('data-original-text', originalText); // Save original content
                }

                const cellText = originalText.toLowerCase();
                if (cellText.includes(keyword)) {
                    rowMatches = true;
                    // Highlight matching text
                    const regex = new RegExp(`(${keyword})`, 'gi');
                    cell.innerHTML = originalText.replace(regex, '<mark>$1</mark>');
                } else {
                    cell.innerHTML = originalText; // Reset to original content
                }
            });

            // Toggle row visibility
            row.style.display = rowMatches ? '' : 'none';
        });

        // Reset rows when keyword is empty
        if (!keyword) {
            resetArchivedTableFilters(container);
        }
    }

    // Function to reset all rows and remove highlights
    function resetArchivedTableFilters(container) {
        const tabContent = container.querySelector('.tab-content.active');
        if (!tabContent) return;

        const rows = tabContent.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.style.display = ''; // Reset visibility
            const cells = row.querySelectorAll('td');
            cells.forEach(cell => {
                const originalText = cell.getAttribute('data-original-text');
                if (originalText) {
                    cell.innerHTML = originalText; // Restore original content
                }
            });
        });
    }

    // Attach input event listener to the filter input
    document.querySelector('.filter-input').addEventListener(
        'input',
        debounce(filterArchivedRows, 300)
    );
</script>



<script>
    // Function to handle "Select All" checkbox for filtered rows
    function handleSelectAll(event) {
        const container = event.target.closest('.inventory-container');
        const tabContent = container.querySelector('.tab-content.active');
        if (!tabContent) return;

        // Get all visible checkboxes in the filtered table
        const checkboxes = tabContent.querySelectorAll('tbody tr:visible input[type="checkbox"]');
        const isChecked = event.target.checked;

        // Set the checked state of each visible checkbox
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    }

    // Attach event listener to "Select All" checkbox
    document.getElementById('select-all-all').addEventListener('change', handleSelectAll);

    // jQuery way to ensure only visible rows are considered when filtering
    (function($) {
        $.fn.extend({
            visible: function() {
                return this.filter(function() {
                    return $(this).css('display') !== 'none';
                });
            }
        });
    })(jQuery);
</script>


<!-- Selection bar css -->
<style>
    /* Basic Reset */
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    /* Selection Bar Styling */
    #selection-bar {
        display: none;
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #201F2B;
        color: white;
        padding: 12px 20px;
        /* Reduced padding for better fit */
        border-radius: 12px;
        box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
        font-size: 14px;
        /* Reduced font size */
        z-index: 1000;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-width: 280px;
        max-width: 450px;
        transition: all 0.3s ease-in-out;
    }

    /* Text Styling */
    #selected-count {
        font-weight: 600;
        letter-spacing: 0.5px;
        font-size: 13px;
        /* Slightly smaller font size */
    }

    /* Button Styling */
    #unarchive-button {
        background-color: #3CAE85;
        color: white;
        border: none;
        padding: 8px 16px;
        /* Reduced padding for smaller button */
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        /* Adjusted button font size */
        transition: background-color 0.3s ease, transform 0.2s ease;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Hover and Active Effects for the Button */
    #unarchive-button:hover {
        background-color: #287458;
        transform: translateY(-2px);
    }

    #unarchive-button:active {
        transform: translateY(1px);
    }

    /* Small screens responsiveness */
    @media (max-width: 480px) {
        #selection-bar {
            min-width: 220px;
            padding: 12px 18px;
            font-size: 12px;
            /* Further reduced font size on small screens */
        }

        #unarchive-button {
            font-size: 13px;
            padding: 8px 14px;
        }
    }
</style>


<!-- CSS filter -->
<style>
    /* Filter input styling */
    .filters {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filter-input-container {
        position: relative;
        display: inline-block;
        margin-bottom: 10px;
        margin-left: auto;
        /* This pushes it to the right */
    }

    .filter-input {
        padding: 6px 10px;
        font-size: 12px;
        border: 1px solid #ccc;
        border-radius: 18px;
        width: 220px;
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
        font-size: 14px;
        color: #888;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .icon-filter:hover {
        color: #0056b3;
    }
</style>






<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Tab switching functionality (ensuring tabs always stay inactive)
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function(event) {
            // Prevent the default behavior and don't change the tab's state
            event.preventDefault();

            // Tabs should never become active
            return false;
        });
    });

    // Event delegation for unarchive buttons
    document.addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('unarchive-button')) {
            const variantId = event.target.getAttribute('data-id');
            const row = document.querySelector(`tr[data-id="${variantId}"]`);
            const itemName = row ? row.querySelector('td:nth-child(3)').innerText : 'this item';

            Swal.fire({
                title: 'Are you sure?',
                text: `Are you sure you want to unarchive ${itemName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, unarchive it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../../backend/controllers/unarchive_item.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                variant_id: variantId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: data.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Remove the row of the unarchived item
                                    if (row) {
                                        row.remove(); // Remove the row from the DOM

                                        // Optionally, remove it from the other inventory tables
                                        // You may need to check if the item is in other tab inventories and remove it
                                        document.querySelectorAll(`tr[data-id="${variantId}"]`).forEach(itemRow => {
                                            itemRow.remove();
                                        });
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error unarchiving item: ' + (data.message || 'Unknown error'),
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Unexpected Error',
                                text: 'An unexpected error occurred. Please try again later.',
                                confirmButtonText: 'OK'
                            });
                        });
                }
            });
        }
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
            height: 100vh;
            overflow: hidden;
        }

        .unarchive-button {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.3s;
        }

        .unarchive-button:hover {
            background-color: #218838;
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
            font-size: 22px;
            color: #333;
            font-weight: 600;
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

        /* Apply the same styling to the inventory table */
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Ensures consistent column widths */
        }

        .inventory-table thead {
            position: sticky;
            top: 0;
            background-color: #f4f7fc;
            z-index: 1;
            font-weight: 600;
            color: #555;
        }

        .inventory-table th,
        .inventory-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 12px;
        }

        .inventory-table th {
            background-color: #f4f7fc;
        }

        .inventory-table td {
            color: #555;
        }

        .inventory-table img {
            border-radius: 5px;
        }

        /* Scrollable tbody */
        .inventory-table tbody {
            display: block;
            max-height: 66vh;
            overflow-y: auto;
            width: 100%;
        }

        .inventory-table thead,
        .inventory-table tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        /* Specific column widths for inventory */

        .inventory-table th,
        .inventory-table td{
            text-align: center; /* Center align all table headers and data cells */
        }

        .inventory-table th:nth-child(1),
        .inventory-table td:nth-child(1) {
            width: 50px;
            /* Checkbox column */
            text-align: center; /* Center align all table headers and data cells */
        }

        .inventory-table th:nth-child(2),
        .inventory-table td:nth-child(2) {
            width: 80px;
            /* Variant ID column */
            text-align: center; /* Center align all table headers and data cells */
        }

        .inventory-table th:nth-child(3),
        .inventory-table td:nth-child(3) {
            width: 150px;
            /* Product Name column */
            text-align: center; /* Center align all table headers and data cells */
        }

        .inventory-table th:nth-child(4),
        .inventory-table td:nth-child(4) {
            width: 150px;
            /* Variant (Size/Color) column */
            text-align: center; /* Center align all table headers and data cells */
        }

        .inventory-table th:nth-child(5),
        .inventory-table td:nth-child(5) {
            width: 120px;
            /* Sale Date column */
            text-align: center; /* Center align all table headers and data cells */
        }

        .inventory-table th:nth-child(6),
        .inventory-table td:nth-child(6) {
            width: 100px;
            /* Quantity Sold column */
            text-align: center; /* Center align all table headers and data cells */
        }

        .inventory-table th:nth-child(7),
        .inventory-table td:nth-child(7) {
            width: 120px;
            /* Cost per Item column */
            text-align: center; /* Center align all table headers and data cells */
        }

        .inventory-table th:nth-child(8),
        .inventory-table td:nth-child(8) {
            width: 100px;
            /* Total Sales column */
            text-align: center; /* Center align all table headers and data cells */
        }

        .inventory-table th:nth-child(9),
        .inventory-table td:nth-child(9) {
            width: 120px;
            /* Sales Channel column */
            text-align: center; /* Center align all table headers and data cells */
        }

        .inventory-table th:nth-child(10),
        .inventory-table td:nth-child(10) {
            width: 150px;
            /* Actions column */
            text-align: center; /* Center align all table headers and data cells */
        }

        /* Checkbox column styling */
        .inventory-table td.checkbox-column {
            width: 50px;
            padding: 0;
            text-align: center;
            vertical-align: middle;
        }

        /* Styling for checkboxes */
        input[type="checkbox"] {
            appearance: none;
            width: 16px;
            height: 16px;
            background-color: #ffffff;
            border: 2px solid #007bff;
            border-radius: 3px;
            position: relative;
            cursor: pointer;
            outline: none;
            transition: background-color 0.3s, border-color 0.3s;
        }

        input[type="checkbox"]:checked {
            background-color: #007bff;
            border-color: #007bff;
        }

        input[type="checkbox"]:checked::after {
            content: "";
            position: absolute;
            left: 4px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        input[type="checkbox"]:hover {
            border-color: #0056b3;
        }

        input[type="checkbox"]:checked:hover {
            background-color: #0056b3;
        }

        /* Hover effect on checkboxes in a table */
        .inventory-table td.checkbox-column input[type="checkbox"]:hover {
            background-color: #f0f0f0;
        }
    </style>



<style>/* Container for the table and pagination */
.inventory-container {
    padding: 20px;
}

/* Style for the inventory table */
.inventory-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px; /* Add spacing below the table */
}

.inventory-table th, .inventory-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

/* Pagination container styled to stay below the table */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: -4px; /* Adds space between table and pagination */
    font-family: 'Arial', sans-serif;
}

/* Style for each pagination link */
.pagination a {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 40px;
    height: 40px;
    margin: 0 8px;
    text-decoration: none;
    font-size: 14px;
    color: #333;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 50%;
    transition: all 0.3s ease;
    text-align: center;
}

/* Hover effect for pagination links */
.pagination a:hover {
    background-color: #ddd;
    border-color: #ccc;
}

/* Active page link */
.pagination a.active {
    background-color: #4a90e2; /* Blue color for the active state */
    color: #fff;
    font-weight: bold;
    border-color: #4a90e2;
}

/* Disabled link (e.g., 'Previous' or 'Next' on first/last page) */
.pagination a.disabled {
    pointer-events: none;
    opacity: 0.5;
}

/* Styling for first and last page buttons */
.pagination a:first-child,
.pagination a:last-child {
    width: auto;
    padding: 0 15px;
    border-radius: 30px;
    font-size: 14px;
}

/* Special hover effect for first and last page buttons */
.pagination a:first-child:hover,
.pagination a:last-child:hover {
    background-color: #ddd;
}

/* Style for the page number display */
.pagination span {
    font-size: 14px;
    color: #333;
    padding: 0 10px;
}


</style>