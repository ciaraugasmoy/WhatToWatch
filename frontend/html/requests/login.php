<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '../client/client_rpc.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Assuming the RPCClient class is defined in the 'client_rpc.php' file
$client = new RPCClient();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request = array();
    $request['type'] = "login";
    $request['username'] = $_POST['username'];
    $request['password'] = $_POST['password'];

    $response = $client->call($request);

    if (isset($response['status']) && $response['status'] == 'success' && isset($response['tokens'])) {
        // Set cookies to store tokens
        setcookie("access_token", $response['tokens']['access_token'], time() + 3600, "/");
        setcookie("refresh_token", $response['tokens']['refresh_token'], time() + (7 * 24 * 3600), "/");
        setcookie("username",  $_POST['username'] , time() + 3600, "/");
        // Redirect to a success page or do further processing
        echo json_encode(['status' => 'success', 'redirect' => 'home.php']);
        exit();
    } else {
        // Return an error message
        echo json_encode(['status' => 'error', 'message' => 'Login failed. Please check your username and password.']);
        exit();
    }
}

// Return an empty response if the request is not a POST request
echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
