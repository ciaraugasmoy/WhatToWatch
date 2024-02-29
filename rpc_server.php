#!/usr/bin/php

<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
	
$connection = new AMQPStreamConnection('localhost', 5672, 'testUser', 'testpassword'); #THE VALUES ARE RABBITMQ CREDs
$channel = $connection->channel();

$channel->queue_declare('testQueue', false, false, false, false);

function HANDLE_MESSAGE($n)
{
	$servername = "192.168.192.13"; #IP ADDRESS OF DB VM
	$username = "testUser"; #USERNAME OF sQL UsER THAT IS OPEN TO THE IP OF RABBITMQ
	$password = "testpassword";
	$dbname = "bruh";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$conn) {
  		die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "SELECT test FROM testTable";
	$result = mysqli_query($conn, $sql);
	
	if (mysqli_num_rows($result) > 0) {
  	// output data of each row
  	while($row = mysqli_fetch_assoc($result)) {
    		echo "test: " . $row["test"]. "<br>";
    		return $row["test"];
  	}
	} else {
  		echo "0 results";
	}

}

echo " [x] Awaiting RPC requests\n";
$callback = function ($req) {
    $n = $req->getBody();
    echo ' [.] OUTPUT(', $n, ")\n";

    $msg = new AMQPMessage(
        (string) HANDLE_MESSAGE($n),
        array('correlation_id' => $req->get('correlation_id'))
    );

    $req->getChannel()->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );
    $req->ack();
};

$channel->basic_qos(null, 1, false);
$channel->basic_consume('testQueue', '', false, false, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}

$channel->close();
$connection->close();
?>
