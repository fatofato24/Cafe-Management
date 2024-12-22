<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}

require_once('database/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['id'] ?? null;
    $delivered_quantity = $_POST['delivered_quantity'] ?? null;
    $status = $_POST['status'] ?? null;

    if (empty($order_id) || $delivered_quantity === null || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    if (!is_numeric($delivered_quantity) || $delivered_quantity < 0) {
        echo json_encode(['success' => false, 'message' => 'Delivered quantity must be a positive number.']);
        exit;
    }

    try {
        $conn->beginTransaction();

        // Check if the order exists
        $stmt = $conn->prepare("SELECT quantity_received FROM order_product WHERE id = :order_id");
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found.']);
            $conn->rollBack();
            exit;
        }

        $current_quantity_received = $order['quantity_received'];
        $new_quantity_received = $current_quantity_received + $delivered_quantity;

        // Update the order_product table
        $updateStmt = $conn->prepare(
            "UPDATE order_product 
            SET quantity_received = :new_quantity_received, status = :status, date_updated = NOW() 
            WHERE id = :order_id"
        );
        $updateStmt->bindParam(':new_quantity_received', $new_quantity_received, PDO::PARAM_INT);
        $updateStmt->bindParam(':status', $status, PDO::PARAM_STR);
        $updateStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $updateStmt->execute();

        // Insert into order_product_history
        $historyStmt = $conn->prepare(
            "INSERT INTO order_product_history (order_product_id, qty_received, date_received, date_updated) 
            VALUES (:order_id, :delivered_quantity, NOW(), NOW())"
        );
        $historyStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $historyStmt->bindParam(':delivered_quantity', $delivered_quantity, PDO::PARAM_INT);
        $historyStmt->execute();

        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Order updated successfully.']);
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Error updating order: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Server error occurred while updating the order.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
