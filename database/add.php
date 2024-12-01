<?php
// Start the session.
session_start();

// Capture the table mappings.
include('table_columns.php');

$table_name = $_SESSION['table'];
$columns = $table_columns_mapping[$table_name];

$db_arr = [];
$user = $_SESSION['user'];

foreach ($columns as $column) {
    if (in_array($column, ['created_at', 'updated_at'])) {
        $value = date('Y-m-d H:i:s');
    } elseif ($column == 'created_by') {
        $value = $user['id'];
    } elseif ($column == 'password') {
        $value = password_hash($_POST[$column], PASSWORD_DEFAULT);
    } else {
        $value = isset($_POST[$column]) ? $_POST[$column] : '';
    }

    $db_arr[$column] = $value;
}

$table_properties = implode(", ", array_keys($db_arr));
$table_placeholders = implode(",", array_keys($db_arr));




//for users data
//$first_name = $_POST['first_name'];
//$last_name = $_POST['last_name'];
//$email = $_POST['email'];
//$password = $_POST['password'];

$encrypted = password_hash($password, PASSWORD_DEFAULT);

// Adding the record.
try {
    $sql = "INSERT INTO $table_name($table_properties) 
    VALUES ($table_placeholders)";

include('connection.php');


    $stmt =$conn->prepare($sql);
    $stmt->execute($db_arr);

    $response = [
        'success' => true,
        'message' => 'successfully added to the system!'
    ];
} catch (PDOException $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

$_SESSION['response'] = $response;
header('location: ../' .$_SESSION['redirect_to']);
exit;
?>
