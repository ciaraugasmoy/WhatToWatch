#!/usr/bin/php

<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


$client_rpc = new RPCClient();

// Test Signup
$signupRequest = array(
    'type' => 'signup',
    'username' => 'john_doe',
    'password' => 'secure_password',
    'email' => 'john@example.com',
    'dob' => '1990-01-01' // Date of Birth in YYYY-MM-DD format
);

$signupResponse = $client_rpc->call($signupRequest);

// Print the response array
echo ' [.] Signup Response: ';
print_r($signupResponse);
echo "\n";
