#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$username='steve';


$request=array();
$request['type'] = "add_to_watchlist";
$request['username'] = $username;
$request['movie_id'] = '1022789';
$response = $client->call($request);
echo ' [.] Response: '.PHP_EOL;
var_dump($response);

$request2=array();
$request2['type'] = "get_watchlist";
$request2['username'] = $username;
$response2 = $client->call($request2);
echo ' [.] Response2: '.PHP_EOL;
var_dump($response2);
?>
