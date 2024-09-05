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
                        <th>Stock Level</th>
                        <th>Category</th>
                        <th>Reorder Level</th>
                        <th>Supplier</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>INV001</td>
                        <td>Item A</td>
                        <td>50</td>
                        <td>Category 1</td>
                        <td>20</td>
                        <td>Supplier A</td>
                        <td><img src="image-placeholder.png" alt="Image" width="50"></td>
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
                                <label for="stock-level" class="swal-label">Stock Level</label>
                                <input type="number" id="stock-level" name="stock-level" class="swal-input" required>
                            </div>
                            <div class="swal-form-group">
                                <label for="category" class="swal-label">Category</label>
                                <input type="text" id="category" name="category" class="swal-input" required>
                            </div>
                            <div class="swal-form-group">
                                <label for="reorder-level" class="swal-label">Reorder Level</label>
                                <input type="number" id="reorder-level" name="reorder-level" class="swal-input" required>
                            </div>
                            <div class="swal-form-group">
                                <label for="supplier" class="swal-label">Supplier</label>
                                <input type="text" id="supplier" name="supplier" class="swal-input" required>
                            </div>
                            <div class="swal-form-group">
                                <label for="image" class="swal-label">Image</label>
                                <input type="file" id="image" name="image" class="swal-input-file">
                            </div>
                        </form>
                    </div>
                `,
                width: '700px',
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
                            stockLevel: form.stockLevel.value,
                            category: form.category.value,
                            reorderLevel: form.reorderLevel.value,
                            supplier: form.supplier.value,
                            image: form.image.files[0]
                        };
                    } else {
                        Swal.showValidationMessage('Please fill out all required fields.');
                        return false;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Handle the form submission here, e.g., send data to the server
                    console.log(result.value); // Access the form data
                }
            });
        });
    </script>

</body>
</html>
