<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


$client = new RPCClient();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request = array();
    $request['type'] = "signup";
    $request['username'] = $_POST['username'];
    $request['password'] = $_POST['password'];
    $request['email'] = $_POST['email'];
    $request['dob'] = $_POST['dob'];
    $request['message'] = "test message";

    $response = $client->call($request);

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

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required><br><br>

        <input type="submit" value="Signup">
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>

</body>
</html>
