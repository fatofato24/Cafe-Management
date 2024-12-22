<?php
// get-delivery-history.php

// Include database connection
require_once('connection.php');

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch the delivery history from the order_product_history table
    $stmt = $conn->prepare(
        "SELECT qty_received, date_received 
         FROM order_product_history 
         WHERE order_product_id = :order_id 
         ORDER BY date_received DESC"
    );
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    $deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($deliveries) {
        echo json_encode(['success' => true, 'deliveries' => $deliveries]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No delivery history found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Order ID is required.']);
}
