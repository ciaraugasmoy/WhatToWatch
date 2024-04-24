#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

// Subscribe 'steve' to thread ID 9
$requestSubscribe = [
    'type' => 'subscribe',
    'username' => 'steve',
    'thread_id' => 9,
];

$responseSubscribe = $client->call($requestSubscribe);
var_dump($responseSubscribe);
echo PHP_EOL;

// Unsubscribe 'steve' from thread ID 9
$requestUnsubscribe = [
    'type' => 'unsubscribe',
    'username' => 'steve',
    'thread_id' => 9,
];

$responseUnsubscribe = $client->call($requestUnsubscribe);
var_dump($responseUnsubscribe);
echo PHP_EOL;

// Subscribe 'steve' to thread ID 9 again
$requestSubscribeAgain = [
    'type' => 'subscribe',
    'username' => 'steve',
    'thread_id' => 9,
];

$responseSubscribeAgain = $client->call($requestSubscribeAgain);
var_dump($responseSubscribeAgain);
echo PHP_EOL;

?>
