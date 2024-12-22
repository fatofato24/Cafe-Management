<?php
require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplierId = $_POST['supplier_id'];
    $supplierName = $_POST['supplier_name'];
    $supplierLocation = $_POST['location'];
    $email = $_POST['email'];

    if (!empty($supplierId) && !empty($supplierName) && !empty($supplierLocation) && !empty($email)) {
        try {
            $query = "UPDATE suppliers 
                      SET supplier_name = :supplier_name, supplier_location = :location, email = :email 
                      WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $supplierId);
            $stmt->bindParam(':supplier_name', $supplierName);
            $stmt->bindParam(':location', $supplierLocation);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Supplier updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update supplier']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
    }
}
?>
