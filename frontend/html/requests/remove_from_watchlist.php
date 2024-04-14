<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

header('Content-Type: application/json');
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
$movie_id = $_GET['movie_id'];
// Check if tokens are not set
if (empty($username)) {
    echo json_encode(['status' => 'error', 'message'=>'cookie unset']);
}
    $request = [
        'type' => 'remove_from_watchlist',
        'username' => $username,
        'movie_id' => $movie_id,
    ];

    $response = $client->call($request);
    echo json_encode($response);
?>