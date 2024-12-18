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
        /* Styling for Pie Chart */
        .pie-chart-container {
            width: 100%; /* Ensure the chart adjusts to the available space */
            max-width: 400px; /* Smaller maximum width for the chart */
            margin: 0 auto; /* Center the chart */
            text-align: center;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        canvas {
            display: block;
            max-width: 100% !important;
            margin: 0 auto;
        }

        .chart-title {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
            font-weight: bold;
        }

        /* Hover effect for pie chart slices */
        .chart-container:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
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

                    <!-- Pie Chart Section -->
                    <div class="pie-chart-container">
                        <h2 class="chart-title">Order Status Distribution</h2>
                        <canvas id="statusPieChart" width="400" height="400"></canvas>
                    </div>

                    <!-- Your existing dashboard content here -->
                    
                    <div class="dashboard_overview">
                        <!-- Include your other sections and content here -->
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/jquery/jquery-3.7.1.min.js"></script>
    <script src="js/script.js"></script>

    <script>
        // Pass PHP data to JavaScript
        const statusCounts = <?php echo json_encode($statusCounts); ?>;
        console.log("Status Counts:", statusCounts); // Check the data in the console

        // Create the pie chart using Chart.js
        const ctx = document.getElementById('statusPieChart').getContext('2d');
        const pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Pending', 'Completed', 'Incomplete'], // Updated label to "Incomplete"
                datasets: [{
                    label: 'Order Statuses',
                    data: [statusCounts.pending, statusCounts.completed, statusCounts.incomplete], // Data from PHP
                    backgroundColor: ['#FF5733', '#28a745', '#ffc107'], // Custom colors
                    borderColor: '#fff',
                    borderWidth: 2, // Subtle border for better look
                    hoverOffset: 10 // Slight hover effect
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' orders'; // Show count in tooltip
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true, // Animate the pie slices to make it more interactive
                    animateRotate: true
                }
            }
        });
    </script>
</body>
</html>
