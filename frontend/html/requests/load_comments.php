<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Assuming you have an instance of the RPCClient class
$client = new RPCClient();

// Get thread ID from the GET parameters
$threadId = isset($_GET['thread_id']) ? $_GET['thread_id'] : '';

// Construct a request to retrieve comments for the given thread ID
$request = array(
    'type' => 'get_comments',
    'thread_id' => $threadId
);

// Make a call to your RPC server to retrieve the comments
$response = $client->call($request);

// Prepare the response as JSON
header('Content-Type: application/json');

if ($response['status'] === 'success') {
    // Return the comments as JSON
    echo json_encode(array('status' => 'success', 'comments' => $response['comments']));
} else {
    // If something went wrong, return an error message
    echo json_encode(array('status' => 'error', 'message' => 'Failed to retrieve comments.'));
}
?>
