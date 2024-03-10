<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
header('Content-Type: application/json');
$watchproviderid = $_POST['watch_provider_id'];

$request = array();
$request['type'] = "unset_provider";
$request['username'] = $username;
$request['watch_provider_id'] = $watchproviderid;
$response = $client->call($request);

header('Content-Type: application/json');
if ($response['status']=='success'){
    echo json_encode($response);
}
?>