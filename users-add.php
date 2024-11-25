<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}
$_SESSION['table'] = 'users';
$user = $_SESSION['user'];
$users = include('database/show-users.php');
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
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php'); ?>
        <div class="dashboard_content_container">
            <?php include('partials/app-topnav.php'); ?>
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
                        <div class="column">
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
                                    <div class="appFormInputContainer">
                                        <label for="password">Password</label>
                                        <input type="password" name="password" required>
                                    </div>
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

                        <!-- List of Users -->
                        <div class="column">
                            <h1 class="section_header"><i class="fa fa-list"></i> List of Users</h1>
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $index => $user): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $user['first_name'] ?></td>
                                        <td><?= $user['last_name'] ?></td>
                                        <td><?= $user['email'] ?></td>
                                        <td>
                                            <a href=""><i class="fa fa-pencil"></i> Edit</a>
                                            <a href="#" class="deleteUser" data-userid="<?= $user['id'] ?>" data-fname="<?= $user['first_name'] ?>" data-lname="<?= $user['last_name'] ?>">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
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
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('deleteUser')) {
                e.preventDefault();
                const userId = e.target.dataset.userid;
                const fname = e.target.dataset.fname;
                const lname = e.target.dataset.lname;
                const fullName = `${fname} ${lname}`;

                if (window.confirm(`Are you sure you want to delete ${fullName}?`)) {
                    $.ajax({
                        method: 'POST',
                        url: 'database/delete-user.php',
                        data: {
                            user_id: userId,
                            f_name: fname,
                            l_name: lname
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data.success) {
                                alert(data.message);
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        },
                        error: function () {
                            alert('An error occurred.');
                        }
                    });
                }
            }
        });
    </script>
</body>
</html>
