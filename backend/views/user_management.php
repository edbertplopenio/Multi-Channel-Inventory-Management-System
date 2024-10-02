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
    <title>User Management</title>
    <link rel="stylesheet" href="/frontend/public/styles/user_management.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="user-management-container">
        <div class="header">
            <h1>User Management</h1>
            <button class="new-user-button">+ Add New User</button>
        </div>

        <div class="filters">
            <div class="tabs-container">
                <button class="tab active" data-tab="all-users">
                    <i class="fas fa-users"></i> All Users
                </button>
                <button class="tab" data-tab="admins">
                    <i class="fas fa-user-shield"></i> Admins
                </button>
                <button class="tab" data-tab="inventory-manager">
                    <i class="fas fa-box"></i> Inventory Manager
                </button>
            </div>
            <div class="filter-input-container">
                <input type="text" class="filter-input" placeholder="Filter users">
                <i class="fas fa-filter icon-filter"></i>
            </div>
        </div>

        <!-- All Users Content -->
        <div id="all-users" class="tab-content active">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example rows -->
                    <tr>
                        <td>UID001</td>
                        <td>John Doe</td>
                        <td>john.doe@example.com</td>
                        <td>Admin</td>
                        <td><span class="status active">Active</span></td>
                        <td>
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Admins Content -->
        <div id="admins" class="tab-content">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>UID002</td>
                        <td>Jane Smith</td>
                        <td>jane.smith@example.com</td>
                        <td>Admin</td>
                        <td><span class="status inactive">Inactive</span></td>
                        <td>
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Inventory Manager Content -->
        <div id="inventory-manager" class="tab-content">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>UID005</td>
                        <td>Alice Green</td>
                        <td>alice.green@example.com</td>
                        <td>Inventory Manager</td>
                        <td><span class="status active">Active</span></td>
                        <td>
                            <button class="action-button edit"><i class="fas fa-edit"></i> Edit</button>
                            <button class="action-button delete"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- New User Modal -->
    <div id="new-user-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <div class="header">
                <h1>Add New User</h1>
            </div>

            <form id="new-user-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first-name">First Name:</label>
                        <input type="text" id="first-name" name="first-name" required>
                    </div>

                    <div class="form-group">
                        <label for="last-name">Last Name:</label>
                        <input type="text" id="last-name" name="last-name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="role">Role:</label>
                        <input type="text" id="role" name="role" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>

                <div class="form-row buttons-row">
                    <button type="button" class="cancel-button">Cancel</button>
                    <button type="submit" class="save-user-button">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function initializeUserManagement() {
            // Handle tab switching
            document.querySelector('.tabs-container').addEventListener('click', function(event) {
                if (event.target.classList.contains('tab')) {
                    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    event.target.classList.add('active');
                    document.getElementById(event.target.getAttribute('data-tab')).classList.add('active');
                }
            });

            // Show the modal when the "Add New User" button is clicked
            const modal = document.getElementById("new-user-modal");
            const newUserButton = document.querySelector(".new-user-button");
            const closeButton = document.querySelector(".close-button");

            newUserButton.addEventListener('click', function() {
                modal.style.display = "flex"; // Display modal
            });

            // Close the modal when the close button is clicked
            closeButton.addEventListener('click', function() {
                modal.style.display = "none"; // Hide modal
            });

            // Close the modal if the user clicks outside of it
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });

            // Handle form submission (placeholder, you can replace with actual logic)
            document.getElementById('new-user-form').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent form submission for now

                const firstName = document.getElementById('first-name').value;
                const lastName = document.getElementById('last-name').value;
                const email = document.getElementById('email').value;
                const role = document.getElementById('role').value;
                const password = document.getElementById('password').value;

                // Placeholder for form handling, e.g., adding the new user
                alert(`User ${firstName} ${lastName} added!`);

                document.getElementById('new-user-form').reset(); // Reset the form
                modal.style.display = "none"; // Hide modal
            });
        }

        // Call the initialization function when the page loads or when entering the section
        initializeUserManagement();
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

.user-management-container {
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

.new-user-button {
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

.new-user-button:hover {
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

.user-table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    border-radius: 10px;
    overflow-y: auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    flex-grow: 1;
}

.user-table th, .user-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.user-table th {
    background-color: #f4f7fc;
    color: #555;
    font-size: 12px;
    font-weight: 600;
}

.user-table td {
    color: #555;
    font-size: 12px;
}

.user-table .status {
    padding: 5px 10px;
    border-radius: 12px;
    color: #fff;
    font-size: 12px;
    text-align: center;
    display: inline-block;
}

.status.active {
    background-color: #28a745;
}

.status.inactive {
    background-color: #dc3545;
}

.user-table tr:last-child td {
    border-bottom: none;
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

/* Hidden by default, shown when active */
.tab-content {
    display: none;
    padding-top: 20px;
}

.tab-content.active {
    display: block;
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

.new-user-container form {
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
    transition: border-color 0.3s ease;
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

.cancel-button, .save-user-button {
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

.save-user-button {
    background-color: #007bff;
    color: white;
    width: 200px;
}

.save-user-button:hover {
    background-color: #0056b3;
}
</style>
