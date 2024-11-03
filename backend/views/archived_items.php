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
    pv.is_archived, pv.date_archived,
    SUM(CASE WHEN i.channel = 'physical_store' THEN i.quantity ELSE 0 END) AS quantity_physical_store,
    SUM(CASE WHEN i.channel = 'shopee' THEN i.quantity ELSE 0 END) AS quantity_shopee,
    SUM(CASE WHEN i.channel = 'tiktok' THEN i.quantity ELSE 0 END) AS quantity_tiktok
    FROM product_variants pv
    JOIN products p ON pv.product_id = p.product_id
    JOIN inventory i ON pv.variant_id = i.variant_id
    WHERE pv.is_archived = 1
    GROUP BY pv.variant_id, pv.size, pv.color, pv.price, pv.date_added, pv.image";
$result_all_inventory = mysqli_query($conn, $sql_all_inventory);

$sql_physical_store = "SELECT pv.variant_id, p.product_id, p.name, p.category, pv.size, pv.color, pv.price, pv.date_added, pv.image, i.channel, i.quantity
                       FROM product_variants pv
                       JOIN products p ON pv.product_id = p.product_id
                       JOIN inventory i ON pv.variant_id = i.variant_id
                       WHERE i.channel = 'physical_store' AND pv.is_archived = 1";
$result_physical_store = mysqli_query($conn, $sql_physical_store);

$sql_shopee = "SELECT pv.variant_id, p.product_id, p.name, p.category, pv.size, pv.color, pv.price, pv.date_added, pv.image, i.channel, i.quantity
               FROM product_variants pv
               JOIN products p ON pv.product_id = p.product_id
               JOIN inventory i ON pv.variant_id = i.variant_id
               WHERE i.channel = 'shopee' AND pv.is_archived = 1";
$result_shopee = mysqli_query($conn, $sql_shopee);

$sql_tiktok = "SELECT pv.variant_id, p.product_id, p.name, p.category, pv.size, pv.color, pv.price, pv.date_added, pv.image, i.channel, i.quantity
               FROM product_variants pv
               JOIN products p ON pv.product_id = p.product_id
               JOIN inventory i ON pv.variant_id = i.variant_id
               WHERE i.channel = 'tiktok' AND pv.is_archived = 1";
$result_tiktok = mysqli_query($conn, $sql_tiktok);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archived Inventory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow-y: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            max-height: 100%;
            table-layout: fixed;
        }

        .inventory-table th,
        .inventory-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }

        .inventory-table th {
            background-color: #f4f7fc;
            color: #555;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .inventory-table img {
            border-radius: 5px;
        }

        .tab-content {
            display: none;
            padding-top: 20px;
            height: calc(100vh - 280px);
            overflow-y: auto;
        }

        .tab-content.active {
            display: block;
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
    </style>
</head>
<body>
    <div class="inventory-container">
        <div class="header">
            <h1>Archived Inventory</h1>
        </div>
        <div class="filters">
            <div class="tabs-container">
                <button class="tab active" data-tab="all-inventory"><i class="fas fa-warehouse"></i> All Inventory</button>
                <button class="tab" data-tab="physical-store"><i class="fas fa-store"></i> Physical Store</button>
                <button class="tab" data-tab="shopee"><i class="fas fa-shopping-bag"></i> Shopee</button>
                <button class="tab" data-tab="tiktok"><i class="fas fa-music"></i> TikTok</button>
            </div>
        </div>

        <!-- All Inventory Tab -->
        <div id="all-inventory" class="tab-content active">
            <table class="inventory-table">
                <thead>
                    <tr>
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
                            <tr data-id="<?php echo $row['variant_id']; ?>">
                                <td><?php echo $row['variant_id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['quantity_physical_store'] + $row['quantity_shopee'] + $row['quantity_tiktok']; ?></td>
                                <td><?php echo $row['size']; ?></td>
                                <td><?php echo $row['color']; ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><?php echo $row['date_added']; ?></td>
                                <td>
                                    <?php
                                    $channels = [];
                                    if ($row['quantity_physical_store'] > 0) $channels[] = 'Physical Store';
                                    if ($row['quantity_shopee'] > 0) $channels[] = 'Shopee';
                                    if ($row['quantity_tiktok'] > 0) $channels[] = 'TikTok';
                                    echo implode(', ', $channels);
                                    ?>
                                </td>
                                <td><img src="../../frontend/public/images/<?php echo $row['image'] ?: 'image-placeholder.png'; ?>" alt="Image" width="50"></td>
                                <td>
                                    <button class="unarchive-button" data-id="<?php echo $row['variant_id']; ?>">Unarchive</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="11">No archived items found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Physical Store Tab -->
        <div id="physical-store" class="tab-content">
            <table class="inventory-table">
                <thead>
                    <tr>
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
                            <tr data-id="<?php echo $row['variant_id']; ?>">
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
                                    <button class="unarchive-button" data-id="<?php echo $row['variant_id']; ?>">Unarchive</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="10">No archived items in Physical Store.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Shopee Tab -->
        <div id="shopee" class="tab-content">
            <table class="inventory-table">
                <thead>
                    <tr>
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
                            <tr data-id="<?php echo $row['variant_id']; ?>">
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
                                    <button class="unarchive-button" data-id="<?php echo $row['variant_id']; ?>">Unarchive</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="10">No archived items in Shopee.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TikTok Tab -->
        <div id="tiktok" class="tab-content">
            <table class="inventory-table">
                <thead>
                    <tr>
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
                            <tr data-id="<?php echo $row['variant_id']; ?>">
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
                                    <button class="unarchive-button" data-id="<?php echo $row['variant_id']; ?>">Unarchive</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="10">No archived items in TikTok.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                tab.classList.add('active');
                document.getElementById(tab.getAttribute('data-tab')).classList.add('active');
            });
        });

        document.querySelectorAll('.unarchive-button').forEach(button => {
    button.addEventListener('click', function() {
        const variantId = this.getAttribute('data-id');
        const row = document.querySelector(`tr[data-id="${variantId}"]`);
        const itemName = row ? row.querySelector('td:nth-child(2)').innerText : 'this item';

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
                    body: JSON.stringify({ variant_id: variantId })
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
                            if (row) row.remove();
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
    });
});

    </script>
</body>
</html>
