<?php
require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $description = $_POST['description'];
    $newImageName = $_POST['product_image'];

    // Handle image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $imageTmp = $_FILES['product_image']['tmp_name'];
        $imageName = $_FILES['product_image']['name'];
        $imageSize = $_FILES['product_image']['size'];
        
        // Validate the file size (optional)
        if ($imageSize > 2000000) {
            die("Image size is too large. Max size allowed is 2MB.");
        }

        // Generate a new name for the image to avoid overwriting
        $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
        $newImageName = uniqid() . '.' . $imageExt;

        // Define the upload directory and move the file
        $uploadDir = 'uploads/products/';
        $uploadFile = $uploadDir . $newImageName;
        if (!move_uploaded_file($imageTmp, $uploadFile)) {
            die("Error uploading image.");
        }
    }

    // Update the product details
    try {
        $query = "UPDATE products SET product_name = :product_name, description = :description, img = :img WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $productId);
        $stmt->bindParam(':product_name', $productName);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':img', $newImageName);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update product']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
