#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();
header('Content-Type: application/json');
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
// Check if tokens are not set
if (empty($username)) {
    echo json_encode(['status' => 'error', 'message'=>'cookie unset']);
}
// Get parameters from POST request
$thread_id = $_GET['thread_id'];
$subscribe_request= $_GET['subscribe_status'];
// Subscribe 'steve' to thread ID 9
$request = [
    'type' => $subscribe_request,
    'username' => $username,
    'thread_id' => $thread_id,
];

$response = $client->call($request);
header('Content-Type: application/json');
echo json_encode($response);

?>
