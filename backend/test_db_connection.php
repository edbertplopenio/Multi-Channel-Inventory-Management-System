<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Database Connection</title>
</head>
<body>
    <!-- Form with a button to check the connection -->
    <form method="POST">
        <button type="submit" name="check_connection">Check Database Connection</button>
    </form>

    <?php
    if (isset($_POST['check_connection'])) {
        // Include the database connection file
        include('config/db_connection.php');

        // Test the connection
        if ($conn) {
            echo "<p>Database connection successful!</p>";
        } else {
            echo "<p>Database connection failed: " . mysqli_connect_error() . "</p>";
        }

        // Close the database connection
        mysqli_close($conn);
    }
    ?>
</body>
</html>
