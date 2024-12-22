<?php

include('connection.php');

$stmt = $conn->prepare("SELECT * FROM products ORDER BY product_name ASC");
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);

return $stmt->fetchAll();
