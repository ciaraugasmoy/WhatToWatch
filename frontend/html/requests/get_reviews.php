
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Assuming you have an instance of the RPCClient class
$client = new RPCClient();

header('Content-Type: application/json');
// Get offset and limit from the GET parameters
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
$movie_id = isset($_GET['id']) ? $_GET['id'] : '';
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

// Construct a request to retrieve posts with the given offset and limit
$request = array(
    'type' => 'get_recent_reviews',
    'username' =>$username,
    'offset' => $offset,
    'limit' => $limit,
    'movie_id' => $movie_id,
);

// Make a call to your RPC server to retrieve the posts
$response = $client->call($request);

// Prepare the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
