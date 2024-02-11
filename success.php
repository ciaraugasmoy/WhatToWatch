<?php
// Ensure that sessions are started
session_start();

// Check if the user is logged in (you might want to implement a more robust check)
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit();
}

// Get the username from the session
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Success</title>
</head>
<body>

    <h2>Welcome, <?php echo $username; ?>!</h2>
    <p>You are now logged in.</p>

    <!-- Add additional content or links as needed -->

</body>
</html>
