<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}
$show_table = 'users';
$user = $_SESSION['user'];

$_SESSION['table']='users';
$users = include('database/show.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Management</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
    <style>
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
        }
        .column {
            width: 48%;
        }
        .section_header {
            font-size: 24px;
            color: #f685a1;
            border-bottom: 1px solid #ffd7e1;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
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
                            <h1 class="section_header"><i class="fa fa-list"></i> List of Users</h1>
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
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
                                        <td><?= date('M d,Y @ h:i:s A', strtotime($user['created_at'])) ?></td>
                                        <td><?= date('M d,Y @ h:i:s A', strtotime($user['updated_at'])) ?></td>
                                        <td>
                                            <a href="#" class="updateUser" 
                                               data-userid="<?= $user['id'] ?>" 
                                               data-fname="<?= $user['first_name'] ?>" 
                                               data-lname="<?= $user['last_name'] ?>" 
                                               data-email="<?= $user['email'] ?>">
                                                <i class="fa fa-pencil"></i> Edit
                                            </a>
                                            <a href="#" class="deleteUser" 
                                               data-userid="<?= $user['id'] ?>" 
                                               data-fname="<?= $user['first_name'] ?>" 
                                               data-lname="<?= $user['last_name'] ?>">
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
    <script src="js/script.js"></script>
<script src="js/jquery/jquery-3.7.1.min.js"></script>
<script>
// Handle Edit (Update User)
document.addEventListener('click', function(e) {
    if (e.target.closest('.updateUser')) {
        e.preventDefault();
        const button = e.target.closest('.updateUser');
        const userId = button.dataset.userid;
        const firstName = button.dataset.fname;
        const lastName = button.dataset.lname;
        const email = button.dataset.email;

        const newFirstName = prompt("Enter First Name:", firstName);
        const newLastName = prompt("Enter Last Name:", lastName);
        const newEmail = prompt("Enter Email:", email);

        if (newFirstName && newLastName && newEmail) {
            $.post('database/update-user.php', {
                user_id: userId,
                f_name: newFirstName,
                l_name: newLastName,
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

// Handle Delete User
document.addEventListener('click', function(e) {
    if (e.target.closest('.deleteUser')) {
        e.preventDefault();
        const button = e.target.closest('.deleteUser');
        const userId = button.dataset.userid;
        const firstName = button.dataset.fname;
        const lastName = button.dataset.lname;

        if (confirm(`Are you sure you want to delete ${firstName} ${lastName}?`)) {
            $.post('database/delete-user.php', { user_id: userId }, function(response) {
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
