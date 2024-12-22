<?php
// Start the session
session_start();
if (!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit;
}

require_once('connection.php'); // Include your DB connection script
require_once('./fpdf186/fpdf.php'); // Include FPDF library

if (isset($_GET['report'])) {
    $reportType = $_GET['report'];

    // Initialize the PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, ucfirst($reportType) . ' Report', 0, 1, 'C');
    $pdf->Ln(10);

    // Switch for different report types
    switch ($reportType) {
        case 'product':
            $query = "SELECT 
                ps.id AS id,
                p.product_name, 
                p.description,s.supplier_name, 
                CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                 ps.created_at,
                 p.stock 
                FROM 
                productsuppliers ps
            JOIN 
                products p ON ps.product = p.id
            JOIN 
                suppliers s ON ps.supplier = s.id
            JOIN 
                users u ON p.created_by = u.id";
        
            $headers = ['ID', 'P-Name', 'Des', 'S-Name', 'User Name', 'Created At', 'Stock'];
            $columnWidths = [10, 20,30, 37, 40, 40, 10]; // Adjust column widths
        
            break;

        case 'deliveries':
            $query = "
                SELECT 
                    oph.date_received, 
                    oph.qty_received, 
                    p.product_name, 
                    s.supplier_name, op.batch
                FROM order_product_history oph
                JOIN order_product op ON oph.order_product_id = op.id
                JOIN products p ON op.product = p.id
                JOIN suppliers s ON op.supplier = s.id
            ";
            $headers = ['Date Received', 'QT RCV', 'P-Name', 'S-Name', 'Batch'];
            $columnWidths = [40, 10, 30, 50, 40];
            break;

        case 'supplier':
            $query = "SELECT id, supplier_name, supplier_location, email, created_at, updated_at FROM suppliers";
            $headers = ['ID', 'Name', 'Location', 'Email', 'Created At', 'Updated At'];
            $columnWidths = [15, 40, 20, 50, 40, 40];
            break;

        case 'order':
            $query = "SELECT o.id, s.supplier_name, p.product_name, o.quantity_ordered, o.quantity_received, 
                             o.quantity_remaining, o.status, o.batch, o.created_at,o.created_by
                      FROM order_product o
                      JOIN suppliers s ON o.supplier = s.id
                      JOIN products p ON o.product = p.id";
            $headers = ['ID', 'Supplier', 'Product', 'O', 'RC', 'RM', 'Status', 'Batch', 'Created', 'User'];
            $columnWidths = [10,40,20, 7, 7, 7, 23, 23, 40, 20];
            break;

        default:
            exit("Invalid report type.");
    }

    // Add headers to the PDF
    $pdf->SetFont('Arial', 'B', 10);
    foreach ($headers as $index => $header) {
        $pdf->Cell($columnWidths[$index], 10, $header, 1, 0, 'C');
    }
    $pdf->Ln();

    // Fetch data from the database
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if data exists
    if ($data) {
        // Add data rows to the PDF
        $pdf->SetFont('Arial', '', 10);
        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                $pdf->Cell($columnWidths[array_search($key, array_keys($row))], 10, $value, 1, 0, 'L');
            }
            $pdf->Ln();
        }
    } else {
        $pdf->Cell(0, 10, 'No data found.', 1, 1, 'C');
    }

    // Output the PDF
    $pdf->Output('D', $reportType . '_report.pdf');
    exit;
} else {
    echo "No report type specified.";
}
?>
