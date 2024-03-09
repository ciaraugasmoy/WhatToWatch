<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$request = array();
$request['type'] = "get_curated_providers";
$request['username'] = $username;
$response = $client->call($request);

header('Content-Type: application/json');
if ($response['status']=='success'){
    echo json_encode($response);
}
?>
