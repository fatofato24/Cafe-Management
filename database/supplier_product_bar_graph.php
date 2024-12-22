<?php
include('connection.php');

// Query suppliers
$stmt = $conn->prepare('SELECT id, supplier_name FROM suppliers');
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = [];
$bar_chart_data = [];

// Query supplier product count
foreach ($rows as $row) {
    $id = $row['id'];
    $categories[] = $row['supplier_name'];

    // Query count
    $stmtCount = $conn->prepare(
        "SELECT COUNT(*) as p_count 
         FROM productsuppliers 
         WHERE productsuppliers.supplier = :supplier_id"
    );
    $stmtCount->bindParam(':supplier_id', $id, PDO::PARAM_INT);
    $stmtCount->execute();
    $countRow = $stmtCount->fetch(PDO::FETCH_ASSOC);

    $count = (int)$countRow['p_count']; // Cast count to integer
    $bar_chart_data[] = $count;
}

// Ensure data is accessible by the main page
return [
    'categories' => $categories,
    'bar_chart_data' => $bar_chart_data
];
