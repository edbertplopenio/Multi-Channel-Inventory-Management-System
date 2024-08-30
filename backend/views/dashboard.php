<?php
session_start();

// Check if the user is logged in
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
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../frontend/public/styles/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>

    <div class="dashboard-container">
        <div class="header">
            <h1>Analytics</h1>
            <div class="date-picker">
                <input type="date" value="2023-01-01">
                <span>to</span>
                <input type="date" value="2023-01-31">
            </div>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
                <img src="../../frontend/public/images/user.jpg" alt="User Avatar" class="user-avatar">
            </div>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h2>Orders</h2>
                <p class="metric">201</p>
                <span class="metric-info"><i class="fas fa-arrow-up"></i> 8.2% since last month</span>
            </div>
            <div class="card">
                <h2>Approved</h2>
                <p class="metric">36</p>
                <span class="metric-info"><i class="fas fa-arrow-up"></i> 3.4% since last month</span>
            </div>
            <div class="card">
                <h2>Users</h2>
                <p class="metric">4,890</p>
                <div class="chart-placeholder"><!-- Chart Placeholder --></div>
            </div>
            <div class="card">
                <h2>Subscriptions</h2>
                <p class="metric">1,201</p>
                <div class="chart-placeholder"><!-- Chart Placeholder --></div>
            </div>
            <div class="card">
                <h2>Month Total</h2>
                <p class="metric">$25,410</p>
                <span class="metric-info"><i class="fas fa-arrow-down"></i> 0.2% since last month</span>
            </div>
            <div class="card">
                <h2>Revenue</h2>
                <p class="metric">$1,352</p>
                <span class="metric-info"><i class="fas fa-arrow-down"></i> 1.2% since last month</span>
            </div>
            <div class="card full-width">
                <h2>Sales Dynamics</h2>
                <div class="chart-placeholder large"><!-- Large Chart Placeholder --></div>
            </div>
            <div class="card full-width">
                <h2>Overall User Activity</h2>
                <div class="chart-placeholder large"><!-- Large Chart Placeholder --></div>
            </div>
            <div class="card half-width">
                <h2>Paid Invoices</h2>
                <p class="metric">$30,256.23</p>
                <span class="metric-info"><i class="fas fa-arrow-up"></i> 15% Current Financial Year</span>
            </div>
            <div class="card half-width">
                <h2>Funds Received</h2>
                <p class="metric">$150,256.23</p>
                <span class="metric-info"><i class="fas fa-arrow-up"></i> 59% Current Financial Year</span>
            </div>
            <div class="card full-width">
                <h2>Customer Orders</h2>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Address</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><img src="../../frontend/public/images/user.jpg" alt="Profile"> Press</td>
                            <td>London</td>
                            <td>22.08.2023</td>
                            <td>Delivered</td>
                            <td>$920</td>
                        </tr>
                        <tr>
                            <td><img src="../../frontend/public/images/user.jpg" alt="Profile"> Marina</td>
                            <td>Man City</td>
                            <td>24.08.2023</td>
                            <td>Processed</td>
                            <td>$452</td>
                        </tr>
                        <tr>
                            <td><img src="../../frontend/public/images/user.jpg" alt="Profile"> Alex</td>
                            <td>Unknown</td>
                            <td>18.08.2023</td>
                            <td>Cancelled</td>
                            <td>$1,200</td>
                        </tr>
                        <tr>
                            <td><img src="../../frontend/public/images/user.jpg" alt="Profile"> Robert</td>
                            <td>New York</td>
                            <td>03.08.2023</td>
                            <td>Delivered</td>
                            <td>$1,235</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
