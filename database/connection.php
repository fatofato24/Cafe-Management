<?php
$servername = "10.7.49.247";
$username = "cimsSF";
$password = "fatiboi";

try {
    $conn = new PDO("mysql:host=$servername;port=3306;dbname=inventory", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "Connected successfully";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
