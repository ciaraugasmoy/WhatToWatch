#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$username='ciara';
$watchproviderid=7;


$request=array();
$request['type'] = "discover_movie";
$request['username'] = $username;
$response = $client->call($request);
var_dump($response)
?>