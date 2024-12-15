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
                            <div class="alignRight">
                                <button class="orderBtn orderProductBtn" id="orderProductBtn">Add Another Product</button>
                            </div>
                            <div id="orderProductLists"></div>

                            <div class="alignRight marginTop20">
                                <button class="orderBtn submitOrderProductBtn">Submit Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/script.js"></script>
<script src="js/jquery/jquery-3.7.1.min.js"></script>

<script>
    var products = <?= $products ?>;  // This loads products from PHP into JS

    function script() {
        let productOptionsTemplate = '\
<div>\
    <label for="product_name">PRODUCT NAME</label>\
    <select name="product_name" class="productNameSelect" id="product_name">\
        <option value="">Select Product</option>\
        INSERTPRODUCTHERE\
    </select>\
</div>';

        this.initialize = function() {
            this.registerEvents();  // Register the event listeners
            this.renderProductOptions();  // Initially render the products into the dropdown
        };

        this.renderProductOptions = function() {
            let optionHtml = '';
            // Loop through the products array and create option elements
            products.forEach((product) => {
                optionHtml += `<option value="${product.id}">${product.product_name}</option>`;
            });

            // Replace INSERTPRODUCTHERE with the actual options
            productOptions = productOptionsTemplate.replace('INSERTPRODUCTHERE', optionHtml);
        };

        this.registerEvents = function() {
            document.addEventListener('click', function(e) {
                let targetElement = e.target;

                // Add new product order row
                if (targetElement.id === 'orderProductBtn') {
                    let orderProductListsContainer = document.getElementById('orderProductLists');

                    // Add a new order product row with the product dropdown
                    orderProductListsContainer.innerHTML += '\
                        <div class="orderProductRow">\
                            ' + productOptions + '\
                            <div class="suppliersRows"></div>\
                        </div>';
                }
            });

            document.addEventListener('change', function(e) {
                let targetElement = e.target;
                if (targetElement.classList.contains('productNameSelect')) {
                    let productId = targetElement.value;

                    if (!productId.length) {
                        console.log('No product selected');
                    } else {
                        console.log('Product selected:', productId);
                        // Further logic for displaying product details, etc.
                    }
                }
            });
        };
    }

    // Initialize the script
    (new script()).initialize();
</script>
</body>
</html>
