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

// Initialize the user role
$user_role = "Unknown Role";

// Fetch the user role from the database
$user_email = $_SESSION['user_email'];
$sql = "SELECT id, role FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, "s", $user_email);
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the user's role and ID
    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['user_role'] = $row['role']; // Store role in the session
        $_SESSION['user_id'] = $row['id'];    // Ensure user_id is in session
        $user_role = $row['role'];
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
    <link rel="stylesheet" href="styles/main.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.js"></script> <!-- Include jQuery -->
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <div class="head">
                <div class="user-img">
                    <img src="images/user.jpg" alt="User Image" />
                </div>
                <div class="user-details">
                    <p class="title"><?php echo htmlspecialchars($user_role); ?></p>
                    <p class="name"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
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
                        }
                    });
                }
            });

            // Account section links (example for logout link)
            $('.account-section').on('click', '#logout-link', function() {
                setActive('logout-item'); // Set the active class for logout
            });
        });
    </script>
</body>

</html>
