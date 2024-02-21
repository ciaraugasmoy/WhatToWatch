#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

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
        $mysqli->close();
        $jwtToken = doGenerateToken($username);
        
        echo "user found" . PHP_EOL;
        echo "Login successful for username: $username\n";
        return array("status" => "success", "message" => "Login successful", "token" => $jwtToken);
    } else {
        $mysqli->close();
        echo "user not found" . PHP_EOL;
        echo "Login failed for username: $username\n";
        
        return array("status" => "error", "message" => "Login failed");
    }
}
function doSignup($username, $password)
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

    // Check if the username or email is already registered
    $checkQuery = "SELECT * FROM users WHERE username = '$username' ";
    $checkResult = $mysqli->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        echo "username already exists" . PHP_EOL;
        $mysqli->close();
        return array("status" => "error", "message" => "Username already exists");
    }

    // Perform signup
    $signupQuery = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
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

function doGenerateToken($username)
{
    // Generate an asymmetric key pair
    $privateKey = openssl_pkey_new(array(
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ));

    // Get the private key as a string
    openssl_pkey_export($privateKey, $privateKeyString);

    // Set thesecret key for encoding the token (use the private key for signing)
    $secretKey = $privateKeyString;

    // Defne the payload of the token
    $tokenPayload = array(
        "username" => $username,
        "iat" => time(),         // Issued at time
        "exp" => time() + 3600   // Token expiration time 1 hour 
    );

    // Generate the token using the private key for signing
    $jwtToken = JWT::encode($tokenPayload, $secretKey, 'RS256');
    $publicKey = openssl_pkey_get_details($privateKey)['key'];
    
    return $jwtToken;
}

//TO BE USED IN doValidate which should check the database for the public key
function verifyToken($jwtToken, $publicKey)
{
    try {
        $decoded = JWT::decode($jwtToken, $publicKey, array('RS256'));
        // Token is valid
        return $decoded;
    } catch (\Exception $e) {
        // Token is invalid
        return false;
    }
}

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
