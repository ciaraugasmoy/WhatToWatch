<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ .'/../client/client_rpc.php'; 

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

    $response = $client->call($request);

    if (isset($response['status']) && $response['status'] == 'success') {
        // Return a JSON response with redirect URL
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'redirect' => 'login.php']);
        exit();
    } else {
        // Return a JSON response with error message
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'error' => 'Signup failed. Please try again.']);
        exit();
    }
}
?>