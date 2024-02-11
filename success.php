<?php
// Ensure that sessions are started
session_start();

// Check if the user is logged in (you might want to implement a more robust check)
if (!isset($_SESSION['username'])) {
    session_destroy();
    header("Location: loginRequest.php"); // Redirect to the login page if not logged in
    exit();
}
if (isset($_POST['logout'])) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("Location: loginRequest.php");
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
    <!-- Logout button -->
    <form method="post">
        <input type="submit" name="logout" value="Logout">
    </form>

</body>
</html>
