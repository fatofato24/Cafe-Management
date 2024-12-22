<?php
// Ensure proper session and permissions are in place
session_start();
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in']);
    exit;
}

require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $orderId = $_POST['id'];
    $deliveredQuantity = $_POST['delivered_quantity'];
    $status = isset($_POST['status']) ? $_POST['status'] : null;

    if (!$status) {
        echo json_encode(['success' => false, 'message' => 'Status is missing']);
        exit;
    }
    
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
                quantity_remaining = quantity_ordered - (quantity_received + :delivered_quantity),
                status = :status
            WHERE id = :id"
        );
        $stmt->execute(['delivered_quantity' => $deliveredQuantity, 'status' => $status, 'id' => $orderId]);

        // Get the product ID from the order_product table
        $stmtProduct = $conn->prepare("SELECT product FROM order_product WHERE id = :id");
        $stmtProduct->execute(['id' => $orderId]);
        $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);
        $productId = $product['product'];

        // Update the stock in the products table
        $stmtUpdateStock = $conn->prepare(
            "UPDATE products 
            SET stock = stock + :quantity_received
            WHERE id = :product_id"
        );
        $stmtUpdateStock->execute(['quantity_received' => $deliveredQuantity, 'product_id' => $productId]);

        // Update the stock in the stocks table
        // Check if there's an existing record in the stocks table
        $stmtStockCheck = $conn->prepare("SELECT id, quantity FROM stocks WHERE product_id = :product_id ORDER BY updated_at DESC LIMIT 1");
        $stmtStockCheck->execute(['product_id' => $productId]);
        $stock = $stmtStockCheck->fetch(PDO::FETCH_ASSOC);

        if ($stock) {
            // If a record exists, update the quantity
            $stmtUpdateStocks = $conn->prepare(
                "UPDATE stocks 
                SET quantity = quantity + :delivered_quantity, updated_at = :updated_at
                WHERE id = :stock_id"
            );
            $stmtUpdateStocks->execute([
                'delivered_quantity' => $deliveredQuantity,
                'updated_at' => date('Y-m-d H:i:s'),
                'stock_id' => $stock['id']
            ]);
        } else {
            // If no record exists, insert a new one into the stocks table
            $stmtInsertStock = $conn->prepare(
                "INSERT INTO stocks (product_id, quantity, created_at, updated_at)
                VALUES (:product_id, :quantity, :created_at, :updated_at)"
            );
            $stmtInsertStock->execute([
                'product_id' => $productId,
                'quantity' => $deliveredQuantity,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Get the current timestamp for history entry
        $currentDateTime = date('Y-m-d H:i:s');

        // Insert into the order_product_history table
        $stmtHistory = $conn->prepare(
            "INSERT INTO order_product_history (order_product_id, qty_received, date_received, date_updated)
            VALUES (:order_product_id, :qty_received, :date_received, :date_updated)"
        );
        $stmtHistory->execute([
            'order_product_id' => $orderId, // Same ID for the order_product
            'qty_received' => $deliveredQuantity, // The delivered quantity
            'date_received' => $currentDateTime, // Date of received quantity
            'date_updated' => $currentDateTime  // Date of the update
        ]);

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error updating order: ' . $e->getMessage()]);
    }
}
?>
