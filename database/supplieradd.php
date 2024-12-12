<?php
session_start();

// Check if user is logged in and has a valid ID
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    die("User not logged in or invalid session.");
}

$user_id = $_SESSION['user']['id'];  // User ID from session

// Supplier form data
$supplier_name = $_POST['supplier_name'] ?? null;
$supplier_location = $_POST['supplier_location'] ?? null;
$email = $_POST['email'] ?? null;
$created_at = date('Y-m-d H:i:s');
$updated_at = date('Y-m-d H:i:s');

// Validate input
if (!$supplier_name || !$supplier_location || !$email) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'All fields are required.'
    ];
    header('location: ../supplier-add.php');
    exit;
}

try {
    include('connection.php');

    // Insert into suppliers table
    $sql = "INSERT INTO suppliers (supplier_name, supplier_location, email, created_by, created_at, updated_at) 
            VALUES (:supplier_name, :supplier_location, :email, :created_by, :created_at, :updated_at)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':supplier_name', $supplier_name);
    $stmt->bindParam(':supplier_location', $supplier_location);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':created_by', $user_id);
    $stmt->bindParam(':created_at', $created_at);
    $stmt->bindParam(':updated_at', $updated_at);

    $stmt->execute();

    // Response
    $response = [
        'success' => true,
        'message' => 'Supplier successfully added!'
    ];
} catch (PDOException $e) {
    // Handle error
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Store response and redirect
$_SESSION['response'] = $response;
header('location: ../supplier-add.php');
exit;
