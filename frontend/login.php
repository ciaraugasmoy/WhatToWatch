<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("rabbitMQDB.ini", "testServer");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request = array();
    $request['type'] = "login";
    $request['username'] = $_POST['username'];
    $request['password'] = $_POST['password'];
    $request['message'] = "test message";

    $response = $client->send_request($request);

    if (isset($response['status']) && $response['status'] == 'success' && isset($response['tokens'])) {
        // Set cookies to store tokens
        setcookie("access_token", $response['tokens']['access_token'], time() + 3600, "/");
        setcookie("refresh_token", $response['tokens']['refresh_token'], time() + (7 * 24 * 3600), "/");
        setcookie("username", $response['username'], time() + 3600, "/");
        // Redirect to a success page or do further processing
        header("Location: success.php");
        exit();
    } else {
        // Display an error message on the same page
        $errorMessage = "Login failed. Please check your username and password.";
    }
}

$payload = isset($errorMessage) ? json_encode(['error' => $errorMessage]) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
</head>
<body>

    <h2>Login</h2>

    <?php
    if (isset($errorMessage)) {
        echo '<p style="color: red;">' . $errorMessage . '</p>';
    }
    ?>

    <form action="" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>

</body>
</html>
