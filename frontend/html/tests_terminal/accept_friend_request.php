#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$username='steve';
$username2='ciara';

$request=array();
$request['type'] = "accept_friend_request";
$request['username'] = $username;
$request['friend_username'] = $username2;
$response = $client->call($request);
echo ' [.] Response: '.PHP_EOL;
var_dump($response);
?>
