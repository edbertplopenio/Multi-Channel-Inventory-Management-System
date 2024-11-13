<?php
require_once '../../backend/config/db_connection.php';

$response = ['success' => true, 'message' => ''];

// Ensure variant_id is provided
if (!isset($_POST['variant_id'])) {
    $response['success'] = false;
    $response['message'] = 'Item ID is missing.';
    echo json_encode($response);
    exit;
}

// Get form data
$variantId = $_POST['variant_id'];
$name = $_POST['name'] ?? '';
$category = $_POST['category'] ?? '';
$size = $_POST['size'] ?? '';
$color = $_POST['color'] ?? '';

// Fetch existing values to compare
$sqlFetchExisting = "
    SELECT p.name, p.category, pv.size, pv.color
    FROM product_variants pv
    JOIN products p ON pv.product_id = p.product_id
    WHERE pv.variant_id = ?
";
$stmt = $conn->prepare($sqlFetchExisting);
$stmt->bind_param("i", $variantId);
$stmt->execute();
$stmt->bind_result($existingName, $existingCategory, $existingSize, $existingColor);
$stmt->fetch();
$stmt->close();

// Check if each field has actually changed
$nameChanged = ($name !== $existingName);
$categoryChanged = ($category !== $existingCategory);
$sizeChanged = ($size !== $existingSize);
$colorChanged = ($color !== $existingColor);

// Perform duplicate checks only for changed fields

// Check for duplicate name if changed
if ($nameChanged) {
    $sqlNameCheck = "SELECT pv.variant_id FROM product_variants pv JOIN products p ON pv.product_id = p.product_id WHERE p.name = ? AND pv.variant_id != ?";
    $stmt = $conn->prepare($sqlNameCheck);
    $stmt->bind_param("si", $name, $variantId);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $response['success'] = false;
        $response['message'] = 'The product name already exists. Please choose a different name.';
        echo json_encode($response);
        exit;
    }
    $stmt->close();
}

// Check for duplicate size and color in the same category if any of those fields changed
if ($categoryChanged || $sizeChanged || $colorChanged) {
    $sqlSizeColorCheck = "
        SELECT pv.variant_id 
        FROM product_variants pv 
        JOIN products p ON pv.product_id = p.product_id 
        WHERE p.category = ? AND pv.size = ? AND pv.color = ? AND pv.variant_id != ?
    ";
    $stmt = $conn->prepare($sqlSizeColorCheck);
    $stmt->bind_param("sssi", $category, $size, $color, $variantId);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $response['success'] = false;
        $response['message'] = 'An item with the same category, size, and color already exists.';
        echo json_encode($response);
        exit;
    }
    $stmt->close();
}

// No duplicates found
$response['success'] = true;
$response['message'] = 'No duplicates found.';
echo json_encode($response);
?>
