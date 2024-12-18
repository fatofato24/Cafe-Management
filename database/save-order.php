<?php
session_start();
// Get POST data
$post_data = $_POST;

// Check if products and supplier_quantity are set
if (!isset($post_data['products']) || !isset($post_data['supplier_quantity'])) {
    die("Error: Missing 'products' or 'quantity' data.");
}

$products = $post_data['products'];  // Array of product IDs
$supplier_quantities = $post_data['supplier_quantity'];  // Associative array of supplier_id => quantity

// Initialize an array to hold the final data for insertion
$post_data_arr = [];

// Loop through each product and its corresponding supplier(s)
foreach ($products as $key => $product_id) {
    // Loop through the supplier quantities
    foreach ($supplier_quantities as $supplier_id => $quantity) {
        // Ensure there is a positive quantity
        if ($quantity > 0) {
            // Add each product-supplier combination with the quantity to the final array
            $post_data_arr[] = [
                'product_id' => $product_id,
                'supplier_id' => $supplier_id,
                'quantity_ordered' => $quantity
            ];
        }
    }
}

// Check the data array (for debugging purposes)
echo '<pre>';
print_r($post_data_arr);
echo '</pre>';

// Include database connection
include('connection.php');

// Initialize success flag and message
$success = false;
$message = '';

// Try to insert the order into the database
try {
    $batch = time();  // Use timestamp for batch grouping
    foreach ($post_data_arr as $data) {
        // Prepare data for the database insert
        $values = [
            'supplier' => $data['supplier_id'],
            'product' => $data['product_id'],
            'quantity_ordered' => $data['quantity_ordered'],
            'status' => 'PENDING',  // Default status as ordered
            'batch' => $batch,  // Assign the batch
            'created_by' => $_SESSION['user']['id'],  // User ID of the person placing the order
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Insert SQL query
        $sql = "INSERT INTO order_product 
                (supplier, product, quantity_ordered, status, batch, created_by, updated_at, created_at)
                VALUES 
                (:supplier, :product, :quantity_ordered, :status, :batch, :created_by, :updated_at, :created_at)";
        
        // Prepare and execute the query
        $stmt = $conn->prepare($sql);
        $stmt->execute($values);
    }

    // If everything goes well, set success message
    $success = true;
} catch (Exception $e) {
    // If an error occurs, set the error message
    $message = $e->getMessage();
}

// Set the session response for feedback
$_SESSION['response'] = [
    'message' => $success ? 'Order saved successfully!' : $message,
    'success' => $success,
];

// Redirect back to the order page
header('location: ../product-order.php');
exit;

?>
