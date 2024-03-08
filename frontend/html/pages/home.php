<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; 

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

// Retrieve tokens from cookies
$access_token = isset($_COOKIE['access_token']) ? $_COOKIE['access_token'] : '';
$refresh_token = isset($_COOKIE['refresh_token']) ? $_COOKIE['refresh_token'] : '';
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

// Redirect to index if cookies are not set
if (empty($access_token) || empty($refresh_token) || empty($username)) {
    header("Location: ../index.html"); // Change the URL to your actual index page
    exit();
}
$request=array();
$request['type'] = "get_curated_providers";
$request['username'] = $username;
$response = $client->call($request);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo $username; ?> Home</title>
    <style scoped>
        .providers{
            display: flex;
        }
        .providers img{
            max-width: 30px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <h2>Home</h2>
    <section>
    <h3>MAKE EACH ICON AN ADD TO MY PROVIDERS BUTTON</h3>
    <div class="providers">
    <?php
        foreach ($response['watch_provider_info'] as $key => $value) {
            echo "<div class='provider'> <img src= 'https://image.tmdb.org/t/p/w500/" . $value['logo_path']. "'>" . "</p></div>";
        }
    ?>
    </div>
    </section>
</body>
</html>
