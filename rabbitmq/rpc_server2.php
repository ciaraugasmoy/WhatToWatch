#!/usr/bin/php

<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Connection details for the main RabbitMQ server
$mainConnection = new AMQPStreamConnection('localhost', 5672, 'mqadmin', 'mqadminpass','brokerHost');
$mainChannel = $mainConnection->channel();

// Declare a direct exchange for routing messages based on type
$mainChannel->exchange_declare('directExchange', 'direct', false, false, false);

// Declare a unique queue for the web server and db server
list($webQueue, ,) = $mainChannel->queue_declare('webQueue', false, false, true, false);
list($dbQueue, ,) = $mainChannel->queue_declare('dbQueue', false, false, true, false);
$mainChannel->queue_bind($webQueue, 'directExchange', 'webRoutingKey');
$mainChannel->queue_bind($dbQueue, 'directExchange', 'dbRoutingKey');

// Array to store correlation IDs and their associated promises
$correlationMap = [];
function forwardToDbServer($message, $correlationId)
{
    global $correlationMap;

    // Connection details for the database server RabbitMQ
    $dbConnection = new AMQPStreamConnection('dbServerHost', 5672, 'mqadmin', 'mqadminPass', 'brokerHost');
    $dbChannel = $dbConnection->channel();

    // Declare a direct exchange for routing messages based on type on the database server
    $dbChannel->exchange_declare('directExchange', 'direct', false, false, false);

    // Declare a queue on the database server
    list($dbQueue, ,) = $dbChannel->queue_declare('dbQueue', false, false, true, false);

    // Bind the database server queue to the exchange with a specific routing key (type)
    $dbChannel->queue_bind($dbQueue, 'directExchange', 'dbRoutingKey');

    // Create a promise for the response
    $promise = new \React\Promise\Deferred();

    // Store the promise in the correlation map
    $correlationMap[$correlationId] = $promise;

    // Publish the message to the database server with the specific routing key (type) and correlation ID
    $msg = new AMQPMessage($message, array('correlation_id' => $correlationId, 'reply_to' => $dbQueue));
    $dbChannel->basic_publish($msg, 'directExchange', 'dbRoutingKey');

    // Close the database channel and connection
    $dbChannel->close();
    $dbConnection->close();
}

function HANDLE_MESSAGE($request)
{
    global $correlationMap;

    if (!isset($request['type'])) {
        return array("status" => "error", "message" => "Unsupported message type");
    }

    switch ($request['type']) {
        case "login":
            // Generate a unique correlation ID
            $correlationId = uniqid();

            // Forward the 'login' request to the database server with the correlation ID
            forwardToDbServer(json_encode($request), $correlationId);

            // Wait for the response from the database server based on the correlation ID
            $responsePromise = $correlationMap[$correlationId]->promise();

            // Remove the promise from the correlation map (optional, depending on your requirements)
            unset($correlationMap[$correlationId]);

            // Wait for the promise to resolve and return the response
            return $responsePromise->wait();
        // ... (Other cases remain unchanged)
    }

    return array("status" => "error", "message" => "Server received request and processed but no case");
}

echo " [x] Awaiting RPC requests\n";

// Callback function for handling messages from the web server queue
$webCallback = function ($req) {
    $n = $req->getBody();
    HANDLE_MESSAGE(json_decode($n, true));
    $req->ack();
};

// Callback function for handling messages from the database server queue
$dbCallback = function ($req) {
    global $correlationMap;

    $n = $req->getBody();
    $correlationId = $req->get('correlation_id');

    // Check if the correlation ID is in the map
    if (isset($correlationMap[$correlationId])) {
        // Resolve the promise with the response
        $correlationMap[$correlationId]->resolve(json_decode($n, true));
    }

    $req->ack();
};

$mainChannel->basic_consume($webQueue, '', false, false, false, false, $webCallback);
$mainChannel->basic_consume($dbQueue, '', false, false, false, false, $dbCallback);

// Start consuming messages from both the web server and database server queues
try {
    while (count($mainChannel->callbacks)) {
        $mainChannel->wait();
    }
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}

// Main channel and connection should not be closed, as we need to keep listening for messages.
