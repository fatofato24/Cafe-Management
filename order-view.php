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
        .po-badge-incomplete{
            padding:4px 6px;
            border: 1px solid green;
            background:rgb(192, 183, 28); /* Green for Completed */
            color: #333;
        }
        .delete-button {
            padding: 6px 12px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .delete-button:hover {
            background-color: #ff0000;
        }
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            background: white;
            padding: 20px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }

        .modal-content {
            position: relative;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
        }

        .modal-background {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-top: 5px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input{
            width: 90%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        select{
            width: 90%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .form-actions {
            text-align: right;
        }

        .btn-save {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-cancel {
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-save:hover {
            background-color: #45a049;
        }

        .btn-cancel:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>

<!-- Update Modal -->
<div id="updateModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeUpdateModal()">&times;</span>
        <h3>Update Order</h3>
        <form id="updateForm" method="POST">
            <input type="hidden" id="orderId" name="id"> <!-- Hidden field for orderId -->
            <div class="form-group">
                <label for="updateProductName">Product</label>
                <input type="text" id="updateProductName" name="product_name" disabled>
            </div>
            <div class="form-group">
                <label for="updateQuantity">Quantity Ordered</label>
                <input type="number" id="updateQuantity" name="quantity_ordered" disabled>
            </div>
            <div class="form-group">
                <label for="updateReceivedQuantity">Quantity Received</label>
                <input type="number" id="updateReceivedQuantity" name="quantity_received" disabled>
            </div>
            <div class="form-group">
                <label for="updateDeliveredQuantity">Quantity Delivered</label>
                <input type="number" id="updateDeliveredQuantity" name="delivered_quantity" value="0" min="0" required>
            </div>
            <div class="form-group">
                <label for="updateStatus">Status</label>
                <select id="updateStatus" name="status">
                    <option value="Pending">Pending</option>
                    <option value="Incomplete">Incomplete</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save">Save Changes</button>
                <button type="button" class="btn-cancel" onclick="closeUpdateModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="modalBackground" class="modal-background" onclick="closeUpdateModal()"></div>

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
                        order_product.id, 
                        order_product.batch,
                        products.product_name,
                        order_product.quantity_ordered,
                        order_product.quantity_received,
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
                            <div class="batch-title">Batch: <?= $batch_id ?></div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Quantity Ordered</th>
                                        <th>Quantity Received</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($batch_po as $po): ?>
                                        <tr>
                                            <td><?= $po['product_name'] ?></td>
                                            <td><?= $po['quantity_ordered'] ?></td>
                                            <td><?= $po['quantity_received'] ?></td>
                                            <td>
                                                <span class="po-badge-<?= strtolower($po['status']) ?>">
                                                    <?= $po['status'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="update-button" onclick="openUpdateModal(<?= $po['id'] ?>)">
                                                    Update
                                                </button>
                                                <button class="delete-button" onclick="deleteOrder(<?= $po['id'] ?>)">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No purchase orders available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function openUpdateModal(orderId) {
        // Fetch the order details by orderId
        const order = <?php echo json_encode($rows); ?>.find(order => order.id === orderId);

        // Set the modal fields
        document.getElementById('orderId').value = order.id;
        document.getElementById('updateProductName').value = order.product_name;
        document.getElementById('updateQuantity').value = order.quantity_ordered;
        document.getElementById('updateReceivedQuantity').value = order.quantity_received;
        document.getElementById('updateDeliveredQuantity').value = 0; // Always start with 0
        document.getElementById('updateStatus').value = order.status;

        // Show the modal
        document.getElementById('updateModal').style.display = 'block';
        document.getElementById('modalBackground').style.display = 'block';
    }

    function closeUpdateModal() {
        document.getElementById('updateModal').style.display = 'none';
        document.getElementById('modalBackground').style.display = 'none';
    }

    document.getElementById('updateForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('update-order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order updated successfully');
                location.reload(); 
            } else {
                alert('Failed to update order: ' + data.message); 
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the order');
        });
    });

    function deleteOrder(orderId) {
        if (confirm('Are you sure you want to delete this order?')) {
            fetch('delete-order.php', {
                method: 'POST',
                body: new URLSearchParams({ 'id': orderId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order deleted successfully');
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert('Failed to delete order: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the order');
            });
        }
    }
</script>

</body>
</html>
