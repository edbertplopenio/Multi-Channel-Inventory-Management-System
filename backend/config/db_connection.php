<?php

require_once __DIR__ . '../../../vendor/autoload.php';  // This is correct if the file is in the same directory as the vendor folder

// Function to create a database connection
function createDatabaseConnection($servername, $username, $password, $dbname) {
    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}

// Create a database connection
$conn = createDatabaseConnection("localhost", "root", "", "mios");

// Now, you can use $conn for your database operations

?>
