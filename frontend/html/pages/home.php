<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

// Retrieve tokens from cookies
$access_token = isset($_COOKIE['access_token']) ? $_COOKIE['access_token'] : '';
$refresh_token = isset($_COOKIE['refresh_token']) ? $_COOKIE['refresh_token'] : '';
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

// Redirect to index if cookies are not set
if (empty($access_token) || empty($refresh_token) || empty($username)) {
    header("Location: ../index.html"); // Change the URL to your actual index page
    exit();
}
$request=array();
$request['type'] = "get_providers";
$request['username'] = $username;
$response = $client->call($request);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo $username; ?> Home</title>
</head>
<body>

    <h2>Home</h2>
    <p><?php
    var_dump($response);
    $response= json_decode($response);

    foreach($response as $key => $value) {
    echo $key . " => " . $value . "<br>";
    }
    ?></p>
</body>
</html>
