<?php
// Start the session.
session_start();
if (!isset($_SESSION['user'])) header('location: login.php');

$user = $_SESSION['user'];
?>


<<!DOCTYPE html>
<html lang="en">
<head>
    <title>DashBoard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/dashboard.css">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
</head>
<body>
    <header>
        <div class="dashboard_topNav">
            <a href="#" id="nav"><i class="fa fa-navicon"></i></a>
            <a href="database/logout.php" id="logoutBtn"><i class="fa fa-power-off"></i>Log-out</a>
        </div>
    </header>

    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php')?>
        
        <div class="dashboard_content_container" id="dashboard_content_container">
            <?php include('partials/app-topnav.php')?>
            <div class="dashboard_content"></div>
            <div class="dashboard_content_main">
                <!-- Main content goes here -->
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
