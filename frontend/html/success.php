<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

// Retrieve tokens from cookies
$access_token = isset($_COOKIE['access_token']) ? $_COOKIE['access_token'] : '';
$refresh_token = isset($_COOKIE['refresh_token']) ? $_COOKIE['refresh_token'] : '';
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

// Redirect to index if cookies are not set
if (empty($access_token) || empty($refresh_token) || empty($username)) {
    header("Location: index.php"); // Change the URL to your actual index page
    exit(); // Ensure that the script stops here
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ismytokenvalid'])) {
    $request = array();
    $request['type'] = "validate";
    $request['tokens']['access_token'] = $access_token;
    $request['tokens']['refresh_token'] = $refresh_token;
    $request['username'] = $username;

    $response = $client->send_request($request);

    if (isset($response['status']) && $response['status'] == 'success') {
        $validationMessage = "Token is valid!";
    } else {
        $validationMessage = "Token is not valid. Please log in again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Token Validation</title>
</head>
<body>

    <h2>Token Validation</h2>

    <?php
    if (isset($validationMessage)) {
        echo '<p style="color: ' . ($response['status'] == 'success' ? 'green' : 'red') . ';">' . $validationMessage . '</p>';
    }
    ?>

    <form action="" method="post">
        <input type="submit" name="ismytokenvalid" value="Check Token Validity">
    </form>

</body>
</html>
