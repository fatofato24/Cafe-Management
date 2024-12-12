<?php 
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}

// Fetch products directly from the database using PDO
require_once('database/connection.php');

try {
    $query = "SELECT * FROM products"; // Fetch all products
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Product Management</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
    <style>
        /* Your CSS styles */
    </style>
</head>
<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php'); ?>
        <?php include('partials/app-topnav.php'); ?>
        <div class="dashboard_content_container" id="dashboard_content_container">
            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <div class="row">
                        <div class="column">
                            <h1 class="section_header"><i class="fa fa-list"></i> List of Products</h1>
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Product Name</th>
                                        <th>Description</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($products) > 0): ?>
                                        <?php foreach ($products as $index => $product): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><img class="productImages" src="uploads/products/<?= $product['img'] ?>" alt="" style="max-width: 100px;"></td>
                                                <td><?= $product['product_name'] ?></td>
                                                <td><?= $product['description'] ?></td>
                                                <td><?= $product['created_by'] ?></td>
                                                <td><?= date('M d, Y @ h:i:s A', strtotime($product['created_at'])) ?></td>
                                                <td><?= date('M d, Y @ h:i:s A', strtotime($product['updated_at'])) ?></td>
                                                <td>
                                                    <a href="#" class="updateProduct" data-productid="<?= $product['id'] ?>" 
                                                       data-productname="<?= $product['product_name'] ?>" 
                                                       data-description="<?= $product['description'] ?>"
                                                       data-productimage="<?= $product['img'] ?>">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
                                                    <a href="#" class="deleteProduct" data-productid="<?= $product['id'] ?>" 
                                                       data-productname="<?= $product['product_name'] ?>">
                                                        <i class=" fa fa-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8">No products found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>   
            </div>
        </div>
    </div>
    <script src="js/script.js"></script>
    <script src="js/jquery/jquery-3.7.1.min.js"></script>
    <script>
    // Handle Edit (Update Product)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.updateProduct')) {
            e.preventDefault();
            const button = e.target.closest('.updateProduct');
            const productId = button.dataset.productid;
            const productName = button.dataset.productname;
            const productDescription = button.dataset.description;
            const productImage = button.dataset.productimage;

            // Prompt to edit product details
            const newProductName = prompt("Enter Product Name:", productName);
            const newProductDescription = prompt("Enter Product Description:", productDescription);
            const newProductImage = prompt("Enter new image name (optional):", productImage);

            if (newProductName && newProductDescription) {
                $.post('database/update-product.php', {
                    product_id: productId,
                    product_name: newProductName,
                    description: newProductDescription,
                    product_image: newProductImage // Pass the new image name
                }, function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }, 'json').fail(function() {
                    alert('Error processing request. Please try again.');
                });
            }
        }
    });

    // Handle Delete Product
    document.addEventListener('click', function(e) {
        if (e.target.closest('.deleteProduct')) {
            e.preventDefault();
            const button = e.target.closest('.deleteProduct');
            const productId = button.dataset.productid;
            const productName = button.dataset.productname;

            if (confirm(`Are you sure you want to delete ${productName}?`)) {
                $.post('database/delete-product.php', { product_id: productId }, function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }, 'json').fail(function() {
                    alert('Error processing request. Please try again.');
                });
            }
        }
    });
    </script>
</body>
</html>
