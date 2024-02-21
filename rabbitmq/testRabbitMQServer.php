#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($username, $password)
{
    // Connect to MySQL database
    echo "attempting to connect to db" . PHP_EOL;
    $mysqli = new mysqli("localhost", "what2watchadmin", "what2watchpassword", "what2watch");

    // Check connection
    if ($mysqli->connect_error) {
        echo "failed to connect" . PHP_EOL;
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Sanitize input to prevent SQL injection
    $username = $mysqli->real_escape_string($username);
    $password = $mysqli->real_escape_string($password);

    // Perform login check
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        echo "user found" . PHP_EOL;
        $mysqli->close();
        echo "Login successful for username: $username\n";
        return array("status" => "success", "message" => "Login successful");
    } else {
        echo "user not found" . PHP_EOL;
        $mysqli->close();
        echo "Login failed for username: $username\n";
        return array("status" => "error", "message" => "Login failed");
    }
}

function requestProcessor($request)
{
    echo "received request" . PHP_EOL;
    var_dump($request);
    if (!isset($request['type'])) {
        return array("status" => "error", "message" => "Unsupported message type");
    }
    switch ($request['type']) {
        case "login":
            return doLogin($request['username'], $request['password']);
        case "validate_session":
            return doValidate($request['sessionId']);
    }
    return array("status" => "error", "message" => "Server received request and processed");
}

$server = new rabbitMQServer("rabbitMQDB.ini", "testServer");

echo "testRabbitMQServer BEGIN" . PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END" . PHP_EOL;
exit();
?>
