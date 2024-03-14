#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$username='ciara';
$username2='steve';

$request=array();
$request['type'] = "send_friend_request";
$request['username'] = $username;
$request['friend_username'] = $username2;
$response = $client->call($request);
echo ' [.] Response: '.PHP_EOL;
var_dump($response);
?>
