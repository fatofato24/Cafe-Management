<?php

include('connection.php');
$statuses = ['pending', 'complete', 'incomplete'];
$results = [];

// Loop through statuses and query
foreach ($statuses as $status) {
  $stmt = $conn->prepare("SELECT COUNT(*) as status_count FROM order_product WHERE order_product.status = :status");
  $stmt->execute([':status' => $status]);
  $row = $stmt->fetch();
  $count = $row['status_count'];

  // var_dump($count); 
  // die;

  $results[$status] = $count;
}

var_dump($results);
die;

?>