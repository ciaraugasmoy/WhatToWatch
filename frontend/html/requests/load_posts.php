<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Assuming you have an instance of the RPCClient class
$client = new RPCClient();

// Get offset and limit from the GET parameters
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;

// Construct a request to retrieve posts with the given offset and limit
$request = array(
    'type' => 'get_recent_threads',
    'offset' => $offset,
    'limit' => $limit,
    'query' => '' // You might need to add a search query here if needed
);

// Make a call to your RPC server to retrieve the posts
$response = $client->call($request);

// Prepare the response as JSON
header('Content-Type: application/json');

if ($response['status'] === 'success') {
    // Return the threads as JSON
    echo json_encode(array('status' => 'success', 'threads' => $response['threads']));
} else {
    // If something went wrong, return an error message
    echo json_encode(array('status' => 'error', 'message' => 'Failed to retrieve posts.'));
}
?>
