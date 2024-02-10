<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$msg = "test message";

$request = array();
$request['type'] = "Login";
$request['username'] = $_POST['username'];  // Retrieve the username from the POST request
$request['password'] = $_POST['password'];  // Retrieve the password from the POST request
$request['message'] = $msg;

// still unsure the big diff btwn send_request vs publish
$response = $client->send_request($request);
//$response = $client->publish($request);

$payload = json_encode($response);
echo $payload;
?>
