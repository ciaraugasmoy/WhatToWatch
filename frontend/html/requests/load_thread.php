<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Create a new RPC client instance
$client = new RPCClient();

// Get the thread ID from the GET request parameters
$thread_id = $_GET['thread_id'];

// Prepare the RPC request to fetch the thread data
$request = array(
    'type' => 'get_thread',
    'thread_id' => $thread_id
);

// Call the RPC server to fetch the thread data
$response = $client->call($request);

// Output the response (thread data) to be consumed by the JavaScript
echo json_encode($response);
?>
