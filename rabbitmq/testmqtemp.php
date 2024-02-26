#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

// Queries
$loginCheckQuery = "SELECT * FROM users WHERE username = ?";
$signupCheckQuery = "SELECT * FROM users WHERE username = ?";
$signupQuery = "INSERT INTO users (username, password) VALUES (?, ?)";
$insertPrivateKeyQuery = "INSERT INTO private_keys (user_id, private_key) VALUES ((SELECT id FROM users WHERE username = ?), ?)";
$selectPrivateKeyQuery = "SELECT private_key FROM private_keys WHERE user_id = (SELECT id FROM users WHERE username = ?)";

function doLogin($username, $password)
{
    require 'connection.php';

    $username = $mysqli->real_escape_string($username);

    $result = $mysqli->query($loginCheckQuery);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedHashedPassword = $row['password'];

        if (password_verify($password, $storedHashedPassword)) {
            $mysqli->close();
            $jwtTokens = doGenerateTokens($username);
            echo "Login successful for username: $username\n";
            return array("status" => "success", "message" => "Login successful", "tokens" => $jwtTokens);
        }
    }

    $mysqli->close();
    echo "user not found or incorrect password" . PHP_EOL;
    echo "Login failed for username: $username\n";

    return array("status" => "error", "message" => "Login failed");
}

function doSignup($username, $password)
{
    require 'connection.php';

    $username = $mysqli->real_escape_string($username);
    $password = $mysqli->real_escape_string($password);

    $checkResult = $mysqli->query($signupCheckQuery);
    if ($checkResult->num_rows > 0) {
        echo "username already exists" . PHP_EOL;
        $mysqli->close();
        return array("status" => "error", "message" => "Username already exists");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $signupResult = $mysqli->query($signupQuery);

    if ($signupResult) {
        echo "user signed up successfully" . PHP_EOL;
        $mysqli->close();
        return array("status" => "success", "message" => "Signup successful");
    } else {
        echo "signup failed" . PHP_EOL;
        $mysqli->close();
        return array("status" => "error", "message" => "Signup failed");
    }
}

$server = new rabbitMQServer("rabbitMQDB.ini", "testServer");

echo "testRabbitMQServer BEGIN" . PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END" . PHP_EOL;
exit();
?>
