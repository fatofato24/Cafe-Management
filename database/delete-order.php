<?php
session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $orderId = intval($_POST['id']); // Sanitize input

    try {
        // Log the ID being passed
        error_log("Attempting to delete order with ID: $orderId");

        $stmt = $conn->prepare("DELETE FROM order_product WHERE id = :id");
        $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);

        // Execute the query and check the number of affected rows
        if ($stmt->execute()) {
            $affectedRows = $stmt->rowCount();
            error_log("Rows affected: $affectedRows");

            if ($affectedRows > 0) {
                // Successful deletion
                echo json_encode(['success' => true, 'message' => 'Order deleted successfully.']);
            } else {
                // No rows affected, meaning no such order exists
                echo json_encode(['success' => false, 'message' => 'Order not found or already deleted.']);
            }
        } else {
            // Failed to execute the query
            error_log("Failed to execute delete query for ID: $orderId");
            echo json_encode(['success' => false, 'message' => 'Failed to delete the order.']);
        }
    } catch (Exception $e) {
        // Catch and log any exceptions
        error_log("Exception occurred while deleting order: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
} else {
    // Invalid request or missing ID
    error_log("Invalid request or missing ID");
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
