<?php
// Start the session.
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}

$user = $_SESSION['user'];

//get graph data - purchase order by status
include('database/po_status_pie_graph.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/dashboard.css">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
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
                <div class="dashboard_content_main">
                    <figure class="highcharts-figure">
                        <div id="container"></div>
                        <p class="highcharts-description">
                            Break-down of the purchase orders by status.
                        </p>
                    </figure>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Highcharts Scripts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    
    <!-- Your custom script -->
    <script src="js/script.js"></script>

    <script>

        var graphData=<?=json_encode($results)?>

        // Initialize Highcharts Pie Chart
        Highcharts.chart('container', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Purchase Orders By Status',
                align: 'left'
            },

            tooltip: {
    pointFormatter: function() {
        var point = this,
            series = point.series;

        return `<b>${point.name}</b>: ${point.y}`
    }
},
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f}%' // Limit to one decimal place
                    }
                }
            },
            series: [{
                name: 'Status',
                colorByPoint: true,
                data: graphData
            }]
        });
    </script>
</body>
</html>
