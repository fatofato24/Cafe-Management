<?php

// Access the data sent via POST using the correct keys
$user_id = (int) $_POST['user_id'];     // Access user_id correctly
$first_name = $_POST['f_name'];         // Access f_name correctly
$last_name = $_POST['l_name'];          // Access l_name correctly
$email = $_POST['email'];               // Access email correctly

// SQL query to update the user
try {
    $sql = "UPDATE users SET email=?, first_name=?, last_name=?, updated_at=? WHERE id=?";
    include('connection.php');

    // Execute the query with the data
    $conn->prepare($sql)->execute([$email, $first_name, $last_name, date('Y-m-d h:i:s'), $user_id]);

    // Send success response
    echo json_encode([
        'success' => true,
        'message' => $first_name . ' ' . $last_name . ' successfully updated.'
    ]);
} catch (PDOException $e) {
    // Error handling if the query fails
    echo json_encode([
        'success' => false,
        'message' => 'Error processing your request!'
    ]);
}
