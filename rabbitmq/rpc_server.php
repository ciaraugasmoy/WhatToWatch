#!/usr/bin/php

<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once 'UserHandler.php';
require_once 'UserDataHandler.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

	
$connection = new AMQPStreamConnection('localhost', 5672, 'mqadmin', 'mqadminpass','brokerHost'); #THE VALUES ARE RABBITMQ CREDs
$channel = $connection->channel();

$channel->queue_declare('testQueue', false, false, false, false);

function HANDLE_MESSAGE($request)
{
    echo "received request" . PHP_EOL;
    var_dump($request);

    if (!isset($request['type'])) {
        return array("status" => "error", "message" => "Unsupported message type");
    }

    $userHandler = new UserHandler();

    switch ($request['type']) {
        case "login":
            return $userHandler->doLogin($request['username'], $request['password']);
        case "signup":
            return $userHandler->doSignup($request['username'], $request['password'], $request['email'], $request['dob']);
        case "validate":
            return $userHandler->doValidate($request['username'], $request['tokens']);
        case "get_providers":
            $userDataHandler= new UserDataHandler();
            return $userDataHandler->getWatchProviders($request['username']);
        //case "update_providers":
    }

    return array("status" => "error", "message" => "Server received request and processed but no case");
}


echo " [x] Awaiting RPC requests\n";
$callback = function ($req) {
    $n = $req->getBody();
    $result = HANDLE_MESSAGE(json_decode($n, true)); // Decode JSON string to associative array

    $msg = new AMQPMessage(
        json_encode($result), // Encode the result array as JSON
        array('correlation_id' => $req->get('correlation_id'))
    );

    $req->getChannel()->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );
    $req->ack();
};

$channel->basic_qos(null, 1, false);
$channel->basic_consume('testQueue', '', false, false, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}

$channel->close();
$connection->close();
?>
