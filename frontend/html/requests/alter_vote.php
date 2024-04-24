<?php
require_once '../client/client_rpc.php'; // Include the RPCClient class

$client = new RPCClient();


header('Content-Type: application/json');
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
// Check if tokens are not set
if (empty($username)) {
    echo json_encode(['status' => 'error', 'message'=>'cookie unset']);
}
// Get parameters from POST request
$thread_id = $_GET['thread_id'];
$vote = $_GET['vote'];

// Make RPC call to setVote method
$request = [
    'type' => 'set_vote',
    'username' => $username,
    'thread_id' => $thread_id,
    'vote' => $vote
];

$response = $client->call($request);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
