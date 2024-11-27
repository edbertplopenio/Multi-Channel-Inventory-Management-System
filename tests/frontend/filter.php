<?php
session_start();
// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_email'])) {
    header("Location: ../../frontend/public/login.html");
    exit();
}

// Include the database connection file
require_once '../../backend/config/db_connection.php';

// Verify the database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch aggregated data for All Inventory tab, excluding archived items
$sql_all_inventory = "
    SELECT 
        pv.variant_id, 
        p.name, 
        p.category, 
        pv.size, 
        pv.color, 
        pv.price, 
        pv.date_added, 
        pv.image,   -- The image is now fetched from product_variants
        SUM(CASE WHEN i.channel = 'physical_store' THEN i.quantity ELSE 0 END) AS quantity_physical_store,
        SUM(CASE WHEN i.channel = 'shopee' THEN i.quantity ELSE 0 END) AS quantity_shopee,
        SUM(CASE WHEN i.channel = 'tiktok' THEN i.quantity ELSE 0 END) AS quantity_tiktok,
        GROUP_CONCAT(DISTINCT i.channel ORDER BY i.channel SEPARATOR ', ') as channels
    FROM product_variants pv
    JOIN products p ON pv.product_id = p.product_id
    JOIN inventory i ON pv.variant_id = i.variant_id
    WHERE pv.is_archived = 0
    GROUP BY pv.variant_id, pv.size, pv.color, pv.price, pv.date_added, pv.image";
$result_all_inventory = mysqli_query($conn, $sql_all_inventory);

if (!$result_all_inventory) {
    die("Error executing query for All Inventory: " . mysqli_error($conn));
}

// Fetch data for Physical Store inventory, excluding archived items
$sql_physical_store = "SELECT pv.variant_id, p.product_id, p.name, p.category, pv.size, pv.color, pv.price, pv.date_added, pv.image, i.channel, i.quantity
                       FROM product_variants pv
                       JOIN products p ON pv.product_id = p.product_id
                       JOIN inventory i ON pv.variant_id = i.variant_id
                       WHERE i.channel = 'physical_store' AND pv.is_archived = 0";
$result_physical_store = mysqli_query($conn, $sql_physical_store);

if (!$result_physical_store) {
    die("Error executing query for Physical Store: " . mysqli_error($conn));
}

// Fetch data for Shopee inventory, excluding archived items
$sql_shopee = "SELECT pv.variant_id, p.product_id, p.name, p.category, pv.size, pv.color, pv.price, pv.date_added, pv.image, i.channel, i.quantity
               FROM product_variants pv
               JOIN products p ON pv.product_id = p.product_id
               JOIN inventory i ON pv.variant_id = i.variant_id
               WHERE i.channel = 'shopee' AND pv.is_archived = 0";
$result_shopee = mysqli_query($conn, $sql_shopee);

if (!$result_shopee) {
    die("Error executing query for Shopee: " . mysqli_error($conn));
}

// Fetch data for TikTok inventory, excluding archived items
$sql_tiktok = "SELECT pv.variant_id, p.product_id, p.name, p.category, pv.size, pv.color, pv.price, pv.date_added, pv.image, i.channel, i.quantity
               FROM product_variants pv
               JOIN products p ON pv.product_id = p.product_id
               JOIN inventory i ON pv.variant_id = i.variant_id
               WHERE i.channel = 'tiktok' AND pv.is_archived = 0";
$result_tiktok = mysqli_query($conn, $sql_tiktok);

if (!$result_tiktok) {
    die("Error executing query for TikTok: " . mysqli_error($conn));
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
            </div>

            <script>
                document.querySelector('.filter-input').addEventListener('input', function () {
    const keyword = this.value.toLowerCase().trim();
    const activeTabContent = document.querySelector('.tab-content.active');
    
    if (!activeTabContent) {
        console.error('No active tab found.');
        return;
    }

    const rows = activeTabContent.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const rowText = Array.from(row.querySelectorAll('td'))
            .map(td => td.textContent.toLowerCase())
            .join(' ');
        row.style.display = rowText.includes(keyword) ? '' : 'none';
    });
});

            </script>
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
                    <?php if (mysqli_num_rows($result_all_inventory) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_all_inventory)): ?>
                            <tr data-item-id="<?php echo $row['variant_id']; ?>"
                                data-quantity-physical-store="<?php echo $row['quantity_physical_store']; ?>"
                                data-quantity-shopee="<?php echo $row['quantity_shopee']; ?>"
                                data-quantity-tiktok="<?php echo $row['quantity_tiktok']; ?>">
                                <td><input type="checkbox" class="select-item-all" value="<?php echo $row['variant_id']; ?>"></td> <!-- Individual checkbox -->
                                <td><?php echo $row['variant_id']; ?></td>
                                <td class="wrap-text"><?php echo $row['name']; ?></td>
                                <td class="wrap-text"><?php echo $row['category']; ?></td>
                                <td><?php echo $row['quantity_physical_store'] + $row['quantity_shopee'] + $row['quantity_tiktok']; ?></td>
                                <td><?php echo $row['size']; ?></td>
                                <td><?php echo $row['color']; ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><?php echo $row['date_added']; ?></td>
                                <td class="wrap-text">
                                    <?php
                                    $displayChannels = [];
                                    if ($row['quantity_physical_store'] > 0) {
                                        $displayChannels[] = "Physical Store";
                                    }
                                    if ($row['quantity_shopee'] > 0) {
                                        $displayChannels[] = "Shopee";
                                    }
                                    if ($row['quantity_tiktok'] > 0) {
                                        $displayChannels[] = "TikTok";
                                    }

                                    if (count($displayChannels) === 3) {
                                        echo 'All Channels';
                                    } else {
                                        echo implode(' and ', $displayChannels);
                                    }
                                    ?>
                                </td>
                                <td><img src="../../frontend/public/images/<?php echo $row['image'] ?: 'image-placeholder.png'; ?>" alt="Image" width="50"></td>
                                <td>
                                <button class="action-button edit-inventory" data-variant-id="<?php echo $row['variant_id']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                                    <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12">No inventory items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Physical Store Tab -->
        <div id="physical-store" class="tab-content" data-type="inventory">
            <table class="inventory-table inventory-table-physical">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all-physical"></th> <!-- Checkbox for selecting all items -->
                        <th>Variant ID</th>
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
                    <?php if (mysqli_num_rows($result_physical_store) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_physical_store)): ?>
                            <tr data-item-id="<?php echo $row['variant_id']; ?>">
                                <td><input type="checkbox" class="select-item-physical" value="<?php echo $row['variant_id']; ?>"></td> <!-- Individual checkbox -->
                                <td><?php echo $row['variant_id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><?php echo $row['size']; ?></td>
                                <td><?php echo $row['color']; ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><?php echo $row['date_added']; ?></td>
                                <td><img src="../../frontend/public/images/<?php echo $row['image'] ?: 'image-placeholder.png'; ?>" alt="Image" width="50"></td>
                                <td>
                                <button class="action-button edit-inventory" data-variant-id="<?php echo $row['variant_id']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                                    <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11">No inventory items found in Physical Store.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Shopee Tab -->
        <div id="shopee" class="tab-content" data-type="inventory">
            <table class="inventory-table inventory-table-shopee">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all-shopee"></th> <!-- Checkbox for selecting all items -->
                        <th>Variant ID</th>
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
                    <?php if (mysqli_num_rows($result_shopee) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_shopee)): ?>
                            <tr data-item-id="<?php echo $row['variant_id']; ?>">
                                <td><input type="checkbox" class="select-item-shopee" value="<?php echo $row['variant_id']; ?>"></td> <!-- Individual checkbox -->
                                <td><?php echo $row['variant_id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><?php echo $row['size']; ?></td>
                                <td><?php echo $row['color']; ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><?php echo $row['date_added']; ?></td>
                                <td><img src="../../frontend/public/images/<?php echo $row['image'] ?: 'image-placeholder.png'; ?>" alt="Image" width="50"></td>
                                <td>
                                <button class="action-button edit-inventory" data-variant-id="<?php echo $row['variant_id']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                                    <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11">No inventory items found in Shopee.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TikTok Tab -->
        <div id="tiktok" class="tab-content" data-type="inventory">
            <table class="inventory-table inventory-table-tiktok">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all-tiktok"></th> <!-- Checkbox for selecting all items -->
                        <th>Variant ID</th>
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
                    <?php if (mysqli_num_rows($result_tiktok) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_tiktok)): ?>
                            <tr data-item-id="<?php echo $row['variant_id']; ?>">
                                <td><input type="checkbox" class="select-item-tiktok" value="<?php echo $row['variant_id']; ?>"></td> <!-- Individual checkbox -->
                                <td><?php echo $row['variant_id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><?php echo $row['size']; ?></td>
                                <td><?php echo $row['color']; ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><?php echo $row['date_added']; ?></td>
                                <td><img src="../../frontend/public/images/<?php echo $row['image'] ?: 'image-placeholder.png'; ?>" alt="Image" width="50"></td>
                                <td>
                                <button class="action-button edit-inventory" data-variant-id="<?php echo $row['variant_id']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                                    <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11">No inventory items found in TikTok.</td>
                        </tr>
                    <?php endif; ?>
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
                        <label for="date_added">Date Added:</label>
                        <input type="date" id="date_added" name="date_added" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="image">Image:</label>
                        <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/jpg" required>
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


    <!-- All JS -->
    <script>
        function initializeInventoryManagement() {
            let originalData = [];
            let lastCheckedProduct = null;
            let isVariantMode = false;
            let existingProductId = null;

            function capitalizeWords(str) {
                return str.replace(/\b\w/g, char => char.toUpperCase());
            }

            function refreshInventory() {
                fetch('../../backend/controllers/get_inventory.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            populateInventoryTables(data.items);
                        } else {
                            console.error('Error fetching inventory:', data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function attachArchiveButtonListeners() {
                document.querySelectorAll('.action-button.archive').forEach(button => {
                    button.addEventListener('click', function() {
                        console.log('Archive button clicked'); // Check if this logs in the console
                        const row = button.closest('tr');
                        const itemId = row.getAttribute('data-item-id');
                        const physicalQuantity = parseInt(row.querySelector('td:nth-child(5)').textContent) || 0;
                        const shopeeQuantity = parseInt(row.querySelector('td:nth-child(6)').textContent) || 0;
                        const tiktokQuantity = parseInt(row.querySelector('td:nth-child(7)').textContent) || 0;

                        // Check if all quantities are 0
                        const totalQuantity = physicalQuantity + shopeeQuantity + tiktokQuantity;

                        if (totalQuantity > 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Cannot Archive',
                                text: 'This item cannot be archived as it still has quantity in stock.',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        // Proceed with the archive confirmation if total quantity is 0
                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'This item will be archived.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, archive it!',
                            cancelButtonText: 'No, keep it'
                        }).then(result => {
                            if (result.isConfirmed) {
                                // Send archive request to the server
                                fetch('../../backend/controllers/archive_item.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            item_id: itemId
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'success') {
                                            Swal.fire('Archived!', data.message, 'success');
                                            refreshInventory(); // Refresh all tabs after archiving
                                        } else {
                                            Swal.fire('Error!', data.message, 'error');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        Swal.fire('Error!', 'Something went wrong while archiving the item.', 'error');
                                    });
                            }
                        });
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                function initializeSelectAllFeature() {
                    const selectAllCheckboxes = document.querySelectorAll('input[type="checkbox"][id^="select_all"]');

                    selectAllCheckboxes.forEach(selectAllCheckbox => {
                        selectAllCheckbox.addEventListener('change', function() {
                            const table = selectAllCheckbox.closest('.inventory-table');
                            const rowCheckboxes = table.querySelectorAll('input[name="select_variant[]"]');

                            // Set the checked state of each row checkbox to match the header checkbox
                            rowCheckboxes.forEach(rowCheckbox => {
                                rowCheckbox.checked = selectAllCheckbox.checked;
                            });

                            updateSelectionBar();
                        });
                    });

                    document.querySelectorAll('.inventory-table input[name="select_variant[]"]').forEach(checkbox => {
                        checkbox.addEventListener('change', updateSelectionBar);
                    });
                }

                function updateSelectionBar() {
                    const activeTabContent = document.querySelector('.tab-content.active');
                    const rowCheckboxes = activeTabContent.querySelectorAll('input[name="select_variant[]"]');
                    const selectedItems = activeTabContent.querySelectorAll('input[name="select_variant[]"]:checked');
                    const selectedCount = selectedItems.length;
                    const selectionBar = document.getElementById("selection-bar");
                    const selectedCountDisplay = document.getElementById("selected-count");

                    selectedCountDisplay.textContent = `${selectedCount} items selected`;

                    // Show or hide the selection bar based on the number of selected items
                    if (selectedCount > 0) {
                        selectionBar.classList.remove('hidden');
                    } else {
                        selectionBar.classList.add('hidden');
                    }

                    // Update the header checkbox state based on individual row checkboxes
                    const selectAllCheckbox = activeTabContent.querySelector('input[type="checkbox"][id^="select_all"]');
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = selectedItems.length === rowCheckboxes.length && rowCheckboxes.length > 0;
                    }
                }

                function initializeTabClickListener() {
                    document.querySelector('.tabs-container').addEventListener('click', function(event) {
                        if (event.target.classList.contains('tab')) {
                            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));

                            event.target.classList.add('active');
                            const activeTabContent = document.getElementById(event.target.getAttribute('data-tab'));
                            activeTabContent.classList.add('active');

                            updateSelectionBar();
                        }
                    });
                }

                function initializeArchiveButton() {
                    const selectionBar = document.getElementById("selection-bar");
                    const archiveButton = selectionBar.querySelector('.action-button.archive');

                    archiveButton.addEventListener('click', function() {
                        const activeTabContent = document.querySelector('.tab-content.active');
                        const selectedItems = activeTabContent.querySelectorAll('input[name="select_variant[]"]:checked');
                        const selectedIds = Array.from(selectedItems).map(item => item.value);

                        if (selectedIds.length === 0) return;

                        let itemsToArchive = [];
                        let itemsWithQuantity = [];

                        // Check the total quantity across all tables for each selected item
                        selectedIds.forEach(itemId => {
                            const rowsAcrossTables = document.querySelectorAll(`tr[data-item-id="${itemId}"]`);
                            let totalQuantity = 0;

                            rowsAcrossTables.forEach(row => {
                                const quantityCell = row.querySelector('td:nth-child(5)');
                                const quantity = parseInt(quantityCell ? quantityCell.textContent : '0') || 0;
                                totalQuantity += quantity;
                            });

                            if (totalQuantity > 0) {
                                itemsWithQuantity.push(itemId);
                            } else {
                                itemsToArchive.push(itemId);
                            }
                        });

                        // Case handling for mixed selection
                        if (itemsWithQuantity.length > 0 && itemsToArchive.length > 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Mixed Selection',
                                text: 'Some selected items cannot be archived as they still have quantity in stock. Only items with zero quantity will be archived.',
                                confirmButtonText: 'Proceed'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    archiveItems(itemsToArchive);
                                }
                            });
                        } else if (itemsWithQuantity.length > 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Cannot Archive',
                                text: 'All selected items have quantity in stock and cannot be archived.',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: `Are you sure?`,
                                text: `You are about to archive ${itemsToArchive.length} items.`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, archive them',
                                cancelButtonText: 'No, keep them'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    archiveItems(itemsToArchive);
                                }
                            });
                        }
                    });

                    function archiveItems(itemIds) {
                        itemIds.forEach(itemId => {
                            const rowsAcrossTables = document.querySelectorAll(`tr[data-item-id="${itemId}"]`);

                            fetch('../../backend/controllers/archive_item.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        item_id: itemId
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        rowsAcrossTables.forEach(row => row.remove()); // Remove the item from all relevant tables
                                        Swal.fire('Archived!', `Item ID ${itemId} has been archived successfully.`, 'success');
                                    } else {
                                        Swal.fire('Error!', `Failed to archive item ID ${itemId}: ${data.message}`, 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error(`Error archiving item with ID ${itemId}:`, error);
                                    Swal.fire('Error!', 'Something went wrong while archiving the item.', 'error');
                                });
                        });

                        // Hide the selection bar and reset the select all checkbox state
                        selectionBar.classList.add('hidden');
                        const selectAllCheckbox = document.querySelector('.tab-content.active input[type="checkbox"][id^="select_all"]');
                        if (selectAllCheckbox) selectAllCheckbox.checked = false;
                    }
                }

                initializeSelectAllFeature();
                initializeTabClickListener();
                initializeArchiveButton();
            });

            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('archive')) {
                    const button = event.target.closest('.archive');
                    if (!button) return;

                    console.log('Archive button clicked');

                    const row = button.closest('tr');
                    if (!row) {
                        console.error('No row found for archive action');
                        return;
                    }

                    const itemId = row.getAttribute('data-item-id');
                    if (!itemId) {
                        console.error('Item ID not found');
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This will archive the item.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, archive it!',
                        cancelButtonText: 'No, keep it'
                    }).then(result => {
                        if (result.isConfirmed) {
                            fetch('../../backend/controllers/archive_item.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        item_id: itemId
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        row.remove();
                                        Swal.fire('Archived!', data.message, 'success');
                                    } else {
                                        Swal.fire('Error!', data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error archiving item:', error);
                                    Swal.fire('Error!', 'Something went wrong while archiving the item.', 'error');
                                });
                        }
                    });
                }
            });

            function fetchOriginalData() {
                console.log("Fetching original data...");
                fetch('../../backend/controllers/get_inventory.php')
                    .then(response => response.json())
                    .then(data => {
                        console.log("Fetched data:", data);
                        if (data.success) {
                            populateInventoryTables(data.items);
                        } else {
                            console.error('Error fetching inventory data:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error during fetchOriginalData:', error);
                    });
            }

            function populateInventoryTables(items) {
                items.forEach(item => {
                    const totalQuantity = item.quantity_physical_store + item.quantity_shopee + item.quantity_tiktok;
                    const channelsText = item.channels.length === 3 ? 'All Channels' : item.channels.join(' and ');

                    const allInventoryRow = `
                <tr data-item-id="${item.product_id}">
                    <td><input type="checkbox" name="select_variant[]" value="${item.product_id}"></td>
                    <td>${item.product_id}</td>
                    <td>${item.name}</td>
                    <td>${item.category}</td>
                    <td>${totalQuantity}</td>
                    <td>${item.size}</td>
                    <td>${item.color}</td>
                    <td>${item.price}</td>
                    <td>${item.date_added}</td>
                    <td>${channelsText}</td>
                    <td><img src="../../frontend/public/images/${item.image || 'image-placeholder.png'}" alt="Image" width="50"></td>
                    <td>
                        <button class="action-button edit-inventory"><i class="fas fa-edit"></i> Edit</button>
                        <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                    </td>
                </tr>
            `;
                    document.querySelector('#all-inventory .inventory-table tbody').insertAdjacentHTML('beforeend', allInventoryRow);

                    if (item.quantity_physical_store > 0) {
                        const physicalStoreRow = `
                    <tr data-item-id="${item.product_id}">
                        <td><input type="checkbox" name="select_variant[]" value="${item.product_id}"></td>
                        <td>${item.product_id}</td>
                        <td>${item.name}</td>
                        <td>${item.category}</td>
                        <td>${item.quantity_physical_store}</td>
                        <td>${item.size}</td>
                        <td>${item.color}</td>
                        <td>${item.price}</td>
                        <td>${item.date_added}</td>
                        <td><img src="../../frontend/public/images/${item.image || 'image-placeholder.png'}" alt="Image" width="50"></td>
                        <td>
                            <button class="action-button edit-inventory"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                        </td>
                    </tr>
                `;
                        document.querySelector('#physical-store .inventory-table tbody').insertAdjacentHTML('beforeend', physicalStoreRow);
                    }

                    if (item.quantity_shopee > 0) {
                        const shopeeRow = `
                    <tr data-item-id="${item.product_id}">
                        <td><input type="checkbox" name="select_variant[]" value="${item.product_id}"></td>
                        <td>${item.product_id}</td>
                        <td>${item.name}</td>
                        <td>${item.category}</td>
                        <td>${item.quantity_shopee}</td>
                        <td>${item.size}</td>
                        <td>${item.color}</td>
                        <td>${item.price}</td>
                        <td>${item.date_added}</td>
                        <td><img src="../../frontend/public/images/${item.image || 'image-placeholder.png'}" alt="Image" width="50"></td>
                        <td>
                            <button class="action-button edit-inventory"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                        </td>
                    </tr>
                `;
                        document.querySelector('#shopee .inventory-table tbody').insertAdjacentHTML('beforeend', shopeeRow);
                    }

                    if (item.quantity_tiktok > 0) {
                        const tiktokRow = `
                    <tr data-item-id="${item.product_id}">
                        <td><input type="checkbox" name="select_variant[]" value="${item.product_id}"></td>
                        <td>${item.product_id}</td>
                        <td>${item.name}</td>
                        <td>${item.category}</td>
                        <td>${item.quantity_tiktok}</td>
                        <td>${item.size}</td>
                        <td>${item.color}</td>
                        <td>${item.price}</td>
                        <td>${item.date_added}</td>
                        <td><img src="../../frontend/public/images/${item.image || 'image-placeholder.png'}" alt="Image" width="50"></td>
                        <td>
                            <button class="action-button edit-inventory"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                        </td>
                    </tr>
                `;
                        document.querySelector('#tiktok .inventory-table tbody').insertAdjacentHTML('beforeend', tiktokRow);
                    }
                });

                attachArchiveButtonListeners();
            }

            document.querySelector('.tabs-container').addEventListener('click', function(event) {
                if (event.target.classList.contains('tab')) {
                    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    event.target.classList.add('active');
                    document.getElementById(event.target.getAttribute('data-tab')).classList.add('active');
                }
            });

            const modal = document.getElementById("new-item-modal");
            const newItemButton = document.querySelector(".new-item-button");
            const closeButton = document.querySelector(".close-button");

            newItemButton.addEventListener('click', function() {
                modal.style.display = "flex";
                resetFormFields();
                disableFormFields();
            });

            closeButton.addEventListener('click', closeModal);
            document.querySelector('.cancel-button').addEventListener('click', closeModal);

            function closeModal() {
                modal.style.display = "none";
                resetFormFields();
                disableFormFields();
            }

            document.querySelectorAll('.channel-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const quantityInput = this.closest('.channel-list').querySelector(`input[name="quantity-${this.value.toLowerCase().replace(' ', '-')}"]`);
                    if (this.checked) {
                        quantityInput.removeAttribute('disabled');
                    } else {
                        quantityInput.setAttribute('disabled', 'disabled');
                        quantityInput.value = "";
                    }
                });
            });

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
            });

            document.getElementById('reset-filters').addEventListener('click', function() {
                document.getElementById('filter-size').value = "";
                document.getElementById('filter-color').value = "";
                document.getElementById('filter-category').value = "";
                document.getElementById('filter-date').value = "";
                document.getElementById('filter-channel').value = "";

                fetchOriginalData();
            });

            document.getElementById('new-item-form').addEventListener('submit', function(event) {
                event.preventDefault();

                const productName = capitalizeWords(document.getElementById('name').value);
                const category = capitalizeWords(document.getElementById('category').value);
                const size = capitalizeWords(document.getElementById('size').value);
                const color = capitalizeWords(document.getElementById('color').value);

                fetch('../../backend/controllers/check_product_exists.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: productName
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Product exists check response:", data);

                        if (data.exists) {
                            Swal.fire({
                                title: 'Product Exists',
                                text: 'Are you adding a variant of this product?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, it\'s a variant',
                                cancelButtonText: 'No, it\'s a new product'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    populateFormWithExistingProduct(data);
                                    disableSpecificOptions(data.existing_sizes, data.existing_colors);
                                    existingProductId = data.product_id;
                                    isVariantMode = true;
                                    console.log("Confirmed variant with existingProductId:", existingProductId);
                                    submitForm(existingProductId, productName, category, size, color);
                                } else {
                                    resetFormFields();
                                    document.getElementById('name').value = "";
                                    disableFormFields();
                                }
                            });
                        } else {
                            submitForm(null, productName, category, size, color);
                        }
                    })
                    .catch(error => {
                        console.error('Error checking product:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to check if product exists.',
                            confirmButtonText: 'OK'
                        });
                    });
            });

            function submitForm(existingProductId, productName, category, size, color) {
                const formData = new FormData(document.getElementById('new-item-form'));
                const selectedChannels = Array.from(document.querySelectorAll('.channel-checkbox:checked'));

                if (selectedChannels.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select at least one channel and enter a quantity.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                let quantityProvided = true;

                selectedChannels.forEach(channel => {
                    const quantityInput = document.querySelector(`input[name="quantity-${channel.value.toLowerCase().replace(' ', '-')}"]`);
                    if (!quantityInput || quantityInput.value.trim() === "" || parseInt(quantityInput.value) <= 0) {
                        quantityProvided = false;
                    }
                });

                if (!quantityProvided) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Each selected channel must have a valid quantity greater than zero.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                selectedChannels.forEach(channel => formData.append('channels[]', channel.value));
                formData.append('name', productName);
                formData.append('category', category);
                formData.append('size', size);
                formData.append('color', color);
                if (existingProductId) formData.append('existing_product_id', existingProductId);

                fetch('../../backend/controllers/add_item.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Product added successfully!',
                                confirmButtonText: 'OK'
                            });
                            document.getElementById('new-item-form').reset();
                            modal.style.display = "none";

                            const cleanedProductName = productName.replace(/\\/g, "");

                            const variantId = data.variant_id || data.product_id;

                            const hasPhysicalStore = data.quantity_physical_store > 0;
                            const hasShopee = data.quantity_shopee > 0;
                            const hasTiktok = data.quantity_tiktok > 0;

                            let channelsText;
                            if (hasPhysicalStore && hasShopee && hasTiktok) {
                                channelsText = "All Channels";
                            } else {
                                channelsText = [
                                    hasPhysicalStore ? "Physical Store" : null,
                                    hasShopee ? "Shopee" : null,
                                    hasTiktok ? "TikTok" : null
                                ].filter(Boolean).join(", ") || "N/A";
                            }

                            const allInventoryRowTemplate = (id, name, category, quantity, size, color, price, dateAdded, image, channels) => `
                    <tr data-item-id="${id}">
                        <td><input type="checkbox" name="select_variant[]" value="${id}"></td>
                        <td>${id}</td>
                        <td>${name}</td>
                        <td>${category}</td>
                        <td>${quantity}</td>
                        <td>${size}</td>
                        <td>${color}</td>
                        <td>${price}</td>
                        <td>${dateAdded}</td>
                        <td>${channels}</td>
                        <td><img src="../../frontend/public/images/${image || 'image-placeholder.png'}" alt="Image" width="50"></td>
                        <td>
                            <button class="action-button edit-inventory"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                        </td>
                    </tr>
                `;

                            const channelSpecificRowTemplate = (id, name, category, quantity, size, color, price, dateAdded, image) => `
                    <tr data-item-id="${id}">
                        <td><input type="checkbox" name="select_variant[]" value="${id}"></td>
                        <td>${id}</td>
                        <td>${name}</td>
                        <td>${category}</td>
                        <td>${quantity}</td>
                        <td>${size}</td>
                        <td>${color}</td>
                        <td>${price}</td>
                        <td>${dateAdded}</td>
                        <td><img src="../../frontend/public/images/${image || 'image-placeholder.png'}" alt="Image" width="50"></td>
                        <td>
                            <button class="action-button edit-inventory"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button archive"><i class="fas fa-archive"></i> Archive</button>
                        </td>
                    </tr>
                `;

                            const allInventoryRow = allInventoryRowTemplate(variantId, cleanedProductName, category, data.total_quantity, size, color, data.price, data.date_added, data.image, channelsText);
                            document.querySelector('#all-inventory .inventory-table tbody').insertAdjacentHTML('beforeend', allInventoryRow);

                            const physicalStoreQuantity = data.quantity_physical_store || 0;
                            const shopeeQuantity = data.quantity_shopee || 0;
                            const tiktokQuantity = data.quantity_tiktok || 0;

                            const physicalStoreRow = channelSpecificRowTemplate(variantId, cleanedProductName, category, physicalStoreQuantity, size, color, data.price, data.date_added, data.image);
                            document.querySelector('#physical-store .inventory-table tbody').insertAdjacentHTML('beforeend', physicalStoreRow);

                            const shopeeRow = channelSpecificRowTemplate(variantId, cleanedProductName, category, shopeeQuantity, size, color, data.price, data.date_added, data.image);
                            document.querySelector('#shopee .inventory-table tbody').insertAdjacentHTML('beforeend', shopeeRow);

                            const tiktokRow = channelSpecificRowTemplate(variantId, cleanedProductName, category, tiktokQuantity, size, color, data.price, data.date_added, data.image);
                            document.querySelector('#tiktok .inventory-table tbody').insertAdjacentHTML('beforeend', tiktokRow);

                            attachArchiveButtonListeners();
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
                        console.error('Fetch error during form submission:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong! Please check the console for more details.',
                            confirmButtonText: 'OK'
                        });
                    });
            }

            function resetFormFields() {
                const nameValue = document.getElementById('name').value;
                document.getElementById('new-item-form').reset();
                document.getElementById('name').value = nameValue;
                document.getElementById('category').removeAttribute('disabled');
                enableSizeAndColorFields();
                document.getElementById('name').focus();
                isVariantMode = false;
                existingProductId = null;
            }

            function enableSizeAndColorFields() {
                document.getElementById('size').removeAttribute('disabled');
                document.getElementById('color').removeAttribute('disabled');

                const sizeOptions = document.querySelectorAll('#size option');
                const colorOptions = document.querySelectorAll('#color option');

                sizeOptions.forEach(option => option.removeAttribute('disabled'));
                colorOptions.forEach(option => option.removeAttribute('disabled'));
            }

            function disableSpecificOptions(existingSizes, existingColors) {
                enableSizeAndColorFields();

                const sizeOptions = document.querySelectorAll('#size option');
                const colorOptions = document.querySelectorAll('#color option');

                sizeOptions.forEach(option => {
                    if (existingSizes.includes(option.value)) {
                        option.setAttribute('disabled', 'disabled');
                    }
                });

                colorOptions.forEach(option => {
                    if (existingColors.includes(option.value)) {
                        option.setAttribute('disabled', 'disabled');
                    }
                });
            }

            function disableFormFields() {
                const fieldsToDisable = ['category', 'size', 'color', 'price', 'date_added', 'image'];
                fieldsToDisable.forEach(field => {
                    document.getElementById(field).setAttribute('disabled', 'disabled');
                });
            }

            function populateFormWithExistingProduct(product) {
                document.getElementById('category').value = product.category;
                document.getElementById('price').value = product.price;

                document.getElementById('category').setAttribute('disabled', 'disabled');
                document.getElementById('price').removeAttribute('disabled');
                document.getElementById('date_added').removeAttribute('disabled');
                document.getElementById('image').removeAttribute('disabled');
            }

            function handleProductNameInput() {
                const nameField = document.getElementById('name');

                nameField.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();

                        const productName = nameField.value.trim();

                        if (productName === lastCheckedProduct) return;

                        lastCheckedProduct = productName;

                        if (productName.length === 0) {
                            resetFormFields();
                            return;
                        }

                        if (isVariantMode) resetFormFields();

                        checkProductExists(productName);
                    }
                });
            }

            function checkProductExists(productName) {
                console.log("Checking if product exists:", productName);

                fetch('../../backend/controllers/check_product_exists.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: productName
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Product exists check response:", data);

                        if (data.exists) {
                            Swal.fire({
                                title: 'Product Exists',
                                text: 'Are you adding a variant of this product?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, it\'s a variant',
                                cancelButtonText: 'No, it\'s a new product'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    populateFormWithExistingProduct(data);
                                    disableSpecificOptions(data.existing_sizes, data.existing_colors);
                                    isVariantMode = true;
                                    existingProductId = data.product_id;
                                    console.log("Confirmed variant with existingProductId:", existingProductId);
                                } else {
                                    resetFormFields();
                                    document.getElementById('name').value = "";
                                    disableFormFields();
                                }
                            });
                        } else {
                            enableAllFields();
                            isVariantMode = false;
                            existingProductId = null;
                        }
                    })
                    .catch(error => console.error('Error checking product:', error));
            }

            function enableAllFields() {
                const fieldsToEnable = ['category', 'size', 'color', 'price', 'date_added', 'image'];
                fieldsToEnable.forEach(field => {
                    document.getElementById(field).removeAttribute('disabled');
                });
            }

            fetchOriginalData();
            disableFormFields();
            handleProductNameInput();
        }

        initializeInventoryManagement();
    </script>








    <!-- Selection bar and Archiving -->

    <div id="selection-bar">
        <span id="selected-count">0 items selected</span>
        <button id="archive-button">Archive</button>
    </div>



    <script>
        // Function to update the selection bar visibility and count
        function updateSelectionBar() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            const selectedIds = new Set(
                Array.from(checkboxes)
                .filter(checkbox => !checkbox.id.includes('select-all')) // Exclude header checkboxes
                .map(checkbox => checkbox.value) // Use the value (item ID) as the unique identifier
            );
            const selectedCount = selectedIds.size;

            // Update the count in the selection bar
            document.getElementById('selected-count').textContent = `${selectedCount} items selected`;

            // Show or hide the selection bar based on the count
            const selectionBar = document.getElementById('selection-bar');
            if (selectedCount > 0) {
                selectionBar.style.display = 'flex';
            } else {
                selectionBar.style.display = 'none';
            }
        }

        // Function to update the "Select All" checkbox and selection bar
        function updateSelectAll(checkboxClass, selectAllId) {
            let checkboxes = document.querySelectorAll(checkboxClass);
            let selectAllCheckbox = document.getElementById(selectAllId);

            // Check if all checkboxes are checked
            let allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            selectAllCheckbox.checked = allChecked;

            // Update selection bar
            updateSelectionBar();
        }

        // Function to reset all checkboxes
        function resetCheckboxes() {
            const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
            allCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectionBar(); // Ensure the selection bar is updated after resetting checkboxes
        }

        // Add event listener to each item checkbox to update the selection bar when clicked
        function addCheckboxListeners() {
            const allCheckboxes = [
                ...document.querySelectorAll('.select-item-all'),
                ...document.querySelectorAll('.select-item-physical'),
                ...document.querySelectorAll('.select-item-shopee'),
                ...document.querySelectorAll('.select-item-tiktok')
            ];

            allCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('click', () => {
                    updateSelectionBar();
                    updateSelectAll('.select-item-all', 'select-all-all');
                    updateSelectAll('.select-item-physical', 'select-all-physical');
                    updateSelectAll('.select-item-shopee', 'select-all-shopee');
                    updateSelectAll('.select-item-tiktok', 'select-all-tiktok');
                });
            });
        }

        // Archive button functionality
        document.getElementById('archive-button').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            const selectedIds = new Set(
                Array.from(checkboxes)
                .filter(checkbox => !checkbox.id.includes('select-all')) // Exclude header checkboxes
                .map(checkbox => checkbox.value)
            );
            let itemsToArchive = [];
            let itemsWithQuantities = [];
            let archivedItemCount = 0;

            if (selectedIds.size === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Items Selected',
                    text: 'Please select items to archive.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    resetCheckboxes(); // Reset checkboxes after alert
                });
                return;
            }

            selectedIds.forEach(itemId => {
                const itemRows = document.querySelectorAll(`tr[data-item-id="${itemId}"]`);
                if (itemRows.length > 0) {
                    // Check if the item has a quantity in any channel
                    if (hasQuantityInAnyChannel(itemId)) {
                        itemRows.forEach(row => {
                            const itemName = row.querySelector('td:nth-child(3)').innerText;
                            const itemCategory = row.querySelector('td:nth-child(4)').innerText;
                            itemsWithQuantities.push(`${itemName} (${itemCategory})`);
                        });
                    } else {
                        itemsToArchive.push(itemId);
                    }
                }
            });

            if (itemsWithQuantities.length > 0) {
                const itemListHtml = itemsWithQuantities.map(item => `<li>${item}</li>`).join('');
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Archive Some Items',
                    html: `<p>The following items have quantities and cannot be archived:</p><ul>${itemListHtml}</ul>`,
                    confirmButtonText: 'OK'
                }).then(() => {
                    resetCheckboxes(); // Reset checkboxes after alert
                });
            }

            if (itemsToArchive.length > 0) {
                let archivePromises = itemsToArchive.map(itemId => {
                    console.log(`Attempting to archive item ID: ${itemId}`); // Debugging log

                    return fetch('../../backend/controllers/archive_item.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                item_id: itemId
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Network response was not ok for item ${itemId}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log(`Response for item ${itemId}:`, data); // Debugging log

                            if (data.status === 'success') {
                                // Remove the item from all relevant tables
                                const itemRows = document.querySelectorAll(`tr[data-item-id="${itemId}"]`);
                                itemRows.forEach(row => row.remove());
                                archivedItemCount++; // Increment count only if item was successfully removed
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: `Error archiving item ${itemId}: ${data.message || 'Unknown error'}`,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    resetCheckboxes(); // Reset checkboxes after alert
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
                            }).then(() => {
                                resetCheckboxes(); // Reset checkboxes after alert
                            });
                        });
                });

                Promise.all(archivePromises).then(() => {
                    if (archivedItemCount > 0) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Archiving Completed',
                            text: `${archivedItemCount} item(s) without quantities have been successfully archived.`,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            resetCheckboxes(); // Reset checkboxes after alert
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'No Items Archived',
                            text: 'No items were archived.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            resetCheckboxes(); // Reset checkboxes after alert
                        });
                    }
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'No Items Archived',
                    text: 'No items were archived as none were eligible.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    resetCheckboxes(); // Reset checkboxes after alert
                });
            }
        });

        // Helper function to check if the selected item has a quantity in any channel
        function hasQuantityInAnyChannel(itemId) {
            const itemRow = document.querySelector(`tr[data-item-id="${itemId}"]`);
            if (!itemRow) return false;

            const quantityPhysicalStore = parseInt(itemRow.dataset.quantityPhysicalStore) || 0;
            const quantityShopee = parseInt(itemRow.dataset.quantityShopee) || 0;
            const quantityTiktok = parseInt(itemRow.dataset.quantityTiktok) || 0;

            return (quantityPhysicalStore > 0 || quantityShopee > 0 || quantityTiktok > 0);
        }

        // Event listener for 'Select All' checkbox functionality for each tab
        document.getElementById('select-all-all').addEventListener('click', function() {
            let checkboxes = document.querySelectorAll('.select-item-all');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectionBar();
        });

        document.getElementById('select-all-physical').addEventListener('click', function() {
            let checkboxes = document.querySelectorAll('.select-item-physical');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectionBar();
        });

        document.getElementById('select-all-shopee').addEventListener('click', function() {
            let checkboxes = document.querySelectorAll('.select-item-shopee');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectionBar();
        });

        document.getElementById('select-all-tiktok').addEventListener('click', function() {
            let checkboxes = document.querySelectorAll('.select-item-tiktok');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectionBar();
        });

        // Initialize 'Select All' checkboxes state and add individual checkbox listeners
        updateSelectAll('.select-item-all', 'select-all-all');
        updateSelectAll('.select-item-physical', 'select-all-physical');
        updateSelectAll('.select-item-shopee', 'select-all-shopee');
        updateSelectAll('.select-item-tiktok', 'select-all-tiktok');
        addCheckboxListeners();
    </script>

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
            /* A more vibrant and professional green */
            color: white;
            padding: 15px 25px;
            border-radius: 12px;
            /* Increased border radius for more rounded corners */
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
            /* Softer and larger shadow for more depth */
            font-size: 16px;
            /* Increased font size for better readability */
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-width: 280px;
            max-width: 450px;
            transition: all 0.3s ease-in-out;
            /* Smooth transition for display changes */
        }

        /* Text Styling */
        #selected-count {
            font-weight: 600;
            /* Slightly bolder text for emphasis */
            letter-spacing: 0.5px;
        }

        /* Button Styling */
        #archive-button {
            background-color: #3CAE85;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            /* Slightly larger border radius for a smoother button */
            cursor: pointer;
            font-size: 15px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            /* Subtle shadow for the button */
        }

        /* Hover and Active Effects for the Button */
        #archive-button:hover {
            background-color: #287458;
            transform: translateY(-2px);
            /* Slight lift effect on hover */
        }

        #archive-button:active {
            transform: translateY(1px);
            /* Button "press" effect */
        }

        /* Small screens responsiveness */
        @media (max-width: 480px) {
            #selection-bar {
                min-width: 220px;
                padding: 12px 18px;
                font-size: 14px;
                /* Adjust font size for smaller screens */
            }

            #archive-button {
                font-size: 13px;
                padding: 8px 14px;
            }
        }
    </style>




    <!-- Edit Item Modal -->
    <div id="edit-item-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <div class="header">
                <h1>Edit Inventory Item</h1>
            </div>

            <form id="edit-item-form">
                <!-- Hidden input for the variant ID -->
                <input type="hidden" id="edit-variant-id" name="variant_id">

                <!-- Name Field -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit-name">Name:</label>
                        <input type="text" id="edit-name" name="name" required minlength="2">
                    </div>
                </div>

                <!-- Category Field -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit-category">Category:</label>
                        <select id="edit-category" name="category" required>
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

                <!-- Size and Color Fields -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit-size">Size:</label>
                        <select id="edit-size" name="size" required>
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
                        <label for="edit-color">Color:</label>
                        <select id="edit-color" name="color" required>
                            <option value="">Select Color</option>
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
                </div>

                <!-- Channel and Quantity Fields -->
                <div class="form-row">
                    <div class="form-group channel-group">
                        <label>Channels:</label>
                        <div class="channel-list">
                            <label>
                                <input type="checkbox" name="channel[]" value="Physical Store" class="channel-checkbox">
                                Physical Store
                            </label>
                            <input type="number" name="quantity-physical-store" placeholder="Qty" min="1" class="quantity-input">

                            <label>
                                <input type="checkbox" name="channel[]" value="Shopee" class="channel-checkbox">
                                Shopee
                            </label>
                            <input type="number" name="quantity-shopee" placeholder="Qty" min="1" class="quantity-input">

                            <label>
                                <input type="checkbox" name="channel[]" value="TikTok" class="channel-checkbox">
                                TikTok
                            </label>
                            <input type="number" name="quantity-tiktok" placeholder="Qty" min="1" class="quantity-input">
                        </div>
                    </div>
                </div>

                <!-- Price and Date Added Fields -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit-price">Price:</label>
                        <input type="number" id="edit-price" name="price" required min="1">
                    </div>

                    <div class="form-group">
                        <label for="edit-date-added">Date Added:</label>
                        <input type="date" id="edit-date-added" name="date_added" required>
                    </div>
                </div>

                <!-- Image Upload Field and Preview -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit-image">Image:</label>
                        <input type="file" id="edit-image" name="image" accept="image/png, image/jpeg, image/jpg">
                        <div id="image-preview-container">
                            <img id="edit-image-preview" src="#" alt="Image Preview" style="display:none;">
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="form-row buttons-row">
                    <button type="button" id="edit-modal-cancel-button" class="cancel-button">Cancel</button>
                    <button type="submit" id="edit-modal-save-button" class="save-item-button">Save Changes</button>
                </div>
            </form>
        </div>
    </div>


    <!-- edit modal JS -->
    <script>
        // Function to handle opening and populating the Edit Modal
        function openEditModal(row) {
            const editModal = document.getElementById('edit-item-modal');

            // Populate modal fields with row data attributes
            editModal.querySelector('#edit-variant-id').value = row.getAttribute('data-item-id'); // Variant ID
            editModal.querySelector('#edit-name').value = row.querySelector('td:nth-child(3)').innerText.trim(); // Name
            editModal.querySelector('#edit-category').value = row.querySelector('td:nth-child(4)').innerText.trim(); // Category
            editModal.querySelector('#edit-size').value = row.querySelector('td:nth-child(6)').innerText.trim(); // Size
            editModal.querySelector('#edit-color').value = row.querySelector('td:nth-child(7)').innerText.trim(); // Color
            editModal.querySelector('#edit-price').value = row.querySelector('td:nth-child(8)').innerText.trim(); // Price
            editModal.querySelector('#edit-date-added').value = row.querySelector('td:nth-child(9)').innerText.trim(); // Date Added

            // Disable the category, size, and color fields
            editModal.querySelector('#edit-category').disabled = true;
            editModal.querySelector('#edit-size').disabled = true;
            editModal.querySelector('#edit-color').disabled = true;

            // Set the image preview
            const currentImage = row.querySelector('td:nth-child(11) img') || row.querySelector('td:nth-child(10) img'); // Adjust based on table
            if (currentImage) {
                const preview = document.getElementById('edit-image-preview');
                preview.src = currentImage.src;
                preview.style.display = 'block'; // Show image preview
            } else {
                // Hide the preview if no image exists
                document.getElementById('edit-image-preview').style.display = 'none';
            }

            // Fetch inventory quantities for this variant from get_inventory.php
            const variantId = row.getAttribute('data-item-id');
            fetch(`../../backend/controllers/get_inventory.php?variant_id=${variantId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const item = data.items[0]; // Assuming one item per variant_id

                        // Set channel-specific quantities from fetched data
                        const physicalStoreQuantity = item.quantity_physical_store || 0;
                        const shopeeQuantity = item.quantity_shopee || 0;
                        const tiktokQuantity = item.quantity_tiktok || 0;

                        editModal.querySelector('input[name="quantity-physical-store"]').value = physicalStoreQuantity;
                        editModal.querySelector('input[name="quantity-shopee"]').value = shopeeQuantity;
                        editModal.querySelector('input[name="quantity-tiktok"]').value = tiktokQuantity;

                        // Set checkboxes based on quantities (check if quantity is greater than 0)
                        const physicalStoreCheckbox = editModal.querySelector('input[name="channel[]"][value="Physical Store"]');
                        const shopeeCheckbox = editModal.querySelector('input[name="channel[]"][value="Shopee"]');
                        const tiktokCheckbox = editModal.querySelector('input[name="channel[]"][value="TikTok"]');

                        physicalStoreCheckbox.checked = physicalStoreQuantity > 0;
                        shopeeCheckbox.checked = shopeeQuantity > 0;
                        tiktokCheckbox.checked = tiktokQuantity > 0;

                        // Lock the quantity fields if quantity is 0, and require the checkbox to enable them
                        lockQuantityField(physicalStoreQuantity, physicalStoreCheckbox, 'quantity-physical-store');
                        lockQuantityField(shopeeQuantity, shopeeCheckbox, 'quantity-shopee');
                        lockQuantityField(tiktokQuantity, tiktokCheckbox, 'quantity-tiktok');

                        // Add event listeners to checkboxes to unlock quantity fields if checked
                        physicalStoreCheckbox.addEventListener('change', () => lockQuantityField(physicalStoreQuantity, physicalStoreCheckbox, 'quantity-physical-store'));
                        shopeeCheckbox.addEventListener('change', () => lockQuantityField(shopeeQuantity, shopeeCheckbox, 'quantity-shopee'));
                        tiktokCheckbox.addEventListener('change', () => lockQuantityField(tiktokQuantity, tiktokCheckbox, 'quantity-tiktok'));

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to load inventory data',
                            text: data.message || 'An error occurred while fetching inventory data.',
                        });
                    }
                })
                .catch((error) => {
                    console.error('Error fetching inventory data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'An error occurred while fetching the inventory data.',
                    });
                });

            // Display the modal
            editModal.style.display = 'flex';
        }

        // Function to lock/unlock quantity fields based on checkbox state
        function lockQuantityField(quantity, checkbox, quantityFieldName) {
            const quantityField = document.querySelector(`#edit-item-modal input[name="${quantityFieldName}"]`);
            if (quantity === 0) {
                // Lock field if quantity is 0
                quantityField.disabled = true;
            } else {
                // Enable field only if checkbox is checked
                quantityField.disabled = !checkbox.checked;
            }
        }

        // Event listener using delegation for dynamic content (Edit Button)
        document.addEventListener('click', function(event) {
            if (event.target.closest('.action-button.edit-inventory')) {
                const row = event.target.closest('tr');
                if (row) {
                    openEditModal(row);
                }
            }
        });

        // Close modal functionality
        // Close modal functionality with immediate refresh of the selection bar
        document.querySelector('#edit-item-modal .close-button').addEventListener('click', () => {
            // Ensure all checkbox states are correctly refreshed
            resetCheckboxStates(); // Custom function to reset checkbox states if needed
            updateSelectionBar(); // Refresh the selection count before closing modal
            document.getElementById('edit-item-modal').style.display = 'none';
        });

        document.getElementById('edit-modal-cancel-button').addEventListener('click', () => {
            // Ensure all checkbox states are correctly refreshed
            resetCheckboxStates(); // Custom function to reset checkbox states if needed
            updateSelectionBar(); // Refresh the selection count before closing modal
            document.getElementById('edit-item-modal').style.display = 'none';
        });
        // Function to reset checkbox states
        function resetCheckboxStates() {
            const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
            allCheckboxes.forEach(checkbox => {
                checkbox.checked = false; // Uncheck all checkboxes
            });

            // Optionally, you can add logic to reset specific checkboxes depending on your needs
            // e.g., uncheck specific checkboxes that were changed during modal interaction
        }


        // Handle Image Selection and Preview in Modal
        document.getElementById('edit-image').addEventListener('change', function(event) {
            const preview = document.getElementById('edit-image-preview');
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                // If no file is selected, retain the existing image or hide the preview
                const existingImageSrc = document.querySelector(`tr[data-item-id="${document.getElementById('edit-variant-id').value}"] td:nth-child(11) img`) ||
                    document.querySelector(`tr[data-item-id="${document.getElementById('edit-variant-id').value}"] td:nth-child(10) img`)?.src || '';
                if (existingImageSrc) {
                    preview.src = existingImageSrc;
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
            }
        });

        // Save changes and send data to the backend on clicking "Save Changes"
        document.getElementById('edit-modal-save-button').addEventListener('click', (event) => {
            event.preventDefault(); // Prevent default form submission

            const editModal = document.getElementById('edit-item-modal');
            const formData = new FormData();

            // Collect form data
            formData.append('variant_id', editModal.querySelector('#edit-variant-id').value);
            formData.append('name', editModal.querySelector('#edit-name').value);
            formData.append('category', editModal.querySelector('#edit-category').value);
            formData.append('size', editModal.querySelector('#edit-size').value);
            formData.append('color', editModal.querySelector('#edit-color').value);
            formData.append('price', editModal.querySelector('#edit-price').value);
            formData.append('date_added', editModal.querySelector('#edit-date-added').value);

            // Channel quantities
            formData.append('quantity_physical_store', editModal.querySelector('input[name="quantity-physical-store"]').value);
            formData.append('quantity_shopee', editModal.querySelector('input[name="quantity-shopee"]').value);
            formData.append('quantity_tiktok', editModal.querySelector('input[name="quantity-tiktok"]').value);

            // Add image file if provided
            const imageFile = document.getElementById('edit-image').files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }

            // Validation checks
            const name = editModal.querySelector('#edit-name').value.trim();
            const price = parseFloat(editModal.querySelector('#edit-price').value.trim());
            const quantityPhysicalStore = parseInt(editModal.querySelector('input[name="quantity-physical-store"]').value) || 0;
            const quantityShopee = parseInt(editModal.querySelector('input[name="quantity-shopee"]').value) || 0;
            const quantityTiktok = parseInt(editModal.querySelector('input[name="quantity-tiktok"]').value) || 0;

            // Validate name
            if (!name) {
                Swal.fire({
                    icon: 'error',
                    title: 'Name Required',
                    text: 'Please enter a name for the item.',
                });
                return;
            }

            // Validate price
            if (isNaN(price) || price <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Price',
                    text: 'Please enter a valid price greater than 0.',
                });
                return;
            }

            // Validate quantities
            if (quantityPhysicalStore < 0 || quantityShopee < 0 || quantityTiktok < 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Quantity',
                    text: 'Quantities must be greater than or equal to 0.',
                });
                return;
            }

            // Check if the name already exists in the database
            fetch(`../../backend/controllers/check_item_name.php?name=${encodeURIComponent(name)}&variant_id=${editModal.querySelector('#edit-variant-id').value}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate Name',
                            text: 'An item with this name already exists. Please choose a different name.',
                        });
                        return;
                    }

                    // Check if the data has actually changed
                    const originalRow = document.querySelector(`tr[data-item-id="${editModal.querySelector('#edit-variant-id').value}"]`);
                    const originalName = originalRow.querySelector('td:nth-child(3)').innerText.trim();
                    const originalCategory = originalRow.querySelector('td:nth-child(4)').innerText.trim();
                    const originalSize = originalRow.querySelector('td:nth-child(6)').innerText.trim();
                    const originalColor = originalRow.querySelector('td:nth-child(7)').innerText.trim();
                    const originalPrice = parseFloat(originalRow.querySelector('td:nth-child(8)').innerText.trim());
                    const originalDate = originalRow.querySelector('td:nth-child(9)').innerText.trim();
                    const originalPhysicalQuantity = parseInt(originalRow.querySelector('td:nth-child(5)').innerText.trim()) || 0;

                    // If no changes detected, show a message and prevent update
                    if (originalName === name &&
                        originalCategory === editModal.querySelector('#edit-category').value &&
                        originalSize === editModal.querySelector('#edit-size').value &&
                        originalColor === editModal.querySelector('#edit-color').value &&
                        originalPrice === price &&
                        originalDate === editModal.querySelector('#edit-date-added').value &&
                        originalPhysicalQuantity === (quantityPhysicalStore + quantityShopee + quantityTiktok)) {
                        Swal.fire({
                            icon: 'info',
                            title: 'No Changes Detected',
                            text: 'No changes were made to the item. The update was canceled.',
                        });
                        return; // Stop execution if no changes were made
                    }

                    // If all validations pass, send the data to the server
                    fetch('../../backend/controllers/update_item.php', {
                            method: 'POST',
                            body: formData,
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Item Updated!',
                                    text: 'The inventory item was updated successfully.',
                                    showConfirmButton: false,
                                    timer: 1500,
                                });

                                // Update the specific row fields in all tabs dynamically
                                const itemId = editModal.querySelector('#edit-variant-id').value;
                                const updatedName = editModal.querySelector('#edit-name').value;
                                const updatedCategory = editModal.querySelector('#edit-category').value;
                                const updatedSize = editModal.querySelector('#edit-size').value;
                                const updatedColor = editModal.querySelector('#edit-color').value;
                                const updatedPrice = editModal.querySelector('#edit-price').value;
                                const updatedDate = editModal.querySelector('#edit-date-added').value;

                                // Channel quantities
                                const physicalQuantity = parseInt(editModal.querySelector('input[name="quantity-physical-store"]').value) || 0;
                                const shopeeQuantity = parseInt(editModal.querySelector('input[name="quantity-shopee"]').value) || 0;
                                const tiktokQuantity = parseInt(editModal.querySelector('input[name="quantity-tiktok"]').value) || 0;
                                const totalQuantity = physicalQuantity + shopeeQuantity + tiktokQuantity;

                                // Determine active channels
                                const activeChannels = [];
                                if (physicalQuantity > 0) activeChannels.push("Physical Store");
                                if (shopeeQuantity > 0) activeChannels.push("Shopee");
                                if (tiktokQuantity > 0) activeChannels.push("TikTok");
                                const updatedChannels = activeChannels.length === 3 ? 'All Channels' : activeChannels.join(' and ');

                                // Update rows in all tabs
                                document.querySelectorAll(`tr[data-item-id="${itemId}"]`).forEach((row) => {
                                    const table = row.closest('table');

                                    if (table.classList.contains('inventory-table-all')) {
                                        // All Inventory Table
                                        row.querySelector('td:nth-child(3)').innerText = updatedName; // Name
                                        row.querySelector('td:nth-child(4)').innerText = updatedCategory; // Category
                                        row.querySelector('td:nth-child(6)').innerText = updatedSize; // Size
                                        row.querySelector('td:nth-child(7)').innerText = updatedColor; // Color
                                        row.querySelector('td:nth-child(8)').innerText = updatedPrice; // Price
                                        row.querySelector('td:nth-child(9)').innerText = updatedDate; // Date Added
                                        row.querySelector('td:nth-child(5)').innerText = totalQuantity; // Total Quantity
                                        row.querySelector('td:nth-child(10)').innerText = updatedChannels; // Channels

                                        // Update image in 11th column
                                        const imagePreview = row.querySelector('td:nth-child(11) img');
                                        if (imagePreview) {
                                            if (data.image_url) {
                                                // Append a timestamp to prevent caching
                                                imagePreview.src = `${data.image_url}?t=${new Date().getTime()}`;
                                            } else {
                                                // If no new image was uploaded, keep the existing src from the preview
                                                imagePreview.src = document.getElementById('edit-image-preview').src;
                                            }
                                        }
                                    } else if (table.classList.contains('inventory-table-physical')) {
                                        // Physical Store Table
                                        row.querySelector('td:nth-child(3)').innerText = updatedName; // Name
                                        row.querySelector('td:nth-child(4)').innerText = updatedCategory; // Category
                                        row.querySelector('td:nth-child(6)').innerText = updatedSize; // Size
                                        row.querySelector('td:nth-child(7)').innerText = updatedColor; // Color
                                        row.querySelector('td:nth-child(8)').innerText = updatedPrice; // Price
                                        row.querySelector('td:nth-child(9)').innerText = updatedDate; // Date Added
                                        row.querySelector('td:nth-child(5)').innerText = physicalQuantity; // Quantity specific to Physical Store

                                        // Update image in 10th column
                                        const imagePreview = row.querySelector('td:nth-child(10) img');
                                        if (imagePreview) {
                                            if (data.image_url) {
                                                imagePreview.src = `${data.image_url}?t=${new Date().getTime()}`;
                                            } else {
                                                imagePreview.src = document.getElementById('edit-image-preview').src;
                                            }
                                        }
                                    } else if (table.classList.contains('inventory-table-shopee')) {
                                        // Shopee Table
                                        row.querySelector('td:nth-child(3)').innerText = updatedName; // Name
                                        row.querySelector('td:nth-child(4)').innerText = updatedCategory; // Category
                                        row.querySelector('td:nth-child(6)').innerText = updatedSize; // Size
                                        row.querySelector('td:nth-child(7)').innerText = updatedColor; // Color
                                        row.querySelector('td:nth-child(8)').innerText = updatedPrice; // Price
                                        row.querySelector('td:nth-child(9)').innerText = updatedDate; // Date Added
                                        row.querySelector('td:nth-child(5)').innerText = shopeeQuantity; // Quantity specific to Shopee

                                        // Update image in 10th column
                                        const imagePreview = row.querySelector('td:nth-child(10) img');
                                        if (imagePreview) {
                                            if (data.image_url) {
                                                imagePreview.src = `${data.image_url}?t=${new Date().getTime()}`;
                                            } else {
                                                imagePreview.src = document.getElementById('edit-image-preview').src;
                                            }
                                        }
                                    } else if (table.classList.contains('inventory-table-tiktok')) {
                                        // TikTok Table
                                        row.querySelector('td:nth-child(3)').innerText = updatedName; // Name
                                        row.querySelector('td:nth-child(4)').innerText = updatedCategory; // Category
                                        row.querySelector('td:nth-child(6)').innerText = updatedSize; // Size
                                        row.querySelector('td:nth-child(7)').innerText = updatedColor; // Color
                                        row.querySelector('td:nth-child(8)').innerText = updatedPrice; // Price
                                        row.querySelector('td:nth-child(9)').innerText = updatedDate; // Date Added
                                        row.querySelector('td:nth-child(5)').innerText = tiktokQuantity; // Quantity specific to TikTok

                                        // Update image in 10th column
                                        const imagePreview = row.querySelector('td:nth-child(10) img');
                                        if (imagePreview) {
                                            if (data.image_url) {
                                                imagePreview.src = `${data.image_url}?t=${new Date().getTime()}`;
                                            } else {
                                                imagePreview.src = document.getElementById('edit-image-preview').src;
                                            }
                                        }
                                    }
                                });

                                // Close the modal
                                editModal.style.display = 'none';
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Update Failed',
                                    text: data.message || 'An error occurred while updating the item.',
                                });
                            }
                        })
                        .catch((error) => {
                            console.error('Error updating item:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Network Error',
                                text: 'An error occurred while updating the item.',
                            });
                        });
                })
                .catch(error => {
                    console.error('Error checking item name:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'An error occurred while checking the item name.',
                    });
                });
        });
    </script>















    <style>
        #image-preview-container {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 5px;
            background-color: #f9f9f9;
            max-width: 80px;
            /* Adjusted max width to make the image smaller */
            max-height: 80px;
        }

        #edit-image-preview {
            display: block;
            width: 100%;
            /* Ensure the image fits within the container */
            height: auto;
            border-radius: 5px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            object-fit: contain;
            /* Keep the image aspect ratio */
        }
    </style>




















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

    .inventory-container,
    .new-item-container {
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

    /* Set a fixed height and make the table content scrollable for each tab */
    /* Add a fixed height to the table container for each tab */
    .tab-content {
        display: none;
        padding-top: 20px;
        overflow-y: auto;
        /* Enable vertical scrolling for the content */
        height: calc(100vh - 280px);
        /* Adjust height as needed */
    }

    /* Make the table header sticky */
    .inventory-table thead th {
        position: sticky;
        top: 0;
        background-color: #f4f7fc;
        /* Match background color */
        z-index: 1;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    }


    .tab-content.active {
        display: block;
    }

    .inventory-table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 10px;
        overflow-y: auto;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        max-height: 100%;
        /* Ensure table fits within tab content */
        table-layout: fixed;
        /* Keeps column widths fixed */
    }

    .inventory-table th,
    .inventory-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #eee;
        overflow: hidden;
        font-size: 12px;
    }

    .inventory-table td.truncate-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }


    /* Checkbox Styling */
    input[type="checkbox"] {
        appearance: none;
        width: 16px;
        height: 16px;
        background-color: #ffffff;
        border: 1px solid #007bff;
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

    /* Column width adjustments for All Inventory tab */
    .inventory-table-all th:nth-child(1),
    .inventory-table-all td:nth-child(1) {
        width: 50px;
    }

    .inventory-table-all th:nth-child(2),
    .inventory-table-all td:nth-child(2) {
        width: 100px;
    }

    .inventory-table-all th:nth-child(3),
    .inventory-table-all td:nth-child(3) {
        width: 120px;
    }

    .inventory-table-all th:nth-child(4),
    .inventory-table-all td:nth-child(4) {
        width: 90px;
    }

    .inventory-table-all th:nth-child(5),
    .inventory-table-all td:nth-child(5) {
        width: 80px;
    }

    .inventory-table-all th:nth-child(6),
    .inventory-table-all td:nth-child(6) {
        width: 100px;
    }

    .inventory-table-all th:nth-child(7),
    .inventory-table-all td:nth-child(7) {
        width: 80px;
    }

    .inventory-table-all th:nth-child(8),
    .inventory-table-all td:nth-child(8) {
        width: 90px;
    }

    .inventory-table-all th:nth-child(9),
    .inventory-table-all td:nth-child(9) {
        width: 100px;
    }

    .inventory-table-all th:nth-child(10),
    .inventory-table-all td:nth-child(10) {
        width: 90px;
    }

    .inventory-table-all th:nth-child(11),
    .inventory-table-all td:nth-child(11) {
        width: 90px;
    }

    .inventory-table-all th:nth-child(12),
    .inventory-table-all td:nth-child(12) {
        width: 150px;
        white-space: nowrap;
    }

    /* Column width adjustments for Physical Store tab */
    .inventory-table-physical th:nth-child(1),
    .inventory-table-physical td:nth-child(1) {
        width: 40px;
    }

    .inventory-table-physical th:nth-child(2),
    .inventory-table-physical td:nth-child(2) {
        width: 80px;
    }

    .inventory-table-physical th:nth-child(3),
    .inventory-table-physical td:nth-child(3) {
        width: 90px;
    }

    .inventory-table-physical th:nth-child(4),
    .inventory-table-physical td:nth-child(4) {
        width: 90px;
    }

    .inventory-table-physical th:nth-child(5),
    .inventory-table-physical td:nth-child(5) {
        width: 50px;
    }

    .inventory-table-physical th:nth-child(6),
    .inventory-table-physical td:nth-child(6) {
        width: 60px;
    }

    .inventory-table-physical th:nth-child(7),
    .inventory-table-physical td:nth-child(7) {
        width: 70px;
    }

    .inventory-table-physical th:nth-child(8),
    .inventory-table-physical td:nth-child(8) {
        width: 80px;
    }

    .inventory-table-physical th:nth-child(9),
    .inventory-table-physical td:nth-child(9) {
        width: 90px;
    }

    .inventory-table-physical th:nth-child(10),
    .inventory-table-physical td:nth-child(10) {
        width: 80px;
    }

    .inventory-table-physical th:nth-child(11),
    .inventory-table-physical td:nth-child(11) {
        width: 150px;
    }

    .inventory-table-physical th:nth-child(12),
    .inventory-table-physical td:nth-child(12) {
        width: 120px;
        white-space: nowrap;
    }

    /* Column width adjustments for Shopee tab */
    .inventory-table-shopee th:nth-child(1),
    .inventory-table-shopee td:nth-child(1) {
        width: 40px;
    }

    .inventory-table-shopee th:nth-child(2),
    .inventory-table-shopee td:nth-child(2) {
        width: 80px;
    }

    .inventory-table-shopee th:nth-child(3),
    .inventory-table-shopee td:nth-child(3) {
        width: 90px;
    }

    .inventory-table-shopee th:nth-child(4),
    .inventory-table-shopee td:nth-child(4) {
        width: 90px;
    }

    .inventory-table-shopee th:nth-child(5),
    .inventory-table-shopee td:nth-child(5) {
        width: 50px;
    }

    .inventory-table-shopee th:nth-child(6),
    .inventory-table-shopee td:nth-child(6) {
        width: 60px;
    }

    .inventory-table-shopee th:nth-child(7),
    .inventory-table-shopee td:nth-child(7) {
        width: 70px;
    }

    .inventory-table-shopee th:nth-child(8),
    .inventory-table-shopee td:nth-child(8) {
        width: 80px;
    }

    .inventory-table-shopee th:nth-child(9),
    .inventory-table-shopee td:nth-child(9) {
        width: 90px;
    }

    .inventory-table-shopee th:nth-child(10),
    .inventory-table-shopee td:nth-child(10) {
        width: 80px;
    }

    .inventory-table-shopee th:nth-child(11),
    .inventory-table-shopee td:nth-child(11) {
        width: 150px;
    }

    .inventory-table-shopee th:nth-child(12),
    .inventory-table-shopee td:nth-child(12) {
        width: 120px;
        white-space: nowrap;
    }

    /* Column width adjustments for TikTok tab */
    .inventory-table-tiktok th:nth-child(1),
    .inventory-table-tiktok td:nth-child(1) {
        width: 40px;
    }

    .inventory-table-tiktok th:nth-child(2),
    .inventory-table-tiktok td:nth-child(2) {
        width: 80px;
    }

    .inventory-table-tiktok th:nth-child(3),
    .inventory-table-tiktok td:nth-child(3) {
        width: 90px;
    }

    .inventory-table-tiktok th:nth-child(4),
    .inventory-table-tiktok td:nth-child(4) {
        width: 90px;
    }

    .inventory-table-tiktok th:nth-child(5),
    .inventory-table-tiktok td:nth-child(5) {
        width: 50px;
    }

    .inventory-table-tiktok th:nth-child(6),
    .inventory-table-tiktok td:nth-child(6) {
        width: 60px;
    }

    .inventory-table-tiktok th:nth-child(7),
    .inventory-table-tiktok td:nth-child(7) {
        width: 70px;
    }

    .inventory-table-tiktok th:nth-child(8),
    .inventory-table-tiktok td:nth-child(8) {
        width: 80px;
    }

    .inventory-table-tiktok th:nth-child(9),
    .inventory-table-tiktok td:nth-child(9) {
        width: 90px;
    }

    .inventory-table-tiktok th:nth-child(10),
    .inventory-table-tiktok td:nth-child(10) {
        width: 80px;
    }

    .inventory-table-tiktok th:nth-child(11),
    .inventory-table-tiktok td:nth-child(11) {
        width: 150px;
    }

    .inventory-table-tiktok th:nth-child(12),
    .inventory-table-tiktok td:nth-child(12) {
        width: 120px;
        white-space: nowrap;
    }


    /* Ensure buttons do not get truncated */
    .inventory-table td .action-button {
        white-space: normal;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .inventory-table th {
        background-color: #f4f7fc;
        color: #555;
        font-size: 12px;
        font-weight: 600;
    }

    .inventory-table td {
        color: #555;
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

    .action-button.edit-inventory {
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
        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05);
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
        font-size: 18px;
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
        font-size: 12px;
    }

    .form-group input,
    .form-group select {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 12px;
        background-color: #f9f9f9;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus {
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

    .cancel-button,
    .save-item-button {
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

    .save-item-button {
        background-color: #007bff;
        color: white;
        width: 200px;
    }

    .save-item-button:hover {
        background-color: #0056b3;
    }

    /* Modal styles */
    .modal {
        display: none;
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
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
        width: 40%;
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
        margin-bottom: 10px;
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

    /* Dropdown styling for filters */
    .filter-dropdown {
        background-color: white;
        border: 1px solid #ccc;
        position: absolute;
        top: 40px;
        right: 0;
        padding: 8px;
        width: 220px;
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
        margin-bottom: 10px;
    }

    .filter-section label {
        font-size: 11px;
        font-weight: 600;
        color: #004085;
        margin-bottom: 5px;
    }

    .filter-section select,
    .filter-section input {
        width: 100%;
        padding: 5px;
        font-size: 11px;
        border: 1px solid #ccc;
        border-radius: 3px;
        background-color: #f8f9fa;
    }

    .filter-section select:focus,
    .filter-section input:focus {
        border-color: #0056b3;
        outline: none;
    }

    #apply-filters,
    #reset-filters {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 6px;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        margin-top: 5px;
        font-size: 12px;
        transition: background-color 0.3s ease;
    }

    #apply-filters:hover,
    #reset-filters:hover {
        background-color: #004085;
    }


    .selection-bar {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 300px;
        background-color: #007bff;
        /* Main accent color */
        color: white;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-radius: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        z-index: 1000;
    }

    .selection-bar.hidden {
        display: none;
    }

    .selection-bar .selection-action {
        background-color: white;
        color: #007bff;
        /* Accent color for action text */
        border: none;
        padding: 5px 10px;
        border-radius: 50px;
        /* Makes the button rounded */
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .selection-bar .selection-action:hover {
        background-color: #0056b3;
        /* Hover color */
        color: white;
    }
</style>


























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










                <!-- nilagay ko filter sa inventory -->