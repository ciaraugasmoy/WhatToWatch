<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("rabbitMQDB.ini", "testServer");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request = array();
    $request['type'] = "signup";
    $request['username'] = $_POST['username'];
    $request['password'] = $_POST['password'];
    $request['message'] = "test message";

    $response = $client->send_request($request);

    if (isset($response['status']) && $response['status'] == 'success') {
        // Redirect to login page upon successful signup
        header("Location: login.php");
        exit();
    } else {
        // Display an error message on the same page
        $errorMessage = "Signup failed. Please try again.";
    }
}

$payload = isset($errorMessage) ? json_encode(['error' => $errorMessage]) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Form</title>
</head>
<body>

    <h2>Signup</h2>

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

        <input type="submit" value="Signup">
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>

</body>
</html>
