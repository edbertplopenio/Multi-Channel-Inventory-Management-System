<?php
// Start the session
session_start();

// Redirect to login.php if not authenticated
if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}
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
                    <p class="title">Web Developer</p>
                    <p class="name"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                </div>
            </div>
            <div class="nav">
                <div class="menu">
                    <ul>
                        <li class="active">
                            <a href="#" id="dashboard-link">
                                <i class="icon ph-bold ph-house-simple"></i> <!-- Icon -->
                                <span class="text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon ph-bold ph-archive"></i> <!-- Icon -->
                                <span class="text">Inventory Management</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon ph-bold ph-file-text"></i> <!-- Icon -->
                                <span class="text">Sales Record</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon ph-bold ph-activity"></i> <!-- Icon -->
                                <span class="text">Demand Forecasting</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon ph ph-arrow-counter-clockwise"></i> <!-- Icon -->
                                <span class="text">Inventory Replenishment</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="icon ph-bold ph-user"></i> <!-- Icon -->
                                <span class="text">User Management</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="account-section">
                <div class="menu">
                    <p class="title">Account</p>
                    <ul>
                        <li>
                            <a href="#">
                                <i class="icon ph-bold ph-info"></i>
                                <span class="text">Help</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../backend/views/logout.php">
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

            // Load dashboard content when dashboard link is clicked
            $('#dashboard-link').on('click', function(e) {
                e.preventDefault();
                $('#main-content').load('../../backend/views/dashboard.php');
            });
        });
    </script>
</body>

</html>