<?php

// Start session.
session_start();
// If the user is already logged in, redirect to the dashboard
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('database/connection.php');

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE users.email = :username AND users.password = :password";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    // Set fetch mode and debug output.
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    // Debugging: var_dump to check results, remove in production
    var_dump($stmt->fetchAll());
    // Remove die after debugging
    // die;

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetchAll()[0];
        $_SESSION['user'] = $user;
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
                    <div class="loginInputsContainer">
                        <label for="password">Password</label>
                        <input placeholder="Password" name="password" id="password" type="password" required />
                    </div>
                    <div class="loginButtonContainer">
                        <button type="submit">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
