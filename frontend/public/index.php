<?php
// Start the session
session_start();

// Redirect to login.html if not authenticated
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}




// Include the database connection file
require_once '../../backend/config/db_connection.php';

// Initialize placeholders
$user_role = "Unknown Role";
$user_name = "Unknown User";
$user_image = "default_user.jpg"; // Default image

// Fetch the user details (role, name, and image) from the database
$user_email = $_SESSION['user_email'];
$sql = "SELECT id, role, CONCAT(first_name, ' ', last_name) AS full_name, image FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, "s", $user_email);
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the user's details
    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['user_role'] = $row['role'];                // Store role in the session
        $_SESSION['user_id'] = $row['id'];                   // Ensure user_id is in session
        $_SESSION['user_image'] = $row['image'] ?: $user_image; // Use default if no image is set
        $_SESSION['user_name'] = $row['full_name'];          // Store full name in session
        $user_role = $row['role'];
        $user_name = $row['full_name'];
    } else {
        // Handle case where user is not found in the database
        session_unset();
        session_destroy();
        header("Location: login.html");
        exit();
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    // Handle SQL preparation error
    die("Database error: Failed to prepare statement.");
}

// Close the database connection
mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main System</title>

    <link rel="icon" href="../../frontend/public/images/bmsuiticon.png" type="image/png">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.js"></script> <!-- Include jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <div class="container">
        <div class="sidebar">
            <div class="head">
                <div class="user-img">
                    <div class="user-img">
                        <img src="../../frontend/public/images/users/<?php echo htmlspecialchars($_SESSION['user_image']); ?>" alt="User Image" />
                    </div>
                </div>
                <div class="user-details">
                    <p class="title"><?php echo htmlspecialchars($_SESSION['user_role']); ?></p>
                    <p class="name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                </div>
            </div>
            <div class="nav">
                <div class="menu">
                    <ul>
                        <li class="active" id="dashboard-item">
                            <a href="#" id="dashboard-link">
                                <i class="icon ph-bold ph-house-simple"></i> <!-- Icon -->
                                <span class="text">Dashboard</span>
                            </a>
                        </li>
                        <li id="inventory-item">
                            <a href="#" id="inventory-link">
                                <i class="icon ph-bold ph-archive"></i> <!-- Icon -->
                                <span class="text">Inventory Management</span>
                            </a>
                        </li>
                        <li id="sales-item">
                            <a href="#" id="sales-link">
                                <i class="icon ph-bold ph-file-text"></i> <!-- Icon -->
                                <span class="text">Sales Record</span>
                            </a>
                        </li>
                        <li id="forecast-item">
                            <a href="#" id="forecast-link">
                                <i class="icon ph-bold ph-activity"></i> <!-- Icon -->
                                <span class="text">Demand Forecasting</span>
                            </a>
                        </li>
                        <li id="replenishment-item">
                            <a href="#" id="replenishment-link">
                                <i class="icon ph ph-arrow-counter-clockwise"></i> <!-- Icon -->
                                <span class="text">Inventory Replenishment</span>
                            </a>
                        </li>
                        <!-- Archive Items section -->
                        <li id="archive-item">
                            <a href="#" id="archive-link">
                                <i class="icon ph-bold ph-folder-simple"></i> <!-- Icon -->
                                <span class="text">Archived Items</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="account-section">
                <div class="menu">
                    <p class="title">Account</p>
                    <ul>
                        <!-- Replaced Help with User Management -->
                        <li id="user-account-item">
                            <a href="#" id="user-account-link">
                                <i class="icon ph-bold ph-user"></i> <!-- Icon -->
                                <span class="text">User Management</span>
                            </a>
                        </li>
                        <li id="logout-item">
                            <a href="../../backend/views/logout.php" id="logout-link">
                                <i class="icon ph-bold ph-sign-out"></i>
                                <span class="text">Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="main-content">
            <!-- Dashboard content will be loaded here by default -->
        </div>
    </div>

    <!-- JavaScript to dynamically load content -->
    <script>
        $(document).ready(function() {
            // Load dashboard content by default when the page is loaded
            $('#main-content').load('../../backend/views/dashboard.php');

            // Function to handle active class toggle
            function setActive(itemId) {
                // Remove active class from all menu items
                $('.menu ul li').removeClass('active');
                // Add active class to the clicked menu item
                $('#' + itemId).addClass('active');
            }

            // Use delegated event listeners for dynamically loaded content
            $('.menu').on('click', 'a', function(e) {
                const linkId = $(this).attr('id');
                let contentUrl = '';

                // Only prevent default for in-app links
                if (linkId !== 'logout-link') {
                    e.preventDefault();
                }

                switch (linkId) {
                    case 'dashboard-link':
                        contentUrl = '../../backend/views/dashboard.php';
                        setActive('dashboard-item');
                        break;
                    case 'inventory-link':
                        contentUrl = '../../backend/views/inventory_management.php';
                        setActive('inventory-item');
                        break;
                    case 'sales-link':
                        contentUrl = '../../backend/views/sales_record.php';
                        setActive('sales-item');
                        break;
                    case 'forecast-link':
                        contentUrl = '../../backend/views/demand_forecasting.php';
                        setActive('forecast-item');
                        break;
                    case 'replenishment-link':
                        contentUrl = '../../backend/views/inventory_replenishment.php';
                        setActive('replenishment-item');
                        break;
                    case 'user-account-link':
                        contentUrl = '../../backend/views/user_management.php';
                        setActive('user-account-item');
                        break;
                    case 'archive-link':
                        contentUrl = '../../backend/views/archived_items.php';
                        setActive('archive-item');
                        break;
                    default:
                        return; // Do nothing if the link ID is not recognized
                }

                if (contentUrl) {
                    $('#main-content').load(contentUrl, function(response, status, xhr) {
                        if (status === "error") {
                            console.error(`Error loading content: ${xhr.statusText}`);
                        } else {
                            console.log(`Loaded content from ${contentUrl}`);

                            // Re-initialize charts after loading new content
                    initializeCharts();  // This will ensure charts are reinitialized after each content load

                                        // Re-initialize checkboxes after loading new content
                                        initializeCheckboxes();

                        }
                    });
                }
            });

            // Account section links (example for logout link)
            $('.account-section').on('click', '#logout-link', function(e) {
                e.preventDefault(); // Prevent the default logout behavior

                // Show SweetAlert confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to logout?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, logout!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to logout.php
                        window.location.href = '../../backend/views/logout.php';
                    }
                });
            });

        });
    </script>




 <script>
// Global variable to hold the chart instance
let salesChart = null;

// Function to fetch sales data and initialize the sales chart
function fetchSalesData() {
    $.ajax({
        url: '../../backend/controllers/fetch_sales_data.php', // Adjust the path to your PHP script
        method: 'GET',
        success: function(data) {
            if (data && Array.isArray(data) && data.length > 0) {
                // Extract the months and sales revenues from the fetched data
                const months = data.map(item => item.month);
                const salesRevenue = data.map(item => parseFloat(item.sales_revenue));

                // Initialize the sales chart with the fetched data
                initializeSalesChart(months, salesRevenue);
            } else {
                console.warn('No sales data available.');
            }
        },
        error: function(error) {
            console.error('Error fetching sales data:', error);
        }
    });
}

// Function to initialize the sales chart
function initializeSalesChart(months, salesRevenue) {
    const ctx = document.getElementById('salesDynamicChart')?.getContext('2d');
    if (ctx) {
        // Destroy any existing chart instance to avoid multiple charts on the same canvas
        if (salesChart) {
            salesChart.destroy();
        }

        // Create a new chart instance
        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,  // Labels are the months fetched from the database
                datasets: [{
                    label: 'Sales Revenue (₱)',  // Change to currency if needed
                    data: salesRevenue,  // Data is the sales revenue for each month
                    borderColor: '#5bc0f8',
                    backgroundColor: 'rgba(91, 192, 248, 0.2)',
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    } else {
        console.warn('Canvas for sales chart not found.');
    }
}

// Function to load content dynamically
function loadPage(contentUrl, initFunction = null) {
    $('#main-content').load(contentUrl, function(response, status, xhr) {
        if (status === "error") {
            console.error(`Error loading content: ${xhr.statusText}`);
        } else if (typeof initFunction === 'function') {
            initFunction();
        }
    });
}

$(document).ready(function() {
    // Load dashboard content by default
    loadPage('../../backend/views/dashboard.php', fetchSalesData); // Changed to call fetchSalesData directly

    // Handle menu clicks
    $('.menu').on('click', 'a', function(e) {
        e.preventDefault();
        const linkId = $(this).attr('id');
        let contentUrl = '';
        let initFunction = null;

        if (linkId === 'dashboard-link') {
            contentUrl = '../../backend/views/dashboard.php';
            initFunction = fetchSalesData; // Fetch data again when navigating to dashboard
        }
        // Add cases for other links...

        if (contentUrl) {
            loadPage(contentUrl, initFunction);
        }
    });
});
</script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>




</body>

</html>

<style>
    /* Sales Dynamics Chart Styling */
    .card-sales-dynamic {
        position: relative;
        /* Ensure proper positioning for the chart */
        overflow: hidden;
        /* Prevent content overflow */
        padding: 15px;
        /* Add padding to prevent the chart from touching the edges */
    }

    .card-sales-dynamic canvas {
        display: block;
        /* Ensure the canvas is treated as a block element */
        width: 100% !important;
        /* Make the chart take the full width of the container */
        height: 80% !important;
        /* Make the chart take the full height of the container */
        max-height: 100%;
        /* Prevent the chart from exceeding the container height */
        max-width: 100%;
        /* Prevent the chart from exceeding the container width */
    }
</style>


<style>
    .card-sales-by-category {
        position: relative;
        padding: 10px;
        /* Add some padding inside the card */
        overflow: hidden;
        height: 245px;
        /* Reduced height for a smaller card */
    }

    .card-sales-by-category canvas {
        width: 80% !important;
        /* Reduce the chart width */
        height: 80% !important;
        /* Reduce the chart height */
        max-height: 100%;
        /* Prevent overflow */
    }
</style>


<style>
    .card-channels {
        position: relative;
        /* Padding inside the card */
        overflow: hidden;

    }

    .card-channels canvas {
        width: 100% !important;
        /* Ensure the canvas spans the width of the container */
        height: 90% !important;
        /* Ensure the canvas spans the height of the container */
        max-height: 90%;
        /* Prevent overflow */
    }
</style>


<style>
    @import url('https://fonts.googleapis.com/css?family=Inter:100,200,300,regular,500,600,700,800,900');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 0;
    font-family: 'Inter', sans-serif;
}

.container {
    display: flex;
    height: 100vh; /* Ensure the container takes full height */
    flex-direction: row; /* Make sure the flex direction is row */
}

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 256px;
    height: 100%;
    background-color: white;
    padding: 24px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.sidebar .head {
    display: flex;
    gap: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f6f6f6;
}

.user-img {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    overflow: hidden;
}

.user-img img {
    width: 100%;
    object-fit: cover;
}

.user-details .title {
    font-size: 12px;
    font-weight: 500;
    color: #757575;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.user-details .name {
    font-size: 14px;
    font-weight: 500;
}

.nav {
    flex: 1;
}

.menu .title {
    font-size: 12px;
    font-weight: 500;
    color: #757575;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.menu ul li {
    list-style: none;
    margin-bottom: 10px;
}

.menu ul li a {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 500;
    color: #757575;
    text-decoration: none;
    padding: 12px 8px;
    transition: all 0.3s;
}

.menu ul li a:hover,
.menu ul li.active > a {
    color: #000;
    background-color: #f6f6f6;
}

.menu ul li .icon {
    font-size: 20px;
}

.menu ul li .text {
    flex: 1;
}

.menu ul li .arrow {
    font-size: 14px;
    transition: transform 0.3s ease; /* Ensure the arrow smoothly rotates */
}

.menu ul li.active .arrow {
    transform: rotate(180deg); /* Rotate the arrow when the menu item is active */
}

.menu .sub-menu {
    display: none;
    margin-left: 20px;
    padding-left: 20px;
    padding-top: 5px;
    border-left: 1px solid #f6f6f6;
}

.menu .sub-menu li a {
    padding: 10px 8px;
    font-size: 12px;
}

/* Highlight active submenu item */
.menu .sub-menu li a.active {
    background-color: #dcdcdc; /* Highlight color for active submenu item */
    color: #000; /* Text color for active submenu item */
}

.menu:not(:last-child) {
    padding-bottom: 10px;
    margin-bottom: 20px;
    border-bottom: 2px solid #f6f6f6;
}

/* Account section styling */
.account-section {
    margin-top: auto; /* Pushes the account section to the bottom of the sidebar */
}

.account-section .menu {
    padding-top: 20px; /* Space above the account section */
    border-top: 2px solid #f6f6f6;
}

.account-section ul {
    padding-top: 10px;
}

/* Main content styling */
.main-content {
    margin-left: 256px; /* Ensure the content starts next to the sidebar */
    padding: 20px; /* Optional padding for some space around the content */
    width: calc(100% - 256px); /* Adjust width to fill remaining space */
}

</style>