<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}

// Include database connection
require_once('database/connection.php');

// Fetch orders from the database
$stmt = $conn->prepare(
    "SELECT 
        order_product.id,
        order_product.batch,
        products.product_name,
        order_product.quantity_ordered,
        users.first_name, 
        users.last_name, 
        suppliers.supplier_name, 
        order_product.status, 
        order_product.created_at, 
        order_product.created_by
    FROM order_product
    JOIN suppliers ON order_product.supplier = suppliers.id
    JOIN products ON order_product.product = products.id
    JOIN users ON order_product.created_by = users.id
    ORDER BY order_product.created_at DESC"
);

$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize counters for each status
$statusCounts = [
    'pending' => 0,
    'completed' => 0,
    'incomplete' => 0,  // Replaced 'ordered' with 'incomplete'
];

// Count orders by their status
foreach ($rows as $row) {
    $status = strtolower($row['status']);
    if (isset($statusCounts[$status])) {
        $statusCounts[$status]++;
    } else {
        $statusCounts['incomplete']++; // Handle any unexpected statuses as 'incomplete'
    }
}

// Fetch supplier product data
include('database/supplier_product_bar_graph.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Layout for the graphs to be side by side */
        .charts-container {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            margin: 20px 0;
        }

        .pie-chart-container, .bar-chart-container {
            width: 48%; /* Take almost half of the container width */
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        canvas {
            display: block;
            width: 100% !important;
            height: auto !important;
            margin: 0 auto;
        }

        .chart-title {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php'); ?>
        <?php include('partials/app-topnav.php'); ?>

        <div class="dashboard_content_container">
            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <h1 class="section_header"><i class="fa fa-tachometer-alt"></i> Dashboard</h1>

                    <!-- Side-by-side layout for the Pie and Bar Charts -->
                    <div class="charts-container">
                        <!-- Pie Chart Section -->
                        <div class="pie-chart-container">
                            <h2 class="chart-title">Order Status Distribution</h2>
                            <canvas id="statusPieChart" width="400" height="400"></canvas>
                        </div>

                        <!-- Bar Chart Section -->
                        <div class="bar-chart-container">
                            <h2 class="chart-title">Product Count by Supplier</h2>
                            <canvas id="productBarChart" width="400" height="400"></canvas>
                        </div>
                    </div>

                    <!-- Your existing dashboard content -->
                    <div class="dashboard_overview">
                        <!-- Include your other sections and content here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Wait until DOM is fully loaded before executing
        document.addEventListener('DOMContentLoaded', function () {
            // Pass PHP data to JavaScript
            const statusCounts = <?php echo json_encode($statusCounts); ?>;
            const barGraphData = <?php echo json_encode($bar_chart_data); ?>;
            const barGraphCategories = <?php echo json_encode($categories); ?>;

            // Log data for debugging
            console.log("Status Counts:", statusCounts);
            console.log("Bar Graph Categories:", barGraphCategories);
            console.log("Bar Graph Data:", barGraphData);

            // Create the pie chart using Chart.js
            const ctxPie = document.getElementById('statusPieChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: ['Pending', 'Completed', 'Incomplete'],
                    datasets: [{
                        label: 'Order Statuses',
                        data: [statusCounts.pending, statusCounts.completed, statusCounts.incomplete],
                        backgroundColor: ['#69359c ', '#b768a2', '#843f5b'], // Purple, Pink, Yellow
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                },
                                color: '#333'
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });

            // Create the bar chart using Chart.js
const ctxBar = document.getElementById('productBarChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: barGraphCategories, // Use dynamic categories
        datasets: [{
            label: 'Product Count',
            data: barGraphData, // Use dynamic data
            backgroundColor: [
                '#dda0dd', // plum purple
                '#7851a9', // royal purple
                '#dcd0ff', // pale lavender
                '#967bb6', // lavender purple
                '#ff80e0', // Pastel Pink
                '#ff99cc'  // Pale Pink
            ], // Shades of pink for columns
            borderColor: '#ff1a75', // Darker Pink for borders
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    color: '#333' // Text color for x-axis
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#333' // Text color for y-axis
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: {
                        size: 14
                    },
                    color: '#333'
                }
            }
        }
    }
});

        });
    </script>
</body>
</html>
