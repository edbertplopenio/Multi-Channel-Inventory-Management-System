<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: ../../frontend/public/login.html");
    exit();
}

// Include the database connection file
require_once '../../backend/config/db_connection.php'; // Adjust path to your actual database config

if (!$conn) {
    die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()])); // Handle db connection failure
}

// Define the SQL query for 'all-inventory'
$sql = "
    SELECT p.name AS product_name,
           p.category,
           iv.channel,
           SUM(iv.quantity) AS total_quantity
    FROM inventory iv
    INNER JOIN product_variants pv ON iv.variant_id = pv.variant_id
    INNER JOIN products p ON pv.product_id = p.product_id
    GROUP BY p.name, iv.channel
";

// Query the database
$result = mysqli_query($conn, $sql);

// Initialize stockData array
$stockData = [];

// Fetch the stock data for each product
while ($row = mysqli_fetch_assoc($result)) {
    $productName = htmlspecialchars($row['product_name']);
    $category = htmlspecialchars($row['category']);
    $channel = htmlspecialchars($row['channel']);
    $quantity = (int) $row['total_quantity'];

    // Ensure product exists in stockData and initialize missing data
    if (!isset($stockData[$productName])) {
        $stockData[$productName] = [
            'category' => $category,
            'physical_store' => 0,
            'shopee' => 0,
            'tiktok' => 0
        ];
    }

    // Set the quantity for the given channel
    $stockData[$productName][$channel] = $quantity;
}

// Now, calculate the Reorder Level, Optimal Stock Level, and Replenishment Quantity for each product

$replenishmentData = [];

foreach ($stockData as $productName => $data) {
    // Query sales data for the product over the last 30 days
    $sales_sql = "
        SELECT SUM(s.quantity) AS total_sales, DATEDIFF(CURDATE(), MIN(s.sale_date)) AS days_in_period
        FROM sales s
        JOIN inventory i ON s.inventory_id = i.inventory_id
        JOIN product_variants pv ON i.variant_id = pv.variant_id
        JOIN products p ON pv.product_id = p.product_id
        WHERE p.name = '$productName'
        AND s.sale_date > NOW() - INTERVAL 30 DAY
    ";
    
    $sales_result = mysqli_query($conn, $sales_sql);
    if (!$sales_result) {
        die("Error executing sales query: " . mysqli_error($conn));
    }
    
    $sales_data = mysqli_fetch_assoc($sales_result);
    
    $average_daily_sales = $sales_data['total_sales'] / max($sales_data['days_in_period'], 1); // Avoid division by zero

    // Calculate Reorder Level (assuming lead time of 7 days)
    $reorder_level = round($average_daily_sales * 7); // 7 days lead time

    // Calculate Optimal Stock Level (assuming 30 days stock cover)
    $optimal_stock_level = round($average_daily_sales * 30); // 30 days cover

    // Get current stock levels for the product in the inventory (physical store, Shopee, TikTok)
    $current_stock_sql = "
        SELECT SUM(iv.quantity) AS total_quantity
        FROM inventory iv
        JOIN product_variants pv ON iv.variant_id = pv.variant_id
        JOIN products p ON pv.product_id = p.product_id
        WHERE p.name = '$productName' AND iv.channel IN ('physical_store', 'shopee', 'tiktok')
    ";
    
    $current_stock_result = mysqli_query($conn, $current_stock_sql);
    $current_stock_data = mysqli_fetch_assoc($current_stock_result);
    $current_stock = (int) $current_stock_data['total_quantity'];

    // Calculate Replenishment Quantity
    $replenishment_quantity = max($optimal_stock_level - $current_stock, 0); // No negative replenishment

    // Store the calculated values
    $replenishmentData[$productName] = [
        'category' => $data['category'],
        'reorder_level' => $reorder_level,
        'optimal_stock_level' => $optimal_stock_level,
        'replenishment_quantity' => $replenishment_quantity,
        'physical_store' => $data['physical_store'],
        'shopee' => $data['shopee'],
        'tiktok' => $data['tiktok']
    ];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory Replenishment</title>
  <link rel="stylesheet" href="../../frontend/public/styles/inventory_replenishment.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery for ease of DOM manipulation -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js library -->
</head>

<body>

  <div class="inventory-replenishment-container">
    <div class="header">
      <h1>Inventory Replenishment</h1>
    </div>

    <div class="filters">
      <div class="tabs-container">
        <!-- Only the Replenishment Details tab remains active -->
        <button class="tab" data-tab="replenishment-details">
          <i class="fas fa-boxes"></i> Replenishment Details
        </button>
      </div>
      <div class="filter-input-container">
        <input type="text" class="filter-input" placeholder="Filter inventory">
        <i class="fas fa-filter icon-filter"></i>
      </div>
    </div>

    <div class="inventory-content">
      <!-- Replenishment Details Content -->
      <div id="replenishment-details" class="tab-content active">
        <div class="inventory-details-container">
          <div class="inventory-details">
            <p id="product-details">Select a product to see detailed inventory replenishment information here.</p>
          </div><br>
          <!-- Chart will be injected here -->
          <div id="chart-container" style="display:none;">
            <canvas id="product-stock-chart" width="400" height="200"></canvas>
          </div>
        </div>
        <div class="inventory-table-container">
          <div class="inventory-table-wrapper">
            <table class="inventory-table">
              <thead>
                <tr>
                  <th>Product Name</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Re-query for all product names and display them in the table
                foreach ($replenishmentData as $productName => $data) {
                  $category = $data['category'];
                  $physicalStore = $data['physical_store'];
                  $shopee = $data['shopee'];
                  $tiktok = $data['tiktok'];

                  echo "<tr class='product-row' data-name='$productName' data-category='$category'
                        data-physical-store='$physicalStore'
                        data-shopee='$shopee'
                        data-tiktok='$tiktok'
                        data-reorder-level='{$data['reorder_level']}'
                        data-optimal-stock-level='{$data['optimal_stock_level']}'
                        data-replenishment-quantity='{$data['replenishment_quantity']}'>";

                  echo "<td>$productName</td>";
                  echo "</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>


  <script>
  $(document).ready(function() {
    // Event listener for the filter input
    $('.filter-input').on('input', function() {
      var filterValue = $(this).val().toLowerCase();  // Get the input value and convert to lowercase
      $('.inventory-table .product-row').each(function() {
        var productName = $(this).data('name').toLowerCase();  // Get the product name from the data attribute and convert to lowercase

        // Check if the product name contains the filter value
        if (productName.indexOf(filterValue) !== -1) {
          $(this).show();  // Show the row if it matches
        } else {
          $(this).hide();  // Hide the row if it doesn't match
        }
      });
    });
  });
</script>


  <script>
$(document).ready(function() {
  // When a row in the inventory table is clicked
  $('.product-row').on('click', function() {
    // Hide the default heading
    $('#replenishment-details h2').hide();

    // Get product details from the clicked row's data attributes
    const productName = $(this).data('name');
    const category = $(this).data('category');
    const physicalStore = $(this).data('physical-store');
    const shopee = $(this).data('shopee');
    const tiktok = $(this).data('tiktok');
    const reorderLevel = $(this).data('reorder-level');
    const optimalStockLevel = $(this).data('optimal-stock-level');
    const replenishmentQuantity = $(this).data('replenishment-quantity');

    // Update the details container with the selected product's information
    $('#product-details').html(`
      <p><strong>Product Name:</strong> ${productName}</p>
      <p><strong>Category:</strong> ${category}</p>
      <p><strong>Reorder Level:</strong> ${reorderLevel}</p>
      <p><strong>Optimal Stock Level:</strong> ${optimalStockLevel}</p>
      <p><strong>Replenishment Quantity:</strong> ${replenishmentQuantity}</p>
    `);

    // Highlight the clicked row and remove highlight from other rows
    $('.product-row').removeClass('highlighted'); // Remove highlight from all rows
    $(this).addClass('highlighted'); // Add highlight to the clicked row




      // Show the chart container
      $('#chart-container').show();

      // Prepare data for the chart
      const chartData = {
        labels: ['Physical Store', 'Shopee', 'TikTok'],
        datasets: [{
          label: 'Stock per Channel',
          data: [physicalStore, shopee, tiktok],
          backgroundColor: [
            '#A3C9E7', // Physical Store
            '#FF6F61', // Shopee
            '#00F2EA'  // TikTok
          ],
          borderColor: ['#A3C9E7', '#FF6F61', '#00F2EA'], // Solid borders matching the colors
          borderWidth: 1
        }]
      };

      // Chart options with customized legend and tooltip
      const chartOptions = {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return value; // No currency formatting, just return the number
              }
            }
          }
        },
        plugins: {
          legend: {
            position: 'right',
            align: 'start',
            labels: {
              generateLabels: function(chart) {
                const data = chart.data;
                return data.labels.map((label, index) => ({
                  text: label, // Show the channel name in the legend
                  fillStyle: data.datasets[0].backgroundColor[index],
                  hidden: chart.getDatasetMeta(0).data[index].hidden,
                  lineWidth: 1,
                  strokeStyle: data.datasets[0].backgroundColor[index],
                  pointStyle: 'circle'
                }));
              },
              color: '#333',
              font: {
                size: 12,
                weight: 'normal'
              },
              padding: 15,
              usePointStyle: true
            }
          },
          tooltip: {
            callbacks: {
              label: function(tooltipItem) {
                const value = tooltipItem.raw;
                return `${value.toLocaleString()}`; // No currency symbol, just the number
              }
            }
          }
        }
      };

      // Create or update the chart
      const ctx = document.getElementById('product-stock-chart').getContext('2d');
      if (window.productStockChart) {
        window.productStockChart.destroy(); // Destroy old chart instance
      }
      window.productStockChart = new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: chartOptions
      });
    });
  });
</script>


</body>

</html>

<style>
  .highlighted {
    background-color: #f1f1f1; /* Light gray background */
    font-weight: bold;
  }

  .highlighted td {
    color: #007bff; /* Blue text color for better visibility */
  }
</style>


<style>

  /* Smaller and Wider Product Details Container */
#product-details {
  font-size: 12.5px; /* Smaller font size */
  max-width: 900px; /* Wider container */
}

/* Styling for each detail section */
#product-details p {
  margin: 6px 0; /* Reduced margin */
  font-weight: 300;
}

/* Styling for labels (e.g., "Product Name", "Category") */
#product-details strong {
  color: #2c3e50; /* Darker color for the label */
  font-size: 13.5px; /* Slightly smaller label size */
}

/* Highlight the values in the details */
#product-details span {
  font-weight: 400;
  color: #1abc9c; /* Highlight color for values */
}


/* Add responsive styling for mobile view */
@media (max-width: 768px) {
  #product-details {
    padding: 10px 15px;
    font-size: 0.85rem;
    max-width: 100%; /* Full width on mobile */
    margin-top: 8px;
  }

  #product-details strong {
    font-size: 0.95rem;
  }

  #product-details span {
    font-size: 15px;
  }
}



#chart-container {
  width: 100%; /* Makes the chart container responsive */
  height: 360px; /* Adjust the height as needed */
}

#product-stock-chart {
  width: 100% !important;  /* Ensure the chart fills the container's width */
  height: 100% !important; /* Ensure the chart fills the container's height */
}

/* Optional: Control the size for different screen sizes (responsive design) */
@media (max-width: 768px) {
  #chart-container {
    height: 300px; /* Adjust height for smaller screens */
  }
}

@media (max-width: 480px) {
  #chart-container {
    height: 250px; /* Adjust height for very small screens */
  }
}


</style>


<style>
  @import url("https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700");

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Inter", sans-serif;
}

body {
  background-color: #f4f7fc;
  margin: 0;
  padding: 0;
  height: 100vh;
}

.inventory-replenishment-container {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
  background-color: #ffffff;
  box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05);
  border-radius: 10px;
  height: 95vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
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

.filters {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
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

.inventory-content {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  flex: 1;
  width: 100%;
  overflow: auto;
}

.inventory-details-container {
  width: 75%;
  background-color: #f4f7fc;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.05);
}

.inventory-details h2 {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 10px;
}

.inventory-details p {
  font-size: 14px;
  color: #555;
}

.inventory-table-container {
  width: 25%;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
}

.inventory-table-wrapper {
  overflow-y: auto;
  max-height: 600px;
}

.inventory-table {
  width: 100%;
  border-collapse: collapse;
  background-color: #fff;
}

.inventory-table thead th {
  background-color: #f4f7fc;
  color: #555;
  font-size: 12px;
  font-weight: 600;
  position: sticky;
  top: 0;
  z-index: 2;
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.inventory-table tbody {
  display: block;
  overflow-y: auto;
  max-height: 450px;
}

.inventory-table thead,
.inventory-table tbody tr {
  display: table;
  width: 100%;
  table-layout: fixed;
}

.inventory-table tbody td {
  padding: 10px;
  font-size: 12px;
  color: #555;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.inventory-table tr:last-child td {
  border-bottom: none;
}

.tab-content {
  display: none;
  padding-top: 20px;
  width: 100%;
}

.tab-content.active {
  display: flex;
  gap: 20px;
}

</style>