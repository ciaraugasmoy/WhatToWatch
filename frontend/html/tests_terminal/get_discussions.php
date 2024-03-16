#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

// Test get_recent_reviews
$request = array();
$request['type'] = "get_recent_reviews";
$request['username'] = 'ciara';
$request['movie_id'] = '27205';
$request['limit'] = 5;
$request['offset'] = 0;
$response = $client->call($request);
var_dump($response);

echo 'TESTING get USER REVIEWS'.PHP_EOL;
$request5 = array();
$request5['type'] = "get_user_reviews";
$request5['username'] = 'ciara';
$request5['friend_name'] = 'steve';
$response5 = $client->call($request5);
var_dump($response5);

echo 'TESTING POSTING THREAD'.PHP_EOL;
$request2 = array();
$request2['type'] = "post_thread";
$request2['username'] = 'steve';
$request2['title'] = 'Test Thread';
$request2['body'] = 'This is a test thread.';
$request2['movie_id'] = '27205';
$response2 = $client->call($request2);
var_dump($response2);

echo 'TESTING POSTING comment'.PHP_EOL;
$request1 = array();
$request1['type'] = "post_comment";
$request1['username'] = 'steve';
$request1['thread_id'] = '1';
$request1['body'] = 'This is a test comment.';
$response1 = $client->call($request1);
var_dump($response1);

echo 'TESTING get comment'.PHP_EOL;
$request3 = array();
$request3['type'] = "get_comments";
$request3['thread_id'] = '1';
$response3 = $client->call($request3);
var_dump($response3);

echo 'TESTING get RECENT THREAD'.PHP_EOL;
$request4 = array();
$request4['type'] = "get_recent_threads";
$request4['offset'] = 0;
$request4['limit'] = 5;
$request4['query'] = '';
$response4 = $client->call($request4);
var_dump($response4);


?>