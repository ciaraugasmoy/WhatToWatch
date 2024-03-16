<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <script src="../javascript/template.js"></script>
    <title>movie page</title>
</head>
<body>
<style scoped>
</style>

<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$access_token = isset($_COOKIE['access_token']) ? $_COOKIE['access_token'] : '';
$refresh_token = isset($_COOKIE['refresh_token']) ? $_COOKIE['refresh_token'] : '';
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
$friend_name = $_GET['username'];
// Check if tokens are not set
if (empty($access_token) || empty($refresh_token) || empty($username)) {
    echo json_encode(['status' => 'error']);
}
    $request = [
        'type' => 'validate',
        'tokens' => [
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
        ],
        'username' => $username,
    ];
    echo "Welcome to $username's profile!";

    $response = $client->call($request);
        $request = [
            'type' => 'get_user_reviews',
            'username' => $username,
            'friend_name' => $friend_name,
        ];
        $response = $client->call($request);
        if ($response['status'] === 'success') {
            $reviews = $response['reviews'];
        
            foreach ($reviews as $review) {
                echo "Review ID: " . $review['id'];
                echo "Movie ID: " . $review['movie_id'];
                echo "User ID: " . $review['user_id'] ;
                echo "Rating: " . $review['rating'] ;
                echo "Review: " . $review['review'] ;
                echo "Created At: " . $review['created_at'] ;
                echo "Movie Title: " . $review['movie_title'] ;
                echo "Movie Poster Path: " . $review['movie_poster_path'];
            }
        } else {
            echo "Error: " . $response['message'];
        }
?>

</body>
</html>