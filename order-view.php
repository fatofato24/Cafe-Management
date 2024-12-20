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
                            order_product.id,  -- Add this line to fetch the unique ID
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
    <td>
        <button class="delete-button" data-id="<?= $order['id'] ?>" data-name="<?= htmlspecialchars($order['product_name']) ?>">Delete</button>
        <a href="#" class="update-button" data-id="<?= $order['id'] ?>" 
           data-product="<?= htmlspecialchars($order['product_name']) ?>" 
           data-quantity="<?= htmlspecialchars($order['quantity_ordered']) ?>" 
           data-status="<?= htmlspecialchars($order['status']) ?>">Update</a>
    </td>
</tr>

                                                </td>
                                            </tr>
                                         <?php endforeach; ?>
                                    </tbody>
                                </table>
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
    <script>
  $(document).on('click', '.delete-button', function () {
    const orderId = $(this).data('id');
    const productName = $(this).data('name');

    console.log(`Deleting order ID: ${orderId}, Product: ${productName}`);

    if (confirm(`Are you sure you want to delete the order for "${productName}"?`)) {
        $.ajax({
            url: './database/delete-order.php',
            type: 'POST',
            data: { id: orderId },
            success: function (response) {
                console.log('Server response:', response); // Log the full response object
                if (response.success) {
                    // Find the row corresponding to the deleted order and remove it
                    $(`button[data-id='${orderId}']`).closest('tr').remove();
                    alert(response.message || 'Order deleted successfully.');
                } 
            },
            error: function (xhr, status, error) {
                console.error(`Error: ${error}`);
                
            }
        });
    }
});
</script>
<!-- Update Modal -->
<div id="updateModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeUpdateModal()">&times;</span>
        <h3>Update Order Details</h3>
        <form id="updateForm">
            <input type="hidden" name="id" id="updateOrderId">
            <div class="form-group">
                <label for="updateProductName">Product Name:</label>
                <input type="text" name="product_name" id="updateProductName" required>
            </div>
            <div class="form-group">
                <label for="updateQuantity">Quantity:</label>
                <input type="number" name="quantity" id="updateQuantity" required>
            </div>
            <div class="form-group">
                <label for="updateStatus">Status:</label>
                <select name="status" id="updateStatus">
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save">Save Changes</button>
                <button type="button" class="btn-cancel" onclick="closeUpdateModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Background -->
<div id="modalBackground" class="modal-background" onclick="closeUpdateModal()"></div>

<script>

// Open Update Modal
function openUpdateModal(orderId, productName, quantity, status) {
    console.log(`Opening update modal for Order ID: ${orderId}, Product: ${productName}`);
    document.getElementById('updateModal').style.display = 'block';
    document.getElementById('modalBackground').style.display = 'block';

    // Populate the form with existing values
    document.getElementById('updateOrderId').value = orderId;
    document.getElementById('updateProductName').value = productName;
    document.getElementById('updateQuantity').value = quantity;
    document.getElementById('updateStatus').value = status;
}

// Close Update Modal
function closeUpdateModal() {
    document.getElementById('updateModal').style.display = 'none';
    document.getElementById('modalBackground').style.display = 'none';
}

$(document).on('click', '.update-button', function (e) {
    e.preventDefault();

    const orderId = $(this).data('id');
    const productName = $(this).data('product');
    const quantity = $(this).data('quantity');
    const status = $(this).data('status');

    openUpdateModal(orderId, productName, quantity, status);
});

// Handle Update Form Submission
$('#updateForm').on('submit', function (e) {
    e.preventDefault();

    const formData = $(this).serialize();
    const orderId = $('#updateOrderId').val();
    console.log(`Submitting update for Order ID: ${orderId}`);
    console.log(`Form Data: ${formData}`);

    $.ajax({
        url: './database/update-order.php', // Backend endpoint for updating the order
        type: 'POST',
        data: formData,
        dataType: 'json', // Ensure response is JSON
        success: function (response) {
            console.log('Update response:', response);
            if (response.success) {
                alert(response.message || 'Order updated successfully.');
                location.reload(); // Refresh the page
            } else {
                alert(response.message || 'Failed to update the order.');
            }
        },
        error: function (xhr, status, error) {
            console.error(`Error: ${error}`);
            console.error(`Response: ${xhr.responseText}`);
            alert('An error occurred while updating the order. Please try again.');
        }
    });
});
</script>
</body>
</html>
