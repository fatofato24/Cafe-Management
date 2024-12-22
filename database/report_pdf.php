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

    switch ($reportType) {
        case 'product':
            $query = "SELECT 
    ps.id AS id,
    p.product_name, 
    p.description, 
    ps.created_at, 
    ps.updated_at, 
    s.supplier_name, 
    CONCAT(u.first_name, ' ', u.last_name) AS user_name
FROM 
    productsuppliers ps
JOIN 
    products p ON ps.product = p.id
JOIN 
    suppliers s ON ps.supplier = s.id
JOIN 
    users u ON p.created_by = u.id
";
        
            $headers = ['ID', 'Product Name', 'Description', 'Supplier Name', 'User Name', 'Created At', 'Updated At'];
        
            // Define column widths (adjusted for the new columns)
            $columnWidths = [15, 40, 60, 40, 40, 40, 40]; // Increased width for Created At and Updated At
        
            // Add headers to the PDF (only once)
            foreach ($headers as $index => $header) {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell($columnWidths[$index], 10, $header, 1, 0, 'C');
            }
            $pdf->Ln();
        
            // Fetch product data directly from the database
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            // Check if product data exists
            if ($products) {
                // Add data rows for products
                foreach ($products as $product) {
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->Cell($columnWidths[0], 10, $product['id'], 1, 0, 'C'); // ID
                    $pdf->Cell($columnWidths[1], 10, $product['product_name'], 1, 0, 'L'); // Product Name
                    $pdf->Cell($columnWidths[2], 10, $product['description'], 1, 0, 'L'); // Description
                    $pdf->Cell($columnWidths[3], 10, $product['supplier_name'], 1, 0, 'L'); // Supplier Name
                    $pdf->Cell($columnWidths[4], 10, $product['user_name'], 1, 0, 'L'); // User Name
                    $pdf->Cell($columnWidths[5], 10, $product['created_at'], 1, 0, 'L'); // Created At
                    $pdf->Cell($columnWidths[6], 10, $product['updated_at'], 1, 1, 'L'); // Updated At
                }
            } else {
                $pdf->Cell(0, 10, 'No product data found.', 1, 1, 'C');
            }
            break;
        

        case 'supplier':
            // Direct query to fetch all suppliers from the database
            $query = "SELECT id, supplier_name, supplier_location, email, created_at, updated_at FROM suppliers";
            $headers = ['ID', 'Name', 'Location', 'Email', 'Created At', 'Updated At'];

            // Define column widths (adjusted)
            $columnWidths = [15, 40, 20, 50, 40, 40]; // Reduced ID and Name width, increased date width

            // Set the left margin for the table (move it left)
            $pdf->SetX(10); // Move to the left (10 mm from the left margin)

            // Add headers to the PDF (only once)
            foreach ($headers as $index => $header) {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell($columnWidths[$index], 10, $header, 1, 0, 'C');
            }
            $pdf->Ln();

            // Fetch supplier data directly from the database
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if suppliers data exists
            if ($suppliers) {
                // Add data rows for suppliers
                foreach ($suppliers as $supplier) {
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->Cell($columnWidths[0], 10, $supplier['id'], 1, 0, 'C'); // ID
                    $pdf->Cell($columnWidths[1], 10, $supplier['supplier_name'], 1, 0, 'L'); // Supplier Name
                    $pdf->Cell($columnWidths[2], 10, $supplier['supplier_location'], 1, 0, 'L'); // Supplier Location
                    $pdf->Cell($columnWidths[3], 10, $supplier['email'], 1, 0, 'L'); // Email
                    $pdf->Cell($columnWidths[4], 10, $supplier['created_at'], 1, 0, 'L'); // Created At
                    $pdf->Cell($columnWidths[5], 10, $supplier['updated_at'], 1, 1, 'L'); // Updated At
                }
            } else {
                $pdf->Cell(0, 10, 'No supplier data found.', 1, 1, 'C');
            }
            break;

            case 'order':
                $query = "SELECT o.id, s.supplier_name, p.product_name, o.quantity_ordered, o.quantity_received, 
                                 o.quantity_remaining, o.status, o.batch, o.created_at, o.updated_at
                          FROM order_product o
                          JOIN suppliers s ON o.supplier = s.id
                          JOIN products p ON o.product = p.id";
             $pdf->SetX(5);
                $headers = ['ID', 'Supplier', 'Product', 'Order', 'Receive', 'Remain', 'Status', 'Batch', 'Created', 'Updated'];
            
                // Define column widths (adjusted for the new columns)
                $columnWidths = [5, 33,16, 10,10, 10, 16, 20, 40, 40];
            
                // Add headers to the PDF (only once)
                foreach ($headers as $index => $header) {
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell($columnWidths[$index], 10, $header, 1, 0, 'C');
                }
                $pdf->Ln();
            
                // Fetch order data directly from the database
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
                // Check if order data exists
                if ($orders) {
                    // Add data rows for orders
                    foreach ($orders as $order) {
                       
                             $pdf->SetX(5);
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->Cell($columnWidths[0], 10, $order['id'], 1, 0, 'C'); // Order ID
                        $pdf->Cell($columnWidths[1], 10, $order['supplier_name'], 1, 0, 'L'); // Supplier Name
                        $pdf->Cell($columnWidths[2], 10, $order['product_name'], 1, 0, 'L'); // Product Name
                        $pdf->Cell($columnWidths[3], 10, $order['quantity_ordered'], 1, 0, 'C'); // Ordered Qty
                        $pdf->Cell($columnWidths[4], 10, $order['quantity_received'], 1, 0, 'C'); // Received Qty
                        $pdf->Cell($columnWidths[5], 10, $order['quantity_remaining'], 1, 0, 'C'); // Remaining Qty
                        $pdf->Cell($columnWidths[6], 10, $order['status'], 1, 0, 'L'); // Status
                        $pdf->Cell($columnWidths[7], 10, $order['batch'], 1, 0, 'L'); // Batch
                        $pdf->Cell($columnWidths[8], 10, $order['created_at'], 1, 0, 'L'); // Created At
                        $pdf->Cell($columnWidths[9], 10, $order['updated_at'], 1, 1, 'L'); // Updated At
                        }
                       
                    
                } else {
                    $pdf->Cell(0, 10, 'No order data found.', 1, 1, 'C');
                }
                break;
            

        default:
            exit("Invalid report type.");
    }

    // Add headers to the PDF (only once)
    foreach ($headers as $index => $header) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell($columnWidths[$index], 10, $header, 1, 0, 'C');
    }
    $pdf->Ln();

    // Fetch and add data to the PDF (for products or orders)
    if ($reportType == 'product') {
        // Add product data (using similar logic to suppliers)
    } elseif ($reportType == 'order') {
        // Add order data (using similar logic to suppliers)
    }

    // Output the PDF
    $pdf->Output('D', $reportType . '_report.pdf');
    exit;
} else {
    echo "No report type specified.";
}
?>
