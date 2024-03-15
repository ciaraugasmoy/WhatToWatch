#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$request2 = array();
$request2['type'] = "get_movie_providers";
$request2['username'] = 'steve';
$request2['movie_id'] = '27205';
$response2 = $client->call($request2);
var_dump($response2).PHP_EOL;

?>