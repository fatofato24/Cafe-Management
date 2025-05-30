<?php

include('connection.php');

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM suppliers WHERE id=?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch products.
$stmt = $conn->prepare("
    SELECT product_name, products.id
    FROM products, productsuppliers
    WHERE productsuppliers.supplier = ?
    AND productsuppliers.product = products.id
");
$stmt->execute([$id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$row['products'] = array_column($products, 'id');

echo json_encode($row);