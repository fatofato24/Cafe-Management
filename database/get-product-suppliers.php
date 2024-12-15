<?php

include('connection.php');

$id = $_GET['id'];

// Fetch suppliers using a parameterized query
$stmt = $conn->prepare("
    SELECT suppliers.id, suppliers.supplier_name 
    FROM suppliers
    INNER JOIN productsuppliers ON productsuppliers.supplier = suppliers.id
    WHERE productsuppliers.product = :product_id
");
$stmt->bindParam(':product_id', $id, PDO::PARAM_INT);
$stmt->execute();

$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($suppliers);  // Send suppliers as JSON

?>
