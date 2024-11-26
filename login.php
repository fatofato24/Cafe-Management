<?php

// Start session.
session_start();
// If the user is already logged in, redirect to the dashboard
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('database/connection.php');

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = :username AND password = :password";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    // Check if any rows were returned
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);  // Fetch the user data as an associative array
        $_SESSION['user'] = $user;  // Store user data in session
        header('Location: dashboard.php');
        exit; // Prevent further script execution after redirection
    } else {
        $error_message = 'Please make sure that username and password are correct.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>IS LOGIN - Inventory Management System</title>
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
</head>
<body id="LoginBody">
    <?php if (!empty($error_message)) { ?>
    <div id="errorMessage">
        <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
    </div>
    <?php } ?>

    <div class="container">
        <div class="loginHeader">
            <h1>CIMS</h1>
            <h3>Cafe Inventory Management System</h3>
            <div class="LoginBody">
                <form action="login.php" method="POST">
                    <div class="loginInputsContainer">
                        <label for="username">Username</label>
                        <input placeholder="Username" name="username" id="username" type="text" required />
                    </div>
                    <div class="loginInputsContainer" style="position: relative;">
                        <label for="password">Password</label>
                        <input placeholder="Password" name="password" id="password" type="password" required />
                        <i id="togglePassword" class="fa fa-eye" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"></i> <!-- Eye Icon -->
                    </div>
                    <div class="loginButtonContainer">
                        <button type="submit">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script> <!-- Font Awesome CDN for Eye icon -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordField = document.querySelector('#password');
            const togglePassword = document.querySelector('#togglePassword');

            if (passwordField && togglePassword) {
                togglePassword.addEventListener('click', function () {
                    // Toggle the input type between 'password' and 'text'
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);

                    // Toggle the icon class between 'fa-eye' and 'fa-eye-slash'
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>

</body>
</html>
