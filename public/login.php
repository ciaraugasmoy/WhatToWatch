<?php
require_once __DIR__ . '/vendor/autoload.php'; // Adjust the path accordingly

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

if (!isset($_POST)) {
    $msg = "NO POST MESSAGE SET, POLITELY FUCK OFF";
    echo json_encode($msg);
    exit(0);
}

$request = $_POST;
$response = "unsupported request type, politely FUCK OFF";

switch ($request["type"]) {
    case "login":
        // Publish the message to RabbitMQ
        publishToRabbitMQ(json_encode($request));
        $response = "login, yeah we can do that";
        break;
}

echo json_encode($response);
exit(0);

function publishToRabbitMQ($message)
{
    $rabbitmq_host = 'testHost';
    $rabbitmq_port = 5672;
    $rabbitmq_username = 'steve';
    $rabbitmq_password = 'password';

    $connection = new AMQPStreamConnection($rabbitmq_host, $rabbitmq_port, $rabbitmq_username, $rabbitmq_password);
    $channel = $connection->channel();

    $channel->queue_declare('testQueue', false, true, false, false);

    $msg = new AMQPMessage($message);
    $channel->basic_publish($msg, '', 'janemyqueuename');

    $channel->close();
    $connection->close();
}

?>	
