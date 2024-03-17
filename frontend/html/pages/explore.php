<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Home</title>
    <link rel="stylesheet" href="../css/global.css">
    <script src="../javascript/template.js"></script>
    <script src="../javascript/globalscript.js"></script>
</head>
<body>
<h2>Explore</h2>
<h3>Latest Discussions</h3>
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
$client = new RPCClient();

$offset=0;
$limit=5;

$request = array();
$request['type'] = "get_recent_threads";
$request['offset'] = $offset;
$request['limit'] = $limit;
$request['query'] = '';
$response = $client->call($request);
if ($response['status'] === 'success') {
    foreach ($response['threads'] as $thread){
        echo '<div class="thread">'
        .'<h4>'.$thread['title'].'</h4>'
        .'<p>'.$thread['body'].'</p>'
        .'<button value="'.$thread['id'].'">'.'See More'.'</button>'
        ;
    }
}

?>
</body>
</html>
