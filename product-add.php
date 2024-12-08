<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}
$_SESSION['table'] = 'products';
$_SESSION['redirect_to'] = 'product-add.php';

$user = $_SESSION['user'];

// Define $successMessage at the beginnin
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
    <title>Add Product</title>
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

                        <!-- Create Product Form -->
                        <div class="column column-12">
                            <h1 class="section_header"><i class="fa fa-plus"></i> Create Product</h1>
                            <div id="userAddFormContainer">
                                <h2>Add Product</h2>
                                <form action="database/productadd.php" method="POST" class="appForm" enctype="multipart/form-data">
                                    <div class="appFormInputContainer">
                                        <label for="product_name">Product Name</label>
                                        <input type="text" class="appFormInput" name="product_name" placeholder="Enter product name..."required>
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="description">Description</label>
                                        <textarea class="appFormInput productTextAreaInput" id="description" placeholder="Enter product description..." name="description"></textarea>
                                    </div>
                                    <div class="appFormInputContainer">
    <label for="suppliersSelect">Suppliers</label>
    <select name="suppliers[]" id="suppliersSelect" multiple> <!-- Allow multiple selections -->
        <option value="">Select Supplier</option>
        
        <?php
        // Assuming session table management and `show-users.php` correctly fetch suppliers.
        $show_table = 'suppliers';
        $suppliers = include('database/show-users.php');

        // Generate options dynamically from the suppliers array.
        foreach ($suppliers as $supplier) {
            echo "<option value='" . htmlspecialchars($supplier['id']) . "'>" . htmlspecialchars($supplier['supplier_name']) . "</option>";
        }
        ?>
    </select>
</div>
                                    <div class="appFormInputContainer">
                                        <label for="product_name">Product Image</label>
                                        <input type="file" class="appFormInput" name="img"/>
                                    </div>

                                    <button type="submit" class="appBtn">Add Product</button>
                                </form>
                                <?php if (isset($_SESSION['response'])): ?>

                                    <?php
                                    $response_message = $_SESSION['response']['message'];
                                    $is_success = $_SESSION['response']['success'];
                                    ?>
                                    <div class="responseMessage">
                                        <p class="responseMessage <?= $is_success ? 'responseMessage_success' : 'responseMessage_error' ?>">
                                            <?= $response_message ?>
                                        </p>
                                    </div>
                                    <?php unset($_SESSION['response']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>   
            </div>
        </div>
    </div>
    <script src="js/script.js"></script>
    <script src="js/jquery/jquery-3.7.1.min.js"></script>
</body>
</html>
