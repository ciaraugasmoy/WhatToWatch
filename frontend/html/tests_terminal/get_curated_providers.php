#!/usr/bin/php

<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client_rpc = new RPCClient();

$request = array();
$request['type'] = "get_curated_providers";
$request['username'] = 'ciara';
$response = $client_rpc->call($request);
echo ' [.] Response: '.PHP_EOL;
var_dump($response);