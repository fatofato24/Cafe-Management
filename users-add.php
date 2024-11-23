<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}
$_SESSION['table'] = 'users';
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add User</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/dashboard.css">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
</head>
<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php'); ?>

        <div class="dashboard_content_container" id="dashboard_content_container">
            <?php include('partials/app-topnav.php'); ?>

            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <div id="userAddFormContainer">
                        <h2>Add User</h2>
                        <form action="database/add.php" method="POST" class="appForm">
                            <div class="appFormInputContainer">
                                <label for="first_name">First Name</label>
                                <input type="text" name="first_name" class="appFormInput" id="first_name" required>
                            </div>
                            <div class="appFormInputContainer">
                                <label for="last_name">Last Name</label>
                                <input type="text" name="last_name" class="appFormInput" id="last_name" required>
                            </div>
                            <div class="appFormInputContainer">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="appFormInput" id="email" required>
                            </div>
                            <div class="appFormInputContainer">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="appFormInput" id="password" required>
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
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
