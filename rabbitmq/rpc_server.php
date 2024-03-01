#!/usr/bin/php

<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
	
$connection = new AMQPStreamConnection('localhost', 5672, 'mqadmin', 'mqadminpass','brokerHost'); #THE VALUES ARE RABBITMQ CREDs
$channel = $connection->channel();

$channel->queue_declare('testQueue', false, false, false, false);

function doLogin($username, $password)
{
    require 'connection.php';
    // Sanitize input to prevent SQL injection
    $username = $mysqli->real_escape_string($username);

    // Perform login check
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedHashedPassword = $row['password'];

        // Use password_verify to check if the entered password matches the stored hashed password
        if (password_verify($password, $storedHashedPassword)) {
            $mysqli->close();
            //$jwtTokens = doGenerateTokens($username);
            echo "Login successful for username: $username\n";
            return array("status" => "success", "message" => "Login successful", "tokens" => "placeholder");
        }
    }

    $mysqli->close();
    echo "user not found or incorrect password" . PHP_EOL;
    echo "Login failed for username: $username\n";

    return array("status" => "error", "message" => "Login failed");
}

function HANDLE_MESSAGE($request)
{
	echo "received request" . PHP_EOL;
    var_dump($request);
    if (!isset($request['type'])) {
        return array("status" => "error", "message" => "Unsupported message type");
    }
    switch ($request['type']) {
        case "login":
            return doLogin($request['username'], $request['password']);
        // case "signup": // Add this case for signup
        //     return doSignup($request['username'], $request['password'],$request['email'],$request['dob']);
        // case "validate":
        //     return doValidate($request['username'], $request['tokens']);
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
