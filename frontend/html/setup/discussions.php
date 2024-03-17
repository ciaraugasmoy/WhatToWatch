#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

echo 'TESTING POSTING THREAD'.PHP_EOL;

for ($i = 1; $i <= 10; $i++) {
    $username = 'user' . $i;
    $body = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam ut elit lectus."; // Sample review text
    $title = 'My Post Title'.$i;
    $request3 = array();
    $request3['type'] = "post_thread";
    $request3['username'] = $username;
    $request3['movie_id'] = '';
    $request3['title'] = $title;
    $request3['body'] = $body;
   
    $response3 = $client->call($request3);
    var_dump($response3);
    if ($response3['status'] === 'success') {
        $thread_id = $response3['thread_id']; 
        $comments_num = rand(1, 5);
        for ($j = 1; $j <= $comments_num; $j++) { 
            echo 'TESTING POSTING comment'.PHP_EOL;
            $commenter = 'user' . $j; 
            $request1['type'] = "post_comment";
            $request1['username'] = $commenter;
            $request1['thread_id'] = $thread_id;
            $request1['body'] = 'This is a test comment'.$j; 
            $response1 = $client->call($request1);
            var_dump($response1);
        }
        echo 'TESTING get comments for post'.$thread_id.PHP_EOL;
        $request6 = array();
        $request6['type'] = "get_comments";
        $request6['thread_id'] = $thread_id;
        $response6 = $client->call($request6);
        var_dump($response6);
    } else {
        echo "Failed to post thread. Aborting comment posting loop." . PHP_EOL;
    } 

}

echo 'TESTING get RECENT THREAD'.PHP_EOL;
$request4 = array();
$request4['type'] = "get_recent_threads";
$request4['offset'] = 0;
$request4['limit'] = 5;
$request4['query'] = '';
$response4 = $client->call($request4);
var_dump($response4);


?>