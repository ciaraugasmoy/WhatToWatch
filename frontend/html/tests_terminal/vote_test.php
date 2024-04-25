#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$requestUpvote = [
    'type' => 'set_vote',
    'username' => 'steve', // User who is upvoting
    'thread_id' => 9, // Thread ID to upvote
    'vote' => 'upvote', // Upvote action
];

$responseUpvote = $client->call($requestUpvote);

var_dump($responseUpvote).PHP_EOL;

$requestDeleteVote = [
    'type' => 'set_vote',
    'username' => 'steve', // User whose vote is to be deleted
    'thread_id' => 9, // Thread ID to delete vote from
    'vote' => 'unset', // Delete vote action
];

$responseDeleteVote = $client->call($requestDeleteVote);

var_dump($responseDeleteVote).PHP_EOL;

$requestDownvote = [
    'type' => 'set_vote',
    'username' => 'steve', // User who is upvoting
    'thread_id' => 9, // Thread ID to upvote
    'vote' => 'downvote', // Upvote action
];

$responseDownvote = $client->call($requestDownvote);
var_dump($responseDownvote).PHP_EOL;

?>