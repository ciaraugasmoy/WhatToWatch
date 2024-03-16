<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

header('Content-Type: application/json');
$username2 = $_POST['friend_username'];
$access_token = isset($_COOKIE['access_token']) ? $_COOKIE['access_token'] : '';
$refresh_token = isset($_COOKIE['refresh_token']) ? $_COOKIE['refresh_token'] : '';
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
$friend_name = $_GET['username'];
// Check if tokens are not set
if (empty($access_token) || empty($refresh_token) || empty($username)) {
    echo json_encode(['status' => 'error']);
}
    $request = [
        'type' => 'validate',
        'tokens' => [
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
        ],
        'username' => $username,
    ];
    echo "Welcome to $username's profile!";

    $response = $client->call($request);
        $request = [
            'type' => 'get_user_reviews',
            'username' => $username,
            'friend_name' => $friend_name,
        ];
        $response = $client->call($request);
        echo json_encode($response);
?>