<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();
$threadId = isset($_GET['thread_id']) ? $_GET['thread_id'] : '';
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
$body = $_POST['body'];

$request = array(
    'type' => 'post_comment',
    'thread_id' => $threadId,
    'username' => $username,
    'body' => $body,
);

$response = $client->call($request);

// Prepare the response as JSON
header('Content-Type: application/json');
echo json_encode($response);