<?php

require_once('get_host_info.inc');
require_once('vendor/autoload.php'); // Include the autoload file for the AMQP library

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exchange\AMQPExchange;
use PhpAmqpLib\Queue\AMQPQueue;

class RabbitMQServer
{
    private $machine = "";
    public $BROKER_HOST;
    private $BROKER_PORT;
    private $USER;
    private $PASSWORD;
    private $VHOST;
    private $exchange;
    private $queue;
    private $routing_key = '*';
    private $exchange_type = "topic";
    private $auto_delete = true;

    function __construct($machine, $server = "rabbitMQ")
    {
        $this->machine = getHostInfo(array($machine));
        $this->BROKER_HOST = $this->machine[$server]["BROKER_HOST"];
        $this->BROKER_PORT = $this->machine[$server]["BROKER_PORT"];
        $this->USER = $this->machine[$server]["USER"];
        $this->PASSWORD = $this->machine[$server]["PASSWORD"];
        $this->VHOST = $this->machine[$server]["VHOST"];

        if (isset($this->machine[$server]["EXCHANGE_TYPE"])) {
            $this->exchange_type = $this->machine[$server]["EXCHANGE_TYPE"];
        }

        if (isset($this->machine[$server]["AUTO_DELETE"])) {
            $this->auto_delete = $this->machine[$server]["AUTO_DELETE"];
        }

        $this->exchange = $this->machine[$server]["EXCHANGE"];
        $this->queue = $this->machine[$server]["QUEUE"];
    }

    function process_message($msg)
    {
        // Acknowledge the message immediately for one-way messages
        $this->conn_queue->ack($msg->getDeliveryTag());

        try {
            if ($msg->getReplyTo()) {
                // Message wants a response
                $body = $msg->getBody();
                $payload = json_decode($body, true);

                $response = null;
                if (isset($this->callback)) {
                    $response = call_user_func($this->callback, $payload);
                }

                $params = array(
                    'host' => $this->BROKER_HOST,
                    'port' => $this->BROKER_PORT,
                    'login' => $this->USER,
                    'password' => $this->PASSWORD,
                    'vhost' => $this->VHOST,
                );

                $conn = new AMQPConnection($params);
                $conn->connect();
                $channel = new AMQPChannel($conn);

                $exchange = new AMQPExchange($channel);
                $exchange->setName($this->exchange);
                $exchange->setType($this->exchange_type);

                $conn_queue = new AMQPQueue($channel);
                $conn_queue->setName($msg->getReplyTo());
                $replyKey = $this->routing_key . ".response";
                $conn_queue->bind($exchange->getName(), $replyKey);

                $exchange->publish(
                    json_encode($response),
                    $replyKey,
                    AMQP_NOPARAM,
                    array('correlation_id' => $msg->getCorrelationId())
                );

                return;
            }
        } catch (Exception $e) {
            echo "Error: RabbitMQServer: process_message: Exception caught: " . $e;
        }
    }

    function process_requests($callback)
    {
        try {
            $this->callback = $callback;

            $params = array(
                'host' => $this->BROKER_HOST,
                'port' => $this->BROKER_PORT,
                'login' => $this->USER,
                'password' => $this->PASSWORD,
                'vhost' => $this->VHOST,
            );

            $conn = new AMQPConnection($params);
            $conn->connect();

            $channel = new AMQPChannel($conn);

            $exchange = new AMQPExchange($channel);
            $exchange->setName($this->exchange);
            $exchange->setType($this->exchange_type);

            $this->conn_queue = new AMQPQueue($channel);
            $this->conn_queue->setName($this->queue);
            $this->conn_queue->bind($exchange->getName(), $this->routing_key);

            $this->conn_queue->consume(array($this, 'process_message'));

            // Loop as long as the channel has callbacks registered
            while (count($channel->callbacks)) {
                $channel->wait();
            }
        } catch (Exception $e) {
            trigger_error("Failed to start request processor: " . $e, E_USER_ERROR);
        }
    }
}

class RabbitMQClient
{
    private $machine = "";
    public $BROKER_HOST;
    private $BROKER_PORT;
    private $USER;
    private $PASSWORD;
    private $VHOST;
    private $exchange;
    private $queue;
    private $routing_key = '*';
    private $response_queue = array();
    private $exchange_type = "topic";

    function __construct($machine, $server = "rabbitMQ")
    {
        $this->machine = getHostInfo(array($machine));
        $this->BROKER_HOST = $this->machine[$server]["BROKER_HOST"];
        $this->BROKER_PORT = $this->machine[$server]["BROKER_PORT"];
        $this->USER = $this->machine[$server]["USER"];
        $this->PASSWORD = $this->machine[$server]["PASSWORD"];
        $this->VHOST = $this->machine[$server]["VHOST"];

        if (isset($this->machine[$server]["EXCHANGE_TYPE"])) {
            $this->exchange_type = $this->machine[$server]["EXCHANGE_TYPE"];
        }

        if (isset($this->machine[$server]["AUTO_DELETE"])) {
            $this->auto_delete = $this->machine[$server]["AUTO_DELETE"];
        }

        $this->exchange = $this->machine[$server]["EXCHANGE"];
        $this->queue = $this->machine[$server]["QUEUE"];
    }

    function process_response($response)
    {
        $uid = $response->getCorrelationId();

        try {
            if (!isset($this->response_queue[$uid])) {
                echo "Unknown UID\n";
                return true;
            }

            $this->conn_queue->ack($response->getDeliveryTag());

            $body = $response->getBody();
            $payload = json_decode($body, true);

            if (!isset($payload)) {
                $payload = "[empty response]";
            }

            $this->response_queue[$uid] = $payload;
            return false;
        } catch (Exception $e) {
            echo "Error processing response: " . $e->getMessage() . "\n";
            return true;
        }
    }

    function send_request($message)
    {
        $uid = uniqid();
        $json_message = json_encode($message);

        try {
            $params = array(
                'host' => $this->BROKER_HOST,
                'port' => $this->BROKER_PORT,
                'login' => $this->USER,
                'password' => $this->PASSWORD,
                'vhost' => $this->VHOST,
            );

            $conn = new AMQPConnection($params);
            $conn->connect();

            $channel = new AMQPChannel($conn);

            $exchange = new AMQPExchange($channel);
            $exchange->setName($this->exchange);
            $exchange->setType($this->exchange_type);

            $callback_queue = new AMQPQueue($channel);
            $callback_queue->setName($this->queue . "_response");
            $callback_queue->declare();
            $callback_queue->bind($exchange->getName(), $this->routing_key . ".response");

            $this->conn_queue = new AMQPQueue($channel);
            $this->conn_queue->setName($this->queue);
            $this->conn_queue->bind($exchange->getName(), $this->routing_key);

            $exchange->publish(
                $json_message,
                $this->routing_key,
                AMQP_NOPARAM,
                array('reply_to' => $callback_queue->getName(), 'correlation_id' => $uid)
            );

            $this->response_queue[$uid] = "waiting";
            $callback_queue->consume(array($this, 'process_response'));

            $response = $this->response_queue[$uid];
            unset($this->response_queue[$uid]);
            return $response;
        } catch (Exception $e) {
            die("Failed to send message to exchange: " . $e->getMessage() . "\n");
        }
    }

    function publish($message)
    {
        $json_message = json_encode($message);

        try {
            $params = array(
                'host' => $this->BROKER_HOST,
                'port' => $this->BROKER_PORT,
                'login' => $this->USER,
                'password' => $this->PASSWORD,
                'vhost' => $this->VHOST,
            );

            $conn = new AMQPConnection($params);
            $conn->connect();

            $channel = new AMQPChannel($conn);

            $exchange = new AMQPExchange($channel);
            $exchange->setName($this->exchange);
            $exchange->setType($this->exchange_type);

            $this->conn_queue = new AMQPQueue($channel);
            $this->conn_queue->setName($this->queue);
            $this->conn_queue->bind($exchange->getName(), $this->routing_key);

            return $exchange->publish($json_message, $this->routing_key);
        } catch (Exception $e) {
            die("Failed to send message to exchange: " . $e->getMessage() . "\n");
        }
    }
}

?>
