<?php
// Start the session
session_start();
if (!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit;
}

require_once('connection.php'); // Include your DB connection script

if (isset($_GET['report'])) {
    $reportType = $_GET['report'];

    // Set the headers for the CSV file download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $reportType . '_report.csv');

    // Open output stream for writing the CSV
    $output = fopen('php://output', 'w');

    // Fetch data based on the report type
    switch ($reportType) {
        case 'product':
            $query = "SELECT id, product_name, description, created_at,stock FROM products";
            $headers = ['ID', 'Name', 'Description', 'Created At', 'Stock'];
            break;

        case 'supplier':
            $query = "SELECT id, supplier_name, supplier_location, email, created_at, updated_at FROM suppliers";
            $headers = ['ID', 'Name', 'Location', 'Email', 'Created At', 'Updated At'];
            break;

        case 'order':
            $query = "SELECT id, supplier, product, quantity_ordered, quantity_received, quantity_remaining, status, batch, created_at, updated_at FROM order_product";
            $headers = ['ID', 'Supplier', 'Product', 'Ordered Quantity', 'Received Quantity', 'Remaining Quantity', 'Status', 'Batch', 'Created At', 'Updated At'];
            break;

        case 'deliveries':
            // Query to fetch data from order_product_history along with product_name and supplier_name
            $query = "
                SELECT 
                    oph.date_received, 
                    oph.qty_received, 
                    p.product_name, 
                    s.supplier_name,op.batch
                FROM order_product_history oph
                JOIN order_product op ON oph.order_product_id = op.id
                JOIN products p ON op.product = p.id
                JOIN suppliers s ON op.supplier = s.id
            ";
            $headers = ['Date Received', 'Quantity Received', 'Product Name', 'Supplier Name','Batch'];
            break;

        default:
            exit("Invalid report type.");
    }

    // Write the headers to the CSV file
    fputcsv($output, $headers);

    // Execute the query
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Write each row to the CSV file
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    // Close the output stream
    fclose($output);
    exit;
} else {
    echo "No report type specified.";
}
?>
