<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}
$_SESSION['table'] = 'users';
$_SESSION['redirect_to'] = 'users-add.php';
$user = $_SESSION['user'];
$users = include('database/show.php');
// Initialize the $successMessage variable
$successMessage = '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add User</title>
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
        /* General styling for the Permissions section */
.Permissions {
    font-family: Arial, sans-serif;
    margin: 20px;
}

/* Styling for each permission section */
.permission {
    border: 1px solid #ccc; /* Add border between sections */
    margin-bottom: 15px;
    padding: 5px;
    border-radius: 8px;
}

/* Row layout */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 10px 0;
}

/* Column layout for each permission */
.col-md-3 {
    flex: 1;
    padding: 5px;
}

/* Column for permissions (View, Create, Edit, etc.) */
.col-md-2 {
    flex: 1;
    padding: 2px;
}

/* Styling for the module names (Dashboard, Reports, etc.) */
.moduleName {
    font-weight: bold;
    margin: 0;
    
}

/* Styling for the module functions (View, Create, Edit, etc.) */
.moduleFunc {
    display: inline-block;
    padding: 5px 5px;
    margin: 5px;
    background-color:rgb(230, 103, 190);
    color: white;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
    transition: background-color 0.3s ease;
}

/* Hover effect for the buttons */
.moduleFunc:hover {
    background-color:rgb(137, 54, 143);
}

/* Active class styling */
.permissionActive {
    background-color:rgb(46, 7, 49); /* Green color when active */
}

/* Ensuring the layout is responsive */
@media (max-width: 768px) {
    .col-md-3, .col-md-2 {
        flex: 100%; /* Stack columns on smaller screens */
        margin-bottom: 10px;
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

                        <!-- Create User Form -->
                        <div class="column column-12">
                            <h1 class="section_header"><i class="fa fa-plus"></i> Create User</h1>
                            <div id="userAddFormContainer">
                                <h2>Add User</h2>
                                <form action="database/add.php" method="POST" class="appForm">
                                    <div class="appFormInputContainer">
                                        <label for="first_name">First Name</label>
                                        <input type="text" name="first_name" required>
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" name="last_name" required>
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" required>
                                    </div>
                                    <div class="appFormInputContainer" style="position: relative;">
                                        <label for="password">Password</label>
                                         <input 
                                       type="password" 
                                       name="password" 
                                       id="password" 
                                       class="appFormInput" 
                                      required 
                                        />
                                        <i 
                                         id="togglePassword" 
                                         class="fa fa-eye" 
                                         style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;">
                                         </i>
                                        </div>
                                        <input type="hidden" name="permissions">

                                        <?php include('partials/permission.php')?>

                                    <button type="submit" class="appBtn">Add User</button>
                                </form>
                                <?php if(isset($_SESSION['response'])): ?>
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
    <script>
    function loadScript() {
        this.initialize = function() {
            this.registerEvents();
        };

        this.registerEvents = function() {
            // Click event
            document.addEventListener('click', function(e) {
                let target = e.target;

                // Check if class name moduleFunc is clicked
                if (target.classList.contains('moduleFunc')) {
                    // Set the active class
                    if (target.classList.contains('permissionActive')) {
                        target.classList.remove('permissionActive');
                    } else {
                        target.classList.add('permissionActive');
                    }
                    // Add hidden element store value here
                }
            });
        };
    }

    var script = new loadScript();
    script.initialize();
</script>

</body>
</html>
