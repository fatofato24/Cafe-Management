<?php
session_start();

// Check if user is logged in and has a valid ID
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    die("User not logged in or invalid session.");
}

$user_id = $_SESSION['user']['id'];  // User ID from session

// Product form data
$product_name = $_POST['product_name'];
$description = $_POST['description'];
$created_at = date('Y-m-d H:i:s');
$updated_at = date('Y-m-d H:i:s');
$image_name = null;

// Handle file upload
if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "../uploads/products/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $file_data = $_FILES['img'];
    $file_name = $file_data['name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $file_name = 'product_' . time() . '.' . $file_ext;

    $check = getimagesize($file_data['tmp_name']);
    if ($check) {
        if (move_uploaded_file($_FILES['img']['tmp_name'], $target_dir . $file_name)) {
            $image_name = $file_name;
        }
    }
}

// Insert product into `products` table
try {
    include('connection.php');

    // Make sure created_by is valid
    if (!is_numeric($user_id)) {
        throw new Exception("Invalid user ID.");
    }

    // Insert into products table
    $sql = "INSERT INTO products (product_name, description, img, created_by, created_at, updated_at) 
            VALUES (:product_name, :description, :img, :created_by, :created_at, :updated_at)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_name', $product_name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':img', $image_name); 
    $stmt->bindParam(':created_by', $user_id);  
    $stmt->bindParam(':created_at', $created_at);
    $stmt->bindParam(':updated_at', $updated_at);

    $stmt->execute();

    // Get the inserted product ID
    $product_id = $conn->lastInsertId();

    // Insert the selected suppliers into the `productsuppliers` table
   // Insert the selected suppliers into the `productsuppliers` table
if (isset($_POST['suppliers']) && !empty($_POST['suppliers'])) {
    $suppliers = $_POST['suppliers']; // Array of selected supplier IDs

    foreach ($suppliers as $supplier_id) {
        // Ensure supplier_id is valid
        if (!is_numeric($supplier_id)) {
            continue; // Skip invalid supplier IDs
        }

        // Insert each supplier-product relationship into `productsuppliers`
        $sql_supplier = "INSERT INTO productsuppliers (product, supplier, created_at, updated_at) 
                         VALUES (:product_id, :supplier_id, :created_at, :updated_at)";
        $stmt_supplier = $conn->prepare($sql_supplier);

        $stmt_supplier->bindParam(':product_id', $product_id);
        $stmt_supplier->bindParam(':supplier_id', $supplier_id);
        $stmt_supplier->bindParam(':created_at', $created_at);
        $stmt_supplier->bindParam(':updated_at', $updated_at);

        // Check for errors in the supplier insert
        if (!$stmt_supplier->execute()) {
            $error = $stmt_supplier->errorInfo();
            echo "Error inserting supplier for product: " . $error[2];
        }
    }
}


    // Response
    $response = [
        'success' => true,
        'message' => 'Product successfully added!'
    ];

} catch (PDOException $e) {
    // Handle error
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Store response and redirect
$_SESSION['response'] = $response;
header('location: ../product-add.php');
exit;
?>