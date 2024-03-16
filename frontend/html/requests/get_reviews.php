<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

header('Content-Type: application/json');

$access_token = isset($_COOKIE['access_token']) ? $_COOKIE['access_token'] : '';
$refresh_token = isset($_COOKIE['refresh_token']) ? $_COOKIE['refresh_token'] : '';
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
// Check if tokens are not set
if (empty($access_token) || empty($refresh_token) || empty($username)) {
    echo json_encode(['status' => 'error', 'message' => 'Tokens are missing']);
    exit;
}

$request = array();
$request['type'] = "get_recent_reviews";
$request['username'] = $username;
$request['movie_id'] = 27205;
$request['limit'] = 5;
$request['offset'] = 0;

$response = $client->call($request);
echo json_encode($response);
?>
