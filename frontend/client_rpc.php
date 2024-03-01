#!/usr/bin/php

<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RPCClient
{
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            'localhost',
            5672,
            'mqadmin',
	    'mqadminpass',
	    'brokerHost'
        );
        $this->channel = $this->connection->channel();
        list($this->callback_queue, ,) = $this->channel->queue_declare(
            "",
            false,
            false,
            true,
            false
        );
        $this->channel->basic_consume(
            $this->callback_queue,
            '',
            false,
            true,
            false,
            false,
            array(
                $this,
                'onResponse'
            )
        );
    }

    public function onResponse($rep)
    {
        if ($rep->get('correlation_id') == $this->corr_id) {
            $this->response = json_decode($rep->body, true); // Decode JSON string to associative array
        }
    }    

    public function call($request)
    {
        $this->response = null;
        $this->corr_id = uniqid();
    
        $msg = new AMQPMessage(
            json_encode($request),
            array(
                'correlation_id' => $this->corr_id,
                'reply_to' => $this->callback_queue
            )
        );
        $this->channel->basic_publish($msg, '', 'testQueue');
        while (!$this->response) {
            $this->channel->wait();
        }
    
        return $this->response; // Return the response as it is, already decoded as an array
    }
    
}
?>
