<?php
// Start the session.
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reports</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/dashboard.css">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
    <style>
        /* Report Page Styles */
        .reportTypeContainer {
            display: flex;
            flex-direction: row;
            gap: 15px; /* Add spacing between report types */
            margin: 20px;
        }

        .reportType {
            border: 1px solid #c2c6c2;
            padding: 10px 24px;
            border-radius: 4px;
            background: #fff;
            font-size: 20px;
            width: 100%;
            transition: background 0.3s ease, color 0.3s ease; /* Smooth hover transition */
        }

        .reportType:hover {
            background: #763a49;
            color: #fff;
        }

        .alignRight {
            text-align: right;
        }

        .reportExportBtn {
            height:25px;
            width:30px;
            padding: 4px 15px;
            display: inline-block;
            text-decoration: none;
            text-transform: uppercase;
            background: #f685a1;
            color: white;
            margin-left: 13px;
            font-size: 15px;
            border: 1px solid transparent;
            transition: background 0.3s ease, color 0.3s ease, border 0.3s ease; /* Smooth hover transition */
        }

        .reportExportBtn:hover {
            border: 1px solid #f685a1;
            background: #fff;
            color: #f685a1;
        }
    </style>
</head>
<body>
    <header>
        <div class="dashboard_topNav">
            <a href="#" id="nav"><i class="fa fa-navicon"></i></a>
            <a href="database/logout.php" id="logoutBtn"><i class="fa fa-power-off"></i> Log-out</a>
        </div>
    </header>

    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php'); ?>
        <div class="dashboard_content_container" id="dashboard_content_container">
            <div class="dashboard_content">
                
                <!-- Report Page Content -->
                <div class="reportTypeContainer">

                    <!-- Export Products -->
                    <div class="reportType">
                        <p>Export Products</p>
                        <div class="alignRight">
                            <a href="database/report_csv.php?report=product" class="reportExportBtn">Excel</a>
                            <a href="database/report_pdf.php?report=product" class="reportExportBtn">PDF</a>
                        </div>
                    </div>

                    <!-- Export Suppliers -->
                    <div class="reportType">
                        <p>Export Suppliers</p>
                        <div class="alignRight">
                        <a href="database/report_csv.php?report=supplier" class="reportExportBtn">Excel</a>
                        <a href="database/report_pdf.php?report=supplier" class="reportExportBtn">PDF</a>
                        </div>
                    </div>
                </div>
                    <div class="reportTypeContainer">
                     <!-- Export Deliveries -->
                    <div class="reportType">
                        <p>Export Deliveries</p>
                        <div class="alignRight">
                            <a href="export_deliveries_excel.php" class="reportExportBtn">Excel</a>
                            <a href="export_deliveries_pdf.php" class="reportExportBtn">PDF</a>
                        </div>
                    </div>

                    <!-- Export Purchase Orders -->
                    <div class="reportType">
                        <p>Export Purchase Orders</p>
                        <div class="alignRight">
                        <a href="database/report_csv.php?report=order" class="reportExportBtn">Excel</a>
                        <a href="database/report_pdf.php?report=order" class="reportExportBtn">PDF</a>
                        </div>
                    </div>

                    </div>
                    
                </div>

            </div>
        </div>
    </div>

    <!-- Your custom script -->
    <script src="js/script.js"></script>
</body>
</html>
