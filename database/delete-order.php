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

    if ($orderId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
        exit;
    }

    try {
        // Check if the order exists
        $stmt = $conn->prepare("SELECT id FROM order_product WHERE id = :id");
        $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Order not found.']);
            exit;
        }

        // Delete the order
        $deleteStmt = $conn->prepare("DELETE FROM order_product WHERE id = :id");
        $deleteStmt->bindParam(':id', $orderId, PDO::PARAM_INT);

        if ($deleteStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Order deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete order.']);
        }
    } catch (Exception $e) {
        error_log("Error deleting order: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error occurred while deleting the order.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
