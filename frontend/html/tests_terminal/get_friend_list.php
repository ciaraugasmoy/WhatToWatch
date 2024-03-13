#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$username='ciara';

$request=array();
$request['type'] = "get_friend_list";
$request['username'] = $username;
$response = $client->call($request);
echo ' [.] Response: '.PHP_EOL;
var_dump($response);
?>