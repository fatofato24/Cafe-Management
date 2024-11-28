<?php
try {
    include('connection.php');

    $user_id = (int) $_POST['user_id'];
    $first_name = trim($_POST['f_name']);
    $last_name = trim($_POST['l_name']);
    $email = trim($_POST['email']);

    $sql = "UPDATE users SET email = ?, first_name = ?, last_name = ?, updated_at = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$email, $first_name, $last_name, date('Y-m-d H:i:s'), $user_id])) {
        echo json_encode([
            'success' => true,
            'message' => "$first_name $last_name successfully updated."
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error updating user in the database.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Exception: ' . $e->getMessage()
    ]);
}
