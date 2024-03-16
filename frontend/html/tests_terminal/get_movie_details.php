#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$signupRequest = array(
    'type' => 'signup',
    'username' => 'ciara',
    'password' => 'secure_password',
    'email' => 'ciara@example.com',
    'dob' => '1990-01-01' // Date of Birth in YYYY-MM-DD format
);
$signupResponse = $client->call($signupRequest);


$request=array();
$request['type'] = "get_movie_details";
$request['movie_id'] = '27205';
$response = $client->call($request);
var_dump($response).PHP_EOL;

$request2 = array();
$request2['type'] = "get_user_review";
$request2['username'] = 'ciara';
$request2['movie_id'] = '27205';
$response2 = $client->call($request2);
var_dump($response2).PHP_EOL;

$request3 = array();
$request3['type'] = "post_user_review";
$request3['username'] = 'ciara';
$request3['movie_id'] = '27205';
$request3['rating'] = '4';
$request3['review'] = 'i love it but it is a 4 out of 5';
$response3 = $client->call($request3);
var_dump($response3).PHP_EOL;

$request4 = array();
$request4['type'] = "get_user_review";
$request4['username'] = 'ciara';
$request4['movie_id'] = '27205';
$response4 = $client->call($request4);
var_dump($response4);

// Test get_recent_reviews
$request = array();
$request['type'] = "get_recent_reviews";
$request['username'] = 'ciara';
$request['movie_id'] = '27205';
$request['limit'] = 5;
$request['offset'] = 0;
$response = $client->call($request);
var_dump($response);

$request5 = array();
$request5['type'] = "get_user_reviews";
$request5['username'] = 'ciara';
$request5['friend_name'] = 'steve';
$response5 = $client->call($request5);
var_dump($response5);

$request5 = array();
$request5['type'] = "get_user_reviews";
$request5['username'] = 'ciara';
$request5['friend_name'] = 'ciara';
$response5 = $client->call($request5);
var_dump($response5);

?>