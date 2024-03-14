#!/usr/bin/php

<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client_rpc = new RPCClient();

$request = array();
$request['type'] = "login";
$request['username'] = "steve";
$request['password'] = "12345";
$request['message'] = "test message";

$response = $client_rpc->call($request); #ENTER WHAT U WANT (MESSAGE) INSIDE OF CALLss
echo ' [.] Response: ', $response, "\n";
var_dump($response);
