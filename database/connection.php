<<<<<<< HEAD
 <?php
$servername = "localhost";
$username = "root";
$password = "";
=======
<?php
$servername = "10.7.49.247";
$username = "cimsSF";
$password = "fatiboi";
>>>>>>> 8e6f8cc4f7ce5d2aab56bbafa7c5c47a84fbd537

try {
    $conn = new PDO("mysql:host=$servername;dbname=inventory", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
