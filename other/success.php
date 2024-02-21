<?php
// Check if the user is logged in TO DO, Cookies? or JSON Web Tokens ?
if (isset($_POST['logout'])) {
    header("Location: loginRequest.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Success</title>
</head>
<body>

    <h2>Welcome, username </h2>
    <p>You are now logged in.</p>
    <!-- Logout button -->
    <form method="post">
        <input type="submit" name="logout" value="Logout">
    </form>

</body>
</html>
