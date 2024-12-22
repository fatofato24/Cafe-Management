<?php
$type = $_GET['report']; // Corrected variable declaration syntax

// Define mapping for filenames based on report type
$mapping_filenames = [
    'supplier' => 'Supplier Report',
    'product' => 'Product Report'
];

// Set the file name based on the mapping
$file_name = isset($mapping_filenames[$type]) ? $mapping_filenames[$type] . '.xls' : 'Report.xls';

header("Content-Disposition: attachment; filename=\"$file_name\"");
header("Content-Type: application/vnd.ms-excel");

// Include database connection
include('connection.php');

if ($type === 'product') {
    $stmt = $conn->prepare(
        "SELECT products.*, users.first_name, users.last_name 
        FROM products 
        INNER JOIN users ON products.created_by = users.id 
        ORDER BY products.created_at DESC"
    );

    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $products = $stmt->fetchAll();

    $is_header = true;

    foreach ($products as $product) {
        // Add creator's full name to the product record
        $product['created_by'] = $product['first_name'] . ' ' . $product['last_name'];

        // Remove unnecessary fields
        unset($product['first_name'], $product['last_name'], $product['password'], $product['email']);

        // Print header row once
        if ($is_header) {
            $row = array_keys($product);
            echo implode("\t", $row) . "\n";
            $is_header = false;
        }

        // Sanitize and format product values
        array_walk($product, function (&$str) {
            $str = preg_replace("/\t/", "\\t", $str); // Escape tabs
            $str = preg_replace("/\r\n/", "\\n", $str); // Escape newlines
            if (strstr($str, '"')) {
                $str = str_replace('"', '""', $str); // Escape double quotes
            }
        });

        echo implode("\t", $product) . "\n";
    }
} elseif ($type === 'supplier') {
    // Add logic to handle 'supplier' report generation
    echo "Supplier report generation is not yet implemented.";
} else {
    echo "Invalid report type.";
}
?>
