<?php
require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $status = $_POST['status'];

    try {
        $stmt = $conn->prepare("UPDATE order_product 
                                JOIN products ON order_product.product = products.id
                                SET products.product_name = :product_name, 
                                    order_product.quantity_ordered = :quantity, 
                                    order_product.status = :status 
                                WHERE order_product.id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
