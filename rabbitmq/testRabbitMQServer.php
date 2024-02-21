#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

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
            $jwtTokens = 'in development';
            echo "user found" . PHP_EOL;
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
    // Sanitize input to prevent SQL injection
    $username = $mysqli->real_escape_string($username);
    $password = $mysqli->real_escape_string($password);

    // Check if the username or email is already registered
    $checkQuery = "SELECT * FROM users WHERE username = '$username' ";
    $checkResult = $mysqli->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        echo "username already exists" . PHP_EOL;
        $mysqli->close();
        return array("status" => "error", "message" => "Username already exists");
    }

    // Perform signup
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $signupQuery = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";
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


// function doGenerateTokens($username)
// {
//     // Check if the secret key exists for the user
//     $existingSecretKey = getSecretKeyFromDatabase($username);

//     // If the secret key doesn't exist, generate and store a new one
//     if (empty($existingSecretKey)) {
//         $newSecretKey = generateSecretKey($username);
//     } else {
//         $newSecretKey = $existingSecretKey;
//     }
//     // Use the secret key to generate tokens
//     $tokens = generateTokens($username, $newSecretKey);

//     return $tokens;
// }
// function generateTokens($username) {
//     // Retrieve your secret key from a secure location
//     $secretKey = getSecretKeyFromDatabase();

//     // Define the payload of the access token
//     $issuedAt = time();
//     $accessTokenExpiration = $issuedAt + 3600; // Access token validity (1 hour)
//     $refreshTokenExpiration = $issuedAt + (7 * 24 * 3600); // Refresh token validity (7 days)

//     $accessTokenPayload = [
//         //"iss" => "https://what2watch.com",
//         "iat" => $issuedAt,
//         "exp" => $accessTokenExpiration,
//         "username" => $username
//     ];

//     // Encode the access token payload to get the access token
//     $accessToken = JWT::encode($accessTokenPayload, $secretKey, 'HS256');

//     // Define the payload of the refresh token
//     $refreshTokenPayload = [
//        // "iss" => "https://what2watch.com",
//         "iat" => $issuedAt,
//         "exp" => $refreshTokenExpiration,
//         "username" => $username
//     ];

//     // Encode the refresh token payload to get the refresh token
//     $refreshToken = JWT::encode($refreshTokenPayload, $secretKey, 'HS256');

//     return [
//         "access_token" => $accessToken,
//         "refresh_token" => $refreshToken,
//         "access_token_expiration" => $accessTokenExpiration
//     ];
// }

// function getSecretKeyFromDatabase($username)
// {
//     require 'connection.php';
//     $username = $mysqli->real_escape_string($username);
//     //fix
//     $query = "SELECT secret_key FROM users WHERE username = '$username'";
//     $result = $mysqli->query($query);
    
//     if ($result->num_rows > 0) {
//         $row = $result->fetch_assoc();
//         return $row['secret_key'];
//     }
//     $mysqli->close();
//     return null;
// }

// function generateSecretKey($username)
// {
//     $newSecretKey = bin2hex(random_bytes(32));
//     require 'connection.php';

//     // Sanitize input to prevent SQL injection
//     $username = $mysqli->real_escape_string($username);
//     $newSecretKey = $mysqli->real_escape_string($newSecretKey);

//     //fix
//     $updateQuery = "secret_key = '$newSecretKey' WHERE username = '$username'";
//     $mysqli->query($updateQuery);
//     $mysqli->close();
//     return $newSecretKey;
// }

// Add the new case for signup in the requestProcessor function
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
        case "signup": // Add this case for signup
            return doSignup($request['username'], $request['password']);
        case "validate":
            return doValidate($request['token']);
    }
    return array("status" => "error", "message" => "Server received request and processed");
}


$server = new rabbitMQServer("rabbitMQDB.ini", "testServer");

echo "testRabbitMQServer BEGIN" . PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END" . PHP_EOL;
exit();
?>
