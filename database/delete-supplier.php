<?php
require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplierId = $_POST['supplier_id'];

    if (!empty($supplierId)) {
        try {
            $query = "DELETE FROM suppliers WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $supplierId);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Supplier deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete supplier']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid supplier ID']);
    }
}
?>
