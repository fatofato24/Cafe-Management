<?php

include('connection.php');

$stmt = $conn->prepare("SELECT * FROM suppliers ORDER BY supplier_name ASC");
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);

return $stmt->fetchAll();
