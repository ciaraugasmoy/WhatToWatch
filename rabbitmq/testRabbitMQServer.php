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

    // Perform login check
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedHashedPassword = $row['password'];

        // Use password_verify to check if the entered password matches the stored hashed password
        if (password_verify($password, $storedHashedPassword)) {
            $mysqli->close();
            $jwtToken = doGenerateToken($username);
            
            echo "user found" . PHP_EOL;
            echo "Login successful for username: $username\n";
            return array("status" => "success", "message" => "Login successful", "token" => $jwtToken);
        }
    }

    $mysqli->close();
    echo "user not found or incorrect password" . PHP_EOL;
    echo "Login failed for username: $username\n";

    return array("status" => "error", "message" => "Login failed");
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
    savePublicKeyToDatabase($username, $publicKey);
    return $jwtToken;
}
function savePublicKeyToDatabase($username, $publicKey)
{
    // Connect to the database (replace these credentials with your actual database credentials)
    $mysqli = new mysqli("localhost", "what2watchadmin", "what2watchpassword", "what2watch");
    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Get the user ID from the 'users' table based on the username
    $userIdQuery = "SELECT id FROM users WHERE username = '$username'";
    $result = $mysqli->query($userIdQuery);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userId = $row['id'];

        // Insert the public key into the 'public_keys' table
        $insertKeyQuery = "INSERT INTO public_keys (user_id, public_key) VALUES ($userId, '$publicKey')";
        $mysqli->query($insertKeyQuery);

        echo "Public key saved to the database for user: $username" . PHP_EOL;
    } else {
        echo "User not found in the database: $username" . PHP_EOL;
    }
    $mysqli->close();
}
function doValidate($jwtToken)
{
    // Connect to MySQL database
    echo "attempting to connect to db" . PHP_EOL;
    $mysqli = new mysqli("localhost", "what2watchadmin", "what2watchpassword", "what2watch");

    // Check connection
    if ($mysqli->connect_error) {
        echo "failed to connect" . PHP_EOL;
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Extract the username from the token
    try {
        $decodedToken = JWT::decode($jwtToken, null, false);
        $username = $decodedToken->username;
    } catch (\Exception $e) {
        // Token is invalid
        $mysqli->close();
        return array("status" => "error", "message" => "Invalid token");
    }

    // Query the database to get the public key associated with the user
    $publicKeyQuery = "SELECT public_key FROM public_keys WHERE user_id = (SELECT id FROM users WHERE username = '$username')";
    $result = $mysqli->query($publicKeyQuery);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $publicKey = $row['public_key'];

        // Verify the token using the public key
        $verificationResult = verifyToken($jwtToken, $publicKey);

        if ($verificationResult) {
            // Token is valid
            $mysqli->close();
            return array("status" => "success", "message" => "Token is valid");
        } else {
            // Token is invalid
            $mysqli->close();
            return array("status" => "error", "message" => "Token verification failed");
        }
    } else {
        // Public key not found in the database
        $mysqli->close();
        return array("status" => "error", "message" => "Public key not found for the user");
    }
}

//TO BE USED IN doValidate which should check the database for the public key
function verifyToken($jwtToken, $publicKey)
{
    try {
        $decoded = JWT::decode($jwtToken, $publicKey, array('RS256'));
        // Token is valid
        return true;
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
