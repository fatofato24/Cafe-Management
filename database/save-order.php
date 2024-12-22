<?php
include('connection.php');

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
    // For each product, we will get the supplier quantities from the POST data
    foreach ($supplier_quantities as $supplier_id => $quantity) {

        // Ensure there is a valid supplier-product mapping in the productsuppliers table
        $stmt = $conn->prepare("
            SELECT 1 FROM productsuppliers WHERE product = :product_id AND supplier = :supplier_id
        ");
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':supplier_id', $supplier_id, PDO::PARAM_INT);
        $stmt->execute();

        // Check if the supplier exists for the product
        if ($stmt->rowCount() > 0 && $quantity > 0) {
            // Add each valid product-supplier combination with the quantity to the final array
            $post_data_arr[] = [
                'product_id' => $product_id,
                'supplier_id' => $supplier_id,
                'quantity_ordered' => $quantity
            ];
        } else {
            // Optional: Handle error if no valid supplier-product mapping is found
            echo "No valid supplier found for Product ID $product_id and Supplier ID $supplier_id.<br>";
        }
    }
}

// Check the data array (for debugging purposes)
echo '<pre>';
print_r($post_data_arr);
echo '</pre>';

// Initialize success flag and message
$success = false;
$message = '';

try {
    // Insert each product-supplier combination with a unique batch number
    foreach ($post_data_arr as $data) {
        // Generate a unique batch number for each product-supplier pair
        $batch = time() . '_' . uniqid();  // Use timestamp and a unique ID for batch
        
        // Prepare data for the database insert
        $values = [
            'supplier' => $data['supplier_id'],
            'product' => $data['product_id'],
            'quantity_ordered' => $data['quantity_ordered'],
            'quantity_received' => 0, // Default quantity_received as 0
            'status' => 'PENDING',  // Default status as ordered
            'batch' => $batch,  // Assign the unique batch
            'created_by' => $_SESSION['user']['id'],  // User ID of the person placing the order
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Insert SQL query
        $sql = "INSERT INTO order_product 
                (supplier, product, quantity_ordered, quantity_received, status, batch, created_by, updated_at, created_at)
                VALUES 
                (:supplier, :product, :quantity_ordered, :quantity_received, :status, :batch, :created_by, :updated_at, :created_at)
                ";
        
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
