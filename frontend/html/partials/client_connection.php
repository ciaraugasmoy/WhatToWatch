<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../client/client_rpc.php'; 

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();
?>