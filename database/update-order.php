<?php
require_once('connection.php');

// Fetch POST data
$orderId = $_POST['id'];
$receivedQuantity = $_POST['received_quantity'];

// Get the order details
$stmt = $conn->prepare("SELECT quantity_ordered FROM order_product WHERE id = :id");
$stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if ($order) {
    $quantityOrdered = $order['quantity_ordered'];
    // Determine the status based on quantity received
    $status = 'Pending'; // Default status
    if ($receivedQuantity > 0 && $receivedQuantity < $quantityOrdered) {
        $status = 'Incomplete';
    } elseif ($receivedQuantity == $quantityOrdered) {
        $status = 'Completed';
    }

    // Update the order
    $updateStmt = $conn->prepare("UPDATE order_product SET quantity_received = :received_quantity, status = :status WHERE id = :id");
    $updateStmt->bindParam(':received_quantity', $receivedQuantity, PDO::PARAM_INT);
    $updateStmt->bindParam(':status', $status, PDO::PARAM_STR);
    $updateStmt->bindParam(':id', $orderId, PDO::PARAM_INT);
    $updateStmt->execute();

    // Return a success response
    echo json_encode(['success' => true, 'message' => 'Order updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Order not found.']);
}
?>
