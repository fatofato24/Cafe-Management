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
    echo ("File upload error: " . $_FILES['img']['error']);
    $target_dir = "../uploads/products/";
    if (!is_dir('../uploads/products/')) {
        mkdir('../uploads/products/', 0755, true);
    }
    $file_data = $_FILES['img'];
    $file_name = $file_data['name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $file_name = 'product_' . time() . '.' . $file_ext;

    $check = getimagesize($file_data['tmp_name']);
    if (!$check) {
        echo "Invalid image file.";
        exit;
    }
    if ($check) {
        if (move_uploaded_file($_FILES['img']['tmp_name'], $target_dir . $file_name)) {
            $image_name = $file_name;
        } else {
            $image_name = null;
        }
    }
}


// Prepare SQL query
try {
    include('connection.php');

    // Make sure created_by is valid
    if (!is_numeric($user_id)) {
        throw new Exception("Invalid user ID.");
    }

    // Insert into products table
    $sql = "INSERT INTO products (product_name, description,img, created_by, created_at, updated_at) 
            VALUES (:product_name, :description,:img, :created_by, :created_at, :updated_at)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_name', $product_name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':img', $image_name); 
    $stmt->bindParam(':created_by', $user_id);  // Valid user ID
    $stmt->bindParam(':created_at', $created_at);
    $stmt->bindParam(':updated_at', $updated_at);

    $stmt->execute();

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
