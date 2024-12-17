<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}

$show_table = 'products';
$products = include('database/show_products.php');  // Ensure this returns the products array from your database
$products = json_encode($products);  // Convert to JSON format for JavaScript

// Define $successMessage at the beginning
$successMessage = ''; // Initialize the variable

if (isset($_SESSION['response'])) {
    $response_message = $_SESSION['response']['message'];
    $is_success = $_SESSION['response']['success'];
    $successMessage = $response_message; // Set the success message
    unset($_SESSION['response']); // Clear session response after displaying the message
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Product</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
    <style>
        /* Layout for User List and Create User Form */
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px; /* Adds space between columns */
            margin: 20px 0;
        }

        .column {
            width: 48%; /* Take up 48% of the row width */
        }

        .section_header {
            font-size: 24px;
            color: #f685a1;
            border-bottom: 1px solid #ffd7e1;
            padding-bottom: 15px;
            padding-left: 10px;
            border-left: 4px solid #f690bf;
            margin-bottom: 20px;
        }

        /* Form Container */
        #userAddFormContainer {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }

        #userAddFormContainer h2 {
            text-align: center;
            font-size: 20px;
            color: #f685a1;
            margin-bottom: 20px;
        }

        .appForm {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .appFormInputContainer label {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .appFormInputContainer input {
            font-size: 16px;
            padding: 10px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        .appFormInputContainer input:focus {
            border-color: #f685a2;
            outline: none;
        }

        .appBtn {
            background-color: #f685a2;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .appBtn:hover {
            background-color: #f690bf;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        table th {
            background-color: #f4f6f9;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f685a2;
            color: white;
        }

        .userCount {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .row {
                flex-direction: column;
            }

            .column {
                width: 100%;
            }

            #userAddFormContainer {
                max-width: 100%;
            }
        }

        
/* Add this to the removeBtn class */
.removeBtn {
    background-color: #f685a2;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 5px 10px;
    cursor: pointer;
    font-size: 12px;
    transition: background-color 0.3s ease;
    float: right; /* Align the button to the right */
}

.removeBtn:hover {
    background-color: #f690bf;
}
.orderProductRow {
    position: relative; /* To ensure the button floats correctly within the container */
}

.removeBtn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%); /* Vertically center the button */
}
/* Success Message */
.success-message {
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    text-align: center;
}

/* No Products Message */
.no-products-message {
    background-color: #f2f2f2;
    color: #888;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    text-align: center;
}

    </style>
</head>
<body>
<header>
    <div class="dashboard_topNav">
        <a href="#" id="nav"><i class="fa fa-navicon"></i></a>
        <a href="database/logout.php" id="logoutBtn"><i class="fa fa-power-off"></i>Log-out</a>
    </div>
</header>

<div id="dashboardMainContainer">
    <?php include('partials/app-sidebar.php'); ?>
    <div class="dashboard_content_container" id="dashboard_content_container">
        <div class="dashboard_content">
            <div class="dashboard_content_main">
                <div class="row">
                    <!-- Success Message -->
                    <?php if ($successMessage): ?>
                        <div class="success-message">
                            <?= htmlspecialchars($successMessage) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Order Product Form -->
                    <div class="column column-12">
                        <h1 class="section_header"><i class="fa fa-plus"></i> Order Product</h1>
                        <div>
                            <form action="database/save-order.php" method="POST">
                                 <div class="alignRight">
                                <button type="button" class="orderBtn orderProductBtn" id="orderProductBtn">Add Another Product</button>
                            </div>
                            <!-- Display message if no products are added yet -->
<div id="no-products-message" class="no-products-message">
    No products selected yet. Click "Add Product" to start the order.
</div>

<div id="orderProductLists"></div>

                            <div class="alignRight marginTop20">
                                <button type="submit" class="orderBtn submitOrderProductBtn">Submit Order</button>
                            </div>
                            </form>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/jquery/jquery-3.7.1.min.js"></script>
<script src="js/script.js"></script>
<script>
    var products = <?= $products ?>;  // Products data passed from PHP
var counter = 0;

function script() {
    var vm = this;

    this.initialize = function () {
        this.registerEvents();
    };

    this.registerEvents = function () {
        document.getElementById('orderProductBtn').addEventListener('click', function () {
    // Hide the "No products selected yet" message when the first product is added
    document.getElementById('no-products-message').style.display = 'none';
    
    // Call the function to add a product row
    vm.addProductRow();
});

        // Handle Dynamic Events
        document.getElementById('orderProductLists').addEventListener('change', function (e) {
            if (e.target.classList.contains('productNameSelect')) {
                let productId = e.target.value;
                let counterId = e.target.dataset.counter;
                let supplierContainer = document.getElementById(`supplierRows_${counterId}`);

                if (productId) {
                    // Fetch suppliers for selected product
                    $.get('database/get-product-suppliers.php', { id: productId }, function (suppliers) {
                        vm.renderSupplierRows(suppliers, supplierContainer);
                    }, 'json');
                } else {
                    supplierContainer.innerHTML = ''; // Clear suppliers if no product is selected
                }
            }
        });

        // Handle Increment/Decrement Quantity Buttons
        document.getElementById('orderProductLists').addEventListener('click', function (e) {
            if (e.target.classList.contains('incrementQtyBtn')) {
                let qtyInput = e.target.previousElementSibling;
                qtyInput.value = parseInt(qtyInput.value) + 1;
            }
            if (e.target.classList.contains('decrementQtyBtn')) {
                let qtyInput = e.target.nextElementSibling;
                if (parseInt(qtyInput.value) > 1) {
                    qtyInput.value = parseInt(qtyInput.value) - 1;
                }
            }

            // Handle Remove Button
            if (e.target.classList.contains('removeBtn')) {
                    let rowId = e.target.dataset.rowId;
                    document.getElementById(rowId).remove();
                }
        });
    };

    this.addProductRow = function () {
        let orderProductListsContainer = document.getElementById('orderProductLists');
        let productRowHtml = `
            <div class="orderProductRow" id="orderProductRow_${counter}">
                <label for="product_name">PRODUCT NAME</label>
                <select name="products[]" class="productNameSelect" data-counter="${counter}">
                    <option value="">Select Product</option>
                    ${products.map(product => `<option value="${product.id}">${product.product_name}</option>`).join('')}
                </select>
                <div class="quantity-container">
                    <button type="button" class="decrementQtyBtn">-</button>
                    <input type="number" min="1" value="1" class="quantityInput" />
                    <button type="button" class="incrementQtyBtn">+</button>
                <button type="button" class="removeBtn" data-row-id="orderProductRow_${counter}">
                    <i class="fa fa-trash"></i> Remove
                </button>
                    </div>
                <div class="suppliersRows" id="supplierRows_${counter}"></div>
            </div>`;
        orderProductListsContainer.insertAdjacentHTML('beforeend', productRowHtml);
        counter++; // Increment counter for next row
    };

    this.renderSupplierRows = function (suppliers, container) {
        let supplierRows = '';

        if (suppliers.length > 0) {
            suppliers.forEach(supplier => {
                supplierRows += `
                    <div class="row">
                        <div style="width: 50%;">
                            <p class="supplierName">${supplier.supplier_name}</p>
                        </div>
                        <div style="width: 50%;">
                            <label for="quantity_${supplier.id}">Supplier Quantity:</label>
                            <input type="number" min="1" id="quantity_${supplier.id}" 
                                name="supplier_quantity[${supplier.id}]" 
                                class="appFormInput" placeholder="Enter quantity..." />
                        </div>
                    </div>`;
            });
        } else {
            supplierRows = `<p>No suppliers available for this product.</p>`;
        }

        container.innerHTML = supplierRows;  // Render the rows into the container
    };
}

// Initialize the script
(new script()).initialize();


</script>
</body>
</html>
<?php if ($successMessage): ?>
    <div class="success-message">
        <?= htmlspecialchars($successMessage) ?>
    </div>
<?php endif; ?>
