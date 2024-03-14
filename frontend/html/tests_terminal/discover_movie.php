#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$request=array();
$request['type'] = "discover_movie";
$request['query'] = 'space';
$request['page'] = '1';
$request['adult_bool'] = 'false';
$response = $client->call($request);
var_dump($response)
?>