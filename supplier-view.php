<?php 
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}

// Fetch suppliers directly from the database using PDO
require_once('database/connection.php');

try {
    $query = "SELECT * FROM suppliers"; // Fetch all suppliers
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Suppliers</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
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
                            <h1 class="section_header"><i class="fa fa-list"></i> List of Suppliers</h1>
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Supplier Name</th>
                                        <th>Location</th>
                                        <th>Email</th>
                                        <th>Products</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($suppliers) > 0): ?>
                                        <?php foreach ($suppliers as $index => $supplier): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($supplier['supplier_name']) ?></td>
                                                <td><?= htmlspecialchars($supplier['supplier_location']) ?></td>
                                                <td><?= htmlspecialchars($supplier['email']) ?></td>
                                                <td>
                                                    <?php
                                                    // Fetch products linked to this supplier
                                                    $product_list = '-';
                                                    $sid = $supplier['id'];
                                                    $stmt = $conn->prepare(
                                                        "SELECT products.product_name 
                                                         FROM products 
                                                         JOIN productsuppliers ON products.id = productsuppliers.product 
                                                         WHERE productsuppliers.supplier = :supplier_id"
                                                    );
                                                    $stmt->bindParam(':supplier_id', $sid, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                    if ($rows) {
                                                        $product_arr = array_column($rows, 'product_name');
                                                        $product_list = '<li>' . implode("</li><li>", $product_arr) . '</li>';
                                                    }
                                                    ?>
                                                    <ul><?= $product_list ?></ul>
                                                </td>
                                                <td>
                                                    <?php
                                                    // Fetch the name of the user who created this supplier
                                                    $uid = $supplier['created_by'];
                                                    $stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = :user_id");
                                                    $stmt->bindParam(':user_id', $uid, PDO::PARAM_INT);
                                                    $stmt->execute();
                                                    $user_row = $stmt->fetch(PDO::FETCH_ASSOC);
                                                    $created_by_name = $user_row ? htmlspecialchars($user_row['first_name'] . ' ' . $user_row['last_name']) : '-';
                                                    ?>
                                                    <?= $created_by_name ?>
                                                </td>
                                                <td><?= date('M d, Y @ h:i:s A', strtotime($supplier['created_at'])) ?></td>
                                                <td><?= date('M d, Y @ h:i:s A', strtotime($supplier['updated_at'])) ?></td>
                                                <td>
                                                    <a href="#" class="updateSupplier" data-supplierid="<?= $supplier['id'] ?>"
                                                       data-suppliername="<?= htmlspecialchars($supplier['supplier_name']) ?>" 
                                                       data-location="<?= htmlspecialchars($supplier['supplier_location']) ?>"
                                                       data-email="<?= htmlspecialchars($supplier['email']) ?>"> 
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
                                                    <a href="#" class="deleteSupplier" data-supplierid="<?= $supplier['id'] ?>"
                                                       data-suppliername="<?= htmlspecialchars($supplier['supplier_name']) ?>">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9">No suppliers found.</td>
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
    <script src="js/jquery/jquery-3.7.1.min.js"></script>
    <script>
    // Handle Edit (Update Supplier)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.updateSupplier')) {
            e.preventDefault();
            const button = e.target.closest('.updateSupplier');
            const supplierId = button.dataset.supplierid;
            const supplierName = button.dataset.suppliername;
            const location = button.dataset.location;
            const email = button.dataset.email;

            // Prompt to edit supplier details
            const newSupplierName = prompt("Enter Supplier Name:", supplierName);
            const newLocation = prompt("Enter Location:", location);
            const newEmail = prompt("Enter Email:", email);

            if (newSupplierName && newLocation && newEmail) {
                $.post('database/update-supplier.php', {
                    supplier_id: supplierId,
                    supplier_name: newSupplierName,
                    location: newLocation,
                    email: newEmail
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

    // Handle Delete Supplier
    document.addEventListener('click', function(e) {
        if (e.target.closest('.deleteSupplier')) {
            e.preventDefault();
            const button = e.target.closest('.deleteSupplier');
            const supplierId = button.dataset.supplierid;
            const supplierName = button.dataset.suppliername;

            if (confirm(`Are you sure you want to delete ${supplierName}?`)) {
                $.post('database/delete-supplier.php', { supplier_id: supplierId }, function(response) {
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
