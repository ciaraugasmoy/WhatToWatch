#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$request=array();
$request['type'] = "get_movie_details";
$request['movie_id'] = '95935';
$response = $client->call($request);
var_dump($response).PHP_EOL;

$request2 = array();
$request2['type'] = "get_user_review";
$request2['username'] = 'ciara';
$request2['movie_id'] = '95935';
$response2 = $client->call($request2);
var_dump($response2).PHP_EOL;

$request3 = array();
$request3['type'] = "post_user_review";
$request3['username'] = 'ciara';
$request3['movie_id'] = '95935';
$request3['rating'] = '4';
$request3['review'] = 'i love it but it is a 4 out of 5';
$response3 = $client->call($request3);
var_dump($response3).PHP_EOL;

$request4 = array();
$request4['type'] = "get_user_review";
$request4['username'] = 'ciara';
$request4['movie_id'] = '95935';
$response4 = $client->call($request4);
var_dump($response4);
?>