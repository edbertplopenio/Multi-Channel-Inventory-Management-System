<?php
include '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    $itemId = isset($data['item_id']) ? intval($data['item_id']) : 0;
    $type = isset($data['type']) ? $data['type'] : '';

    if ($itemId <= 0 || empty($type)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid item ID or type']);
        exit;
    }

    // Process based on delete type
    if ($type === 'variant') {
        // Step 1: Retrieve product_id for the variant to check for other variants
        $productIdQuery = "SELECT product_id FROM product_variants WHERE variant_id = ?";
        $stmt = $conn->prepare($productIdQuery);
        $stmt->bind_param('i', $itemId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Variant not found!']);
            $stmt->close();
            $conn->close();
            exit;
        }

        $productId = $result->fetch_assoc()['product_id'];
        $stmt->close();

        // Step 2: Delete the specified variant
        $deleteVariantQuery = "DELETE FROM product_variants WHERE variant_id = ?";
        $stmt = $conn->prepare($deleteVariantQuery);
        $stmt->bind_param('i', $itemId);

        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete variant: ' . $stmt->error]);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();

        // Step 3: Check if other variants exist for the same product
        $checkOtherVariantsQuery = "SELECT variant_id FROM product_variants WHERE product_id = ?";
        $stmt = $conn->prepare($checkOtherVariantsQuery);
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // No other variants found, so delete the product itself
            $deleteProductQuery = "DELETE FROM products WHERE product_id = ?";
            $stmt = $conn->prepare($deleteProductQuery);
            $stmt->bind_param('i', $productId);

            if (!$stmt->execute()) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete product: ' . $stmt->error]);
                $stmt->close();
                $conn->close();
                exit;
            }
            $stmt->close();
            echo json_encode(['status' => 'success', 'message' => 'Variant and product deleted successfully!']);
        } else {
            // Other variants exist, so only the variant was deleted
            $stmt->close();
            echo json_encode(['status' => 'success', 'message' => 'Variant deleted successfully!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid deletion type']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
