<?php
// Ensure proper session and permissions are in place
session_start();
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in']);
    exit;
}

require_once('database/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $orderId = $_POST['id'];
    $deliveredQuantity = $_POST['delivered_quantity'];
    $status = $_POST['status'];

    // Validate delivered quantity
    if ($deliveredQuantity < 0) {
        echo json_encode(['success' => false, 'message' => 'Delivered quantity cannot be negative']);
        exit;
    }

    try {
        // Begin transaction
        $conn->beginTransaction();

        // Update the order_product table with new delivered quantity
        $stmt = $conn->prepare(
            "UPDATE order_product SET 
                quantity_received = quantity_received + :delivered_quantity,
                status = :status,
                date_updated = NOW()
            WHERE id = :id"
        );
        $stmt->execute(['delivered_quantity' => $deliveredQuantity, 'status' => $status, 'id' => $orderId]);

        // Optionally log history in order_product_history table if needed

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error updating order: ' . $e->getMessage()]);
    }
}
?>
