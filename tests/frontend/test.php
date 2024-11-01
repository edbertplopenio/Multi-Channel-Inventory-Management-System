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

// Fetch aggregated data for All Inventory tab
$sql_all_inventory = "
    SELECT 
        pv.variant_id, 
        p.name, 
        p.category, 
        pv.size, 
        pv.color, 
        pv.price, 
        pv.date_added, 
        pv.image,
        SUM(CASE WHEN i.channel = 'physical_store' THEN i.quantity ELSE 0 END) AS quantity_physical_store,
        SUM(CASE WHEN i.channel = 'shopee' THEN i.quantity ELSE 0 END) AS quantity_shopee,
        SUM(CASE WHEN i.channel = 'tiktok' THEN i.quantity ELSE 0 END) AS quantity_tiktok,
        GROUP_CONCAT(DISTINCT i.channel ORDER BY i.channel SEPARATOR ', ') as channels
    FROM product_variants pv
    JOIN products p ON pv.product_id = p.product_id
    JOIN inventory i ON pv.variant_id = i.variant_id
    GROUP BY pv.variant_id, pv.size, pv.color, pv.price, pv.date_added, pv.image";
$result_all_inventory = mysqli_query($conn, $sql_all_inventory);

if (!$result_all_inventory) {
    die("Error executing query for All Inventory: " . mysqli_error($conn));
}

// Fetch data for Physical Store inventory
$sql_physical_store = "SELECT pv.variant_id, p.product_id, p.name, p.category, pv.size, pv.color, pv.price, pv.date_added, pv.image, i.channel, i.quantity
                       FROM product_variants pv
                       JOIN products p ON pv.product_id = p.product_id
                       JOIN inventory i ON pv.variant_id = i.variant_id
                       WHERE i.channel = 'physical_store'";
$result_physical_store = mysqli_query($conn, $sql_physical_store);

if (!$result_physical_store) {
    die("Error executing query for Physical Store: " . mysqli_error($conn));
}

// Fetch data for Shopee inventory
$sql_shopee = "SELECT pv.variant_id, p.product_id, p.name, p.category, pv.size, pv.color, pv.price, pv.date_added, pv.image, i.channel, i.quantity
               FROM product_variants pv
               JOIN products p ON pv.product_id = p.product_id
               JOIN inventory i ON pv.variant_id = i.variant_id
               WHERE i.channel = 'shopee'";
$result_shopee = mysqli_query($conn, $sql_shopee);

if (!$result_shopee) {
    die("Error executing query for Shopee: " . mysqli_error($conn));
}

// Fetch data for TikTok inventory
$sql_tiktok = "SELECT pv.variant_id, p.product_id, p.name, p.category, pv.size, pv.color, pv.price, pv.date_added, pv.image, i.channel, i.quantity
               FROM product_variants pv
               JOIN products p ON pv.product_id = p.product_id
               JOIN inventory i ON pv.variant_id = i.variant_id
               WHERE i.channel = 'tiktok'";
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

<!-- All Inventory Tab -->
<div id="all-inventory" class="tab-content active">
    <table class="inventory-table inventory-table-all">
                <thead>
                    <tr>
                        <th></th>
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
                            <tr>
                                <td><input type="checkbox" name="select_variant[]" value="<?php echo $row['variant_id']; ?>"></td>
                                <td><?php echo $row['variant_id']; ?></td>
                                <td class="wrap-text"><?php echo $row['name']; ?></td> <!-- Enable wrapping for long names -->
                                <td class="wrap-text"><?php echo $row['category']; ?></td> <!-- Enable wrapping for long categories -->
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
                                    <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                                    <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
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

        <!-- Repeat this structure for Physical Store, Shopee, and TikTok tabs with checkboxes beside the Variant ID column -->

<!-- Physical Store Tab -->
<div id="physical-store" class="tab-content">
    <table class="inventory-table inventory-table-physical">
                <thead>
                    <tr>
                        <th></th>
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
                            <tr>
                                <td><input type="checkbox" name="select_variant[]" value="<?php echo $row['variant_id']; ?>"></td>
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
                                    <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                                    <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
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
<div id="shopee" class="tab-content">
    <table class="inventory-table inventory-table-shopee">
                <thead>
                    <tr>
                        <th></th>
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
                            <tr>
                                <td><input type="checkbox" name="select_variant[]" value="<?php echo $row['variant_id']; ?>"></td>
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
                                    <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                                    <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
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
<div id="tiktok" class="tab-content">
    <table class="inventory-table inventory-table-tiktok">
                <thead>
                    <tr>
                        <th></th>
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
                            <tr>
                                <td><input type="checkbox" name="select_variant[]" value="<?php echo $row['variant_id']; ?>"></td>
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
                                    <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                                    <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
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
</body>

</html>





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



<script>
    function initializeInventoryManagement() {
        let originalData = [];
        let lastCheckedProduct = null; // Track the last checked product name
        let isVariantMode = false; // Track if form is in variant mode
        let existingProductId = null; // Track existing product ID for variant submissions

        function capitalizeWords(str) {
            return str.replace(/\b\w/g, char => char.toUpperCase());
        }

        function fetchOriginalData() {
            console.log("Fetching original data..."); // Debugging
            fetch('../../backend/controllers/get_inventory.php')
                .then(response => response.json())
                .then(data => {
                    console.log("Fetched data:", data); // Debugging
                    if (data.success) {
                        populateInventoryTables(data.items);
                    } else {
                        console.error('Error fetching inventory data:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error during fetchOriginalData:', error); // Debugging
                });
        }

        function populateInventoryTables(items) {
            items.forEach(item => {
                const totalQuantity = item.quantity_physical_store + item.quantity_shopee + item.quantity_tiktok;
                const channelsText = item.channels.length === 3 ? 'All Channels' : item.channels.join(' and ');

                const allInventoryRow = `
                <tr data-product-id="${item.product_id}">
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
                        <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                        <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
                    </td>
                </tr>
            `;
                document.querySelector('#all-inventory .inventory-table tbody').insertAdjacentHTML('beforeend', allInventoryRow);

                if (item.quantity_physical_store > 0) {
                    const physicalStoreRow = `
                    <tr data-product-id="${item.product_id}">
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
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                `;
                    document.querySelector('#physical-store .inventory-table tbody').insertAdjacentHTML('beforeend', physicalStoreRow);
                }

                if (item.quantity_shopee > 0) {
                    const shopeeRow = `
                    <tr data-product-id="${item.product_id}">
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
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                `;
                    document.querySelector('#shopee .inventory-table tbody').insertAdjacentHTML('beforeend', shopeeRow);
                }

                if (item.quantity_tiktok > 0) {
                    const tiktokRow = `
                    <tr data-product-id="${item.product_id}">
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
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                `;
                    document.querySelector('#tiktok .inventory-table tbody').insertAdjacentHTML('beforeend', tiktokRow);
                }
            });
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
            disableFormFields(); // Reset and lock fields on close
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
                    console.log("Product exists check response:", data); // Debugging

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
                                console.log("Confirmed variant with existingProductId:", existingProductId); // Debugging
                                submitForm(existingProductId, productName, category, size, color); // Call with existingProductId for variant
                            } else {
                                resetFormFields();
                                document.getElementById('name').value = "";
                                disableFormFields();
                            }
                        });
                    } else {
                        submitForm(null, productName, category, size, color); // Call with null if it's a new product
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

            console.log("Preparing to submit form data:", {
                existingProductId,
                productName,
                category,
                size,
                color,
                channels: selectedChannels.map(chk => chk.value)
            }); // Debugging

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
                console.log(`Channel ${channel.value}, Quantity Provided: ${quantityInput.value}`); // Debugging
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

            console.log("Final formData to submit:", Array.from(formData.entries())); // Debugging

            fetch('../../backend/controllers/add_item.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Server response after form submission:', data); // Debugging
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Product updated successfully!',
                            confirmButtonText: 'OK'
                        });
                        document.getElementById('new-item-form').reset();
                        modal.style.display = "none";
                    } else {
                        console.error('Server returned error:', data.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error: ' + data.message,
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Fetch error during form submission:', error); // Debugging
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
            console.log("Checking if product exists:", productName); // Debugging

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
                    console.log("Product exists check response:", data); // Debugging

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
                                console.log("Confirmed variant with existingProductId:", existingProductId); // Debugging
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

    /* Table styling */
    .inventory-table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 10px;
        overflow-y: auto;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        flex-grow: 1;
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
        /* Default font size */
    }

    /* Apply ellipsis only to specific columns with potentially long text */
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
.inventory-table-all th:nth-child(1), .inventory-table-all td:nth-child(1) { width: 50px; }
    .inventory-table-all th:nth-child(2), .inventory-table-all td:nth-child(2) { width: 100px; }
    .inventory-table-all th:nth-child(3), .inventory-table-all td:nth-child(3) { width: 120px; }
    .inventory-table-all th:nth-child(4), .inventory-table-all td:nth-child(4) { width: 90px; }
    .inventory-table-all th:nth-child(5), .inventory-table-all td:nth-child(5) { width: 80px; }
    .inventory-table-all th:nth-child(6), .inventory-table-all td:nth-child(6) { width: 100px; }
    .inventory-table-all th:nth-child(7), .inventory-table-all td:nth-child(7) { width: 80px; }
    .inventory-table-all th:nth-child(8), .inventory-table-all td:nth-child(8) { width: 90px; }
    .inventory-table-all th:nth-child(9), .inventory-table-all td:nth-child(9) { width: 100px; }
    .inventory-table-all th:nth-child(10), .inventory-table-all td:nth-child(10) { width: 90px; }
    .inventory-table-all th:nth-child(11), .inventory-table-all td:nth-child(11) { width: 90px; }
    .inventory-table-all th:nth-child(12), .inventory-table-all td:nth-child(12) { width: 150px; white-space: nowrap; }

    /* Column width adjustments for Physical Store tab */
    .inventory-table-physical th:nth-child(1), .inventory-table-physical td:nth-child(1) { width: 40px; }
    .inventory-table-physical th:nth-child(2), .inventory-table-physical td:nth-child(2) { width: 80px; }
    .inventory-table-physical th:nth-child(3), .inventory-table-physical td:nth-child(3) { width: 90px; }
    .inventory-table-physical th:nth-child(4), .inventory-table-physical td:nth-child(4) { width: 90px; }
    .inventory-table-physical th:nth-child(5), .inventory-table-physical td:nth-child(5) { width: 50px; }
    .inventory-table-physical th:nth-child(6), .inventory-table-physical td:nth-child(6) { width: 60px; }
    .inventory-table-physical th:nth-child(7), .inventory-table-physical td:nth-child(7) { width: 70px; }
    .inventory-table-physical th:nth-child(8), .inventory-table-physical td:nth-child(8) { width: 80px; }
    .inventory-table-physical th:nth-child(9), .inventory-table-physical td:nth-child(9) { width: 90px; }
    .inventory-table-physical th:nth-child(10), .inventory-table-physical td:nth-child(10) { width: 80px; }
    .inventory-table-physical th:nth-child(11), .inventory-table-physical td:nth-child(11) { width: 150px; }
    .inventory-table-physical th:nth-child(12), .inventory-table-physical td:nth-child(12) { width: 120px; white-space: nowrap; }

/* Column width adjustments for Shopee tab */
.inventory-table-shopee th:nth-child(1), .inventory-table-shopee td:nth-child(1) { width: 40px; }
.inventory-table-shopee th:nth-child(2), .inventory-table-shopee td:nth-child(2) { width: 80px; }
.inventory-table-shopee th:nth-child(3), .inventory-table-shopee td:nth-child(3) { width: 90px; }
.inventory-table-shopee th:nth-child(4), .inventory-table-shopee td:nth-child(4) { width: 90px; }
.inventory-table-shopee th:nth-child(5), .inventory-table-shopee td:nth-child(5) { width: 50px; }
.inventory-table-shopee th:nth-child(6), .inventory-table-shopee td:nth-child(6) { width: 60px; }
.inventory-table-shopee th:nth-child(7), .inventory-table-shopee td:nth-child(7) { width: 70px; }
.inventory-table-shopee th:nth-child(8), .inventory-table-shopee td:nth-child(8) { width: 80px; }
.inventory-table-shopee th:nth-child(9), .inventory-table-shopee td:nth-child(9) { width: 90px; }
.inventory-table-shopee th:nth-child(10), .inventory-table-shopee td:nth-child(10) { width: 80px; }
.inventory-table-shopee th:nth-child(11), .inventory-table-shopee td:nth-child(11) { width: 150px; }
.inventory-table-shopee th:nth-child(12), .inventory-table-shopee td:nth-child(12) { width: 120px; white-space: nowrap; }

/* Column width adjustments for TikTok tab */
.inventory-table-tiktok th:nth-child(1), .inventory-table-tiktok td:nth-child(1) { width: 40px; }
.inventory-table-tiktok th:nth-child(2), .inventory-table-tiktok td:nth-child(2) { width: 80px; }
.inventory-table-tiktok th:nth-child(3), .inventory-table-tiktok td:nth-child(3) { width: 90px; }
.inventory-table-tiktok th:nth-child(4), .inventory-table-tiktok td:nth-child(4) { width: 90px; }
.inventory-table-tiktok th:nth-child(5), .inventory-table-tiktok td:nth-child(5) { width: 50px; }
.inventory-table-tiktok th:nth-child(6), .inventory-table-tiktok td:nth-child(6) { width: 60px; }
.inventory-table-tiktok th:nth-child(7), .inventory-table-tiktok td:nth-child(7) { width: 70px; }
.inventory-table-tiktok th:nth-child(8), .inventory-table-tiktok td:nth-child(8) { width: 80px; }
.inventory-table-tiktok th:nth-child(9), .inventory-table-tiktok td:nth-child(9) { width: 90px; }
.inventory-table-tiktok th:nth-child(10), .inventory-table-tiktok td:nth-child(10) { width: 80px; }
.inventory-table-tiktok th:nth-child(11), .inventory-table-tiktok td:nth-child(11) { width: 150px; }
.inventory-table-tiktok th:nth-child(12), .inventory-table-tiktok td:nth-child(12) { width: 120px; white-space: nowrap; }


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
</style>

