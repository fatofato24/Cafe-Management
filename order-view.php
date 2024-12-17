<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}

// Include database connection
require_once('database/connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Purchase Orders</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .batch-container {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            background-color: #fff;
        }
        .batch-title {
            font-weight: bold;
            font-size: 16px;
            color: #e91e63;
            border-bottom: 2px solid #e91e63;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .update-button {
            margin-top: 10px;
            display: inline-block;
            padding: 5px 10px;
            background-color: #e91e63;
            color: #fff;
            border-radius: 3px;
            text-decoration: none;
            text-align: center;
        }
        .po-badge-pending{
            padding:4px 6px;
            border: 1px solid green;
            background:rgb(207, 74, 59); /* Red for Pending */
            color: #fff;
        }
        .po-badge-completed{
            padding:4px 6px;
            border: 1px solid green;
            background: #b5ebb5; /* Green for Completed */
            color: #333;
        }
    </style>
</head>
<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php'); ?>
        <?php include('partials/app-topnav.php'); ?>

        <div class="dashboard_content_container">
            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <h1 class="section_header"><i class="fa fa-list"></i> List of Purchase Orders</h1>
                    <?php
                    // Fetch orders from the database
                    $stmt = $conn->prepare(
                        "SELECT 
                            order_product.batch,       
                            products.product_name,
                            order_product.quantity_ordered,
                            users.first_name, 
                            users.last_name, 
                            suppliers.supplier_name, 
                            order_product.status, 
                            order_product.created_at, 
                            order_product.created_by    
                        FROM order_product
                        JOIN suppliers ON order_product.supplier = suppliers.id
                        JOIN products ON order_product.product = products.id
                        JOIN users ON order_product.created_by = users.id
                        ORDER BY order_product.created_at DESC"
                    );
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Group rows by batch
                    $data = [];
                    foreach ($rows as $row) {
                        $data[$row['batch']][] = $row;
                    }
                    ?>

                    <?php if (!empty($data)): ?>
                        <?php foreach ($data as $batch_id => $batch_po): ?>
                            <div class="batch-container">
                                <div class="batch-title">Batch #: <?= htmlspecialchars($batch_id) ?></div>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>QTY Ordered</th>
                                            <th>Supplier</th>
                                            <th>Status</th>
                                            <th>Ordered By</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($batch_po as $index => $order): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                                <td><?= htmlspecialchars($order['quantity_ordered']) ?></td>
                                                <td><?= htmlspecialchars($order['supplier_name']) ?></td>
                                                <td>
                                                    <?php 
                                                    $status_class = '';
                                                    if (strtolower($order['status']) == 'pending') {
                                                        $status_class = 'po-badge-pending';
                                                    } elseif (strtolower($order['status']) == 'completed') {
                                                        $status_class = 'po-badge-completed';
                                                    }
                                                    ?>
                                                    <span class="po-badge <?= $status_class ?>"><?= htmlspecialchars($order['status']) ?></span>
                                                </td>
                                                <td><?= htmlspecialchars($order['created_by']) ?></td>
                                                <td><?= date('M d, Y @ h:i A', strtotime($order['created_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <a href="#" class="update-button">Update</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No purchase orders found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/jquery/jquery-3.7.1.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
