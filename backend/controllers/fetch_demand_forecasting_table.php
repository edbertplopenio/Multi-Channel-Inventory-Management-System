<?php
// sales_data_api.php

// Enable CORS (Optional: Adjust the allowed origins as needed)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include the database connection
include '../config/db_connection.php'; // Ensure this file sets up $conn as a mysqli connection

// Check database connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Function to fetch aggregated sales data for charts
function getAggregatedSalesData($conn, $type) {
    // Define allowed types
    $allowed_types = ['date', 'day_of_week', 'month', 'year'];

    if (!in_array($type, $allowed_types)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid "type" parameter. Allowed values: date, day_of_week, month, year.']);
        exit;
    }

    // Define SQL queries based on the aggregation type
    switch ($type) {
        case 'date':
            $sql = "
                SELECT 
                    DATE(sale_date) AS label,
                    SUM(total_price) AS total_sales
                FROM 
                    sales
                GROUP BY 
                    DATE(sale_date)
                ORDER BY 
                    DATE(sale_date) ASC
            ";
            break;
            
        case 'day_of_week':
            $sql = "
                SELECT 
                    DAYNAME(sale_date) AS label,
                    SUM(total_price) AS total_sales
                FROM 
                    sales
                GROUP BY 
                    DAYOFWEEK(sale_date)
                ORDER BY 
                    DAYOFWEEK(sale_date) ASC
            ";
            break;
            
        case 'month':
            $sql = "
                SELECT 
                    MONTHNAME(sale_date) AS label,
                    SUM(total_price) AS total_sales
                FROM 
                    sales
                GROUP BY 
                    MONTH(sale_date)
                ORDER BY 
                    MONTH(sale_date) ASC
            ";
            break;
            
        case 'year':
            $sql = "
                SELECT 
                    YEAR(sale_date) AS label,
                    SUM(total_price) AS total_sales
                FROM 
                    sales
                GROUP BY 
                    YEAR(sale_date)
                ORDER BY 
                    YEAR(sale_date) ASC
            ";
            break;
    }

    // Execute the SQL query
    $result = $conn->query($sql);

    if ($result) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            // Ensure total_sales is a float
            $data[] = [
                'label' => $row['label'],
                'total_sales' => floatval($row['total_sales'])
            ];
        }
        echo json_encode(['data' => $data]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to execute query: ' . $conn->error]);
    }
}

// Function to calculate predicted demand using moving average
function calculatePredictedDemand($salesData, $window = 7) {
    $predictedDemand = [];
    $count = count($salesData);

    for ($i = 0; $i < $count; $i++) {
        if ($i < $window - 1) {
            $predictedDemand[] = null; // Not enough data to calculate
            continue;
        }
        $windowData = array_slice($salesData, $i - $window + 1, $window);
        $average = array_sum($windowData) / $window;
        $predictedDemand[] = round($average, 2);
    }

    return $predictedDemand;
}

// Function to generate future dates based on the last actual date
function generateFutureDates($lastDateStr, $daysAhead) {
    $dates = [];
    $lastDate = new DateTime($lastDateStr);
    for ($i = 1; $i <= $daysAhead; $i++) {
        $futureDate = clone $lastDate;
        $futureDate->modify("+$i day");
        // Format as 'YYYY-MM-DD'
        $dates[] = $futureDate->format('Y-m-d');
    }
    return $dates;
}

// Function to fetch sales and forecast data for a specific product
function getProductSalesForecast($conn, $product_id, $channel = null) {
    // Fetch actual sales data
    $actualSales = [];
    $labels = [];

    // Prepare the sales SQL query
    if ($channel) {
        $salesSql = "
            SELECT 
                DATE(s.sale_date) AS sale_date, 
                SUM(s.quantity) AS daily_sales
            FROM sales s
            WHERE s.product_id = ?
              AND s.channel = ?
            GROUP BY DATE(s.sale_date)
            ORDER BY DATE(s.sale_date) ASC
            LIMIT 30
        ";
    } else {
        $salesSql = "
            SELECT 
                DATE(s.sale_date) AS sale_date, 
                SUM(s.quantity) AS daily_sales
            FROM sales s
            WHERE s.product_id = ?
            GROUP BY DATE(s.sale_date)
            ORDER BY DATE(s.sale_date) ASC
            LIMIT 30
        ";
    }

    // Prepare and execute the statement
    if ($channel) {
        $stmt = $conn->prepare($salesSql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("is", $product_id, $channel);
    } else {
        $stmt = $conn->prepare($salesSql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("i", $product_id);
    }

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to execute statement: ' . $stmt->error]);
        exit;
    }

    $result = $stmt->get_result();

    $salesData = []; // To store daily sales for moving average
    $lastActualDate = null;

    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['sale_date'];
        $actualSales[] = intval($row['daily_sales']);
        $salesData[] = intval($row['daily_sales']);
        $lastActualDate = $row['sale_date'];
    }

    $stmt->close();

    // Calculate predicted demand using moving average
    $predictedDemand = calculatePredictedDemand($salesData);

    // Generate future dates (e.g., next 7 days)
    $daysAhead = 7;
    if ($lastActualDate) {
        $futureDates = generateFutureDates($lastActualDate, $daysAhead);
        $labels = array_merge($labels, $futureDates);

        // Use the last predicted demand value for future predictions
        $lastPredictedValue = end($predictedDemand) !== false ? end($predictedDemand) : 0;

        // Append predicted demand for future dates
        for ($i = 0; $i < $daysAhead; $i++) {
            $predictedDemand[] = $lastPredictedValue;
        }
    } else {
        // If no sales data exists, generate future dates from today
        $futureDates = generateFutureDates(date('Y-m-d'), $daysAhead);
        $labels = $futureDates;
        $actualSales = array_fill(0, $daysAhead, 0);
        $predictedDemand = array_fill(0, $daysAhead, 0);
    }

    // Return the data
    echo json_encode([
        'labels' => $labels,
        'actual_sales' => $actualSales,
        'predicted_demand' => $predictedDemand
    ]);
}

// Function to fetch inventory table data
function getInventoryTableData($conn, $tab = 'all-inventory') {
    $data = [];

    // Determine the channel condition based on the 'tab' parameter
    $channel = null;
    switch ($tab) {
        case 'physical-store':
            $channel = 'physical_store';
            break;
        case 'shopee':
            $channel = 'shopee';
            break;
        case 'tiktok':
            $channel = 'tiktok';
            break;
        default:
            $channel = null; // All channels
            break;
    }

    // Build the main SQL query
    if ($channel) {
        $sql = "
            SELECT 
                p.product_id,
                p.name AS product_name,
                COALESCE(SUM(i.quantity), 0) AS total_stock
            FROM products p
            JOIN product_variants pv ON p.product_id = pv.product_id
            LEFT JOIN inventory i ON pv.variant_id = i.variant_id
            WHERE i.channel = ?
            GROUP BY p.product_id
        ";
    } else {
        $sql = "
            SELECT 
                p.product_id,
                p.name AS product_name,
                COALESCE(SUM(i.quantity), 0) AS total_stock
            FROM products p
            JOIN product_variants pv ON p.product_id = pv.product_id
            LEFT JOIN inventory i ON pv.variant_id = i.variant_id
            GROUP BY p.product_id
        ";
    }

    // Prepare and execute the statement
    if ($channel) {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("s", $channel);
    } else {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
            exit;
        }
    }

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to execute statement: ' . $stmt->error]);
        exit;
    }

    $result = $stmt->get_result();

    // Helper function to calculate predicted demand for a product
    function calculatePredictedDemandForProduct($conn, $productId, $channel = null) {
        if ($channel) {
            $sql = "
                SELECT 
                    SUM(s.quantity) AS total_sales
                FROM sales s
                WHERE s.product_id = ?
                  AND s.channel = ?
                  AND s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ";
        } else {
            $sql = "
                SELECT 
                    SUM(s.quantity) AS total_sales
                FROM sales s
                WHERE s.product_id = ?
                  AND s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ";
        }

        if ($channel) {
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                return 0;
            }
            $stmt->bind_param("is", $productId, $channel);
        } else {
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                return 0;
            }
            $stmt->bind_param("i", $productId);
        }

        if (!$stmt->execute()) {
            $stmt->close();
            return 0;
        }

        $result = $stmt->get_result();
        $predictedDemand = 0;

        if ($row = $result->fetch_assoc()) {
            $total_sales = intval($row['total_sales']);
            // Calculate average daily sales over the last 7 days
            $predictedDemand = round($total_sales / 7, 2);
        }

        $stmt->close();
        return $predictedDemand;
    }

    // Iterate through each product and calculate predicted demand
    while ($row = $result->fetch_assoc()) {
        $productId = $row['product_id'];

        // Calculate predicted demand based on channel (if applicable)
        $predictedDemand = null;
        if ($channel) {
            $predictedDemand = calculatePredictedDemandForProduct($conn, $productId, $channel);
        } else {
            $predictedDemand = calculatePredictedDemandForProduct($conn, $productId);
        }

        // Add predicted demand to the row
        $row['predicted_demand'] = $predictedDemand;
        $data[] = $row;
    }

    $stmt->close();

    // Return table data as JSON
    echo json_encode($data);
}

// Main Logic: Determine which functionality to execute based on GET parameters
if (isset($_GET['type'])) {
    // Handle aggregated sales data for charts
    $type = $_GET['type'];
    getAggregatedSalesData($conn, $type);
} elseif (isset($_GET['product_id'])) {
    // Handle product-specific sales and forecast data
    $product_id = intval($_GET['product_id']);
    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'all-inventory';

    // Determine the channel based on the tab
    $channel = null;
    switch ($tab) {
        case 'physical-store':
            $channel = 'physical_store';
            break;
        case 'shopee':
            $channel = 'shopee';
            break;
        case 'tiktok':
            $channel = 'tiktok';
            break;
        default:
            $channel = null; // All channels
            break;
    }

    getProductSalesForecast($conn, $product_id, $channel);
} else {
    // Handle inventory table data
    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'all-inventory';
    getInventoryTableData($conn, $tab);
}

// Close the database connection
$conn->close();
?>
