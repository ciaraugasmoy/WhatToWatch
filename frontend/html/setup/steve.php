#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__. '/../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

// Sending friend requests from steve to user1 to user5
for ($i = 1; $i <= 10; $i++) {
    $username = 'user' . $i;
    $request = array();
    $request['type'] = "send_friend_request";
    $request['username'] = 'steve';
    $request['friend_username'] = $username;
    $response = $client->call($request);
}

// Accepting friend requests from user1 to user3
for ($i = 1; $i <= 5; $i++) {
    $username = 'user' . $i;
    $request = [
        'type' => 'accept_friend_request',
        'username' => $username,
        'friend_username' => 'steve',
    ];
    $response = $client->call($request);
}
// users 20 to 25 send request to steve
for ($i = 20; $i <= 25; $i++) {
    $username = 'user' . $i;
    $request = array();
    $request['type'] = "send_friend_request";
    $request['username'] = $username;
    $request['friend_username'] = 'steve';
    $response = $client->call($request);
}

//10 users writing reviews with ratings
for ($i = 1; $i <= 10; $i++) {
    $username = 'user' . $i;
    $movie_id = 27205;
    $numstars = rand(1, 5); // Generating random rating from 1 to 5
    $review = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam ut elit lectus."; // Sample review text

    $request3 = array();
    $request3['type'] = "post_user_review";
    $request3['username'] = $username;
    $request3['movie_id'] = $movie_id;
    $request3['rating'] = $numstars;
    $request3['review'] = $review;
    $response3 = $client->call($request3);
}