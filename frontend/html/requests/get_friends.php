
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

header('Content-Type: application/json');
$watchproviderid = $_POST['watch_provider_id'];
$access_token = isset($_COOKIE['access_token']) ? $_COOKIE['access_token'] : '';
$refresh_token = isset($_COOKIE['refresh_token']) ? $_COOKIE['refresh_token'] : '';
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

// Check if tokens are not set
if (empty($access_token) || empty($refresh_token) || empty($username)) {
    echo json_encode(['status' => false]);
} else {
    $request = [
        'type' => 'validate',
        'tokens' => [
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
        ],
        'username' => $username,
    ];

    $response = $client->call($request);
    if ($response['status']!='success'){
        echo json_encode(['status' => false]);
    }
    $request = [
        'type' => 'get_friend_list',
        'username' => $username,
    ];
    $response = $client->call($request);
    if ($response['status']=='success'){
        echo json_encode($response);
    }
}
?>