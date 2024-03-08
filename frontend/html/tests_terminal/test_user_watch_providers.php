#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$username='ciara';
$watchproviderid=8;


$request=array();
$request['type'] = "get_providers";
$request['username'] = $username;
$request['watch_provider_id'] = $watchproviderid;
$response = $client->call($request);

foreach( $response['watch_provider_info'] as $key => $value) {
    var_dump($value);
}
?>