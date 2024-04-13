<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <script src="../js/template.js"></script>
    <title>movie page</title>
</head>
<body>
<style scoped>
#review-box{
justify-self: center;
}
.review{
    margin:10px 0px 20px;
    border-radius: 20px;
    background: linear-gradient(#001E38 5px,#001E38DD);
    width:500px;
    max-width: 100vw;
    padding: 20px;
    justify-self: center;
    justify-content: center;
    justify-items: center;
    gap:10px;
    display: grid;
}
.poster{
    width: 300px;
    border-radius: 20px;
}
.poster img{
    width:100%;
}
h2:first-of-type{
    text-align: center;
    margin-top:20px;
}

</style>
<?php 
$usr=$_GET['username'];
echo '<h2>'."Welcome to $usr's profile!".'</h2>';
?>
<section id="review-box">
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
                // echo "Review ID: " . $review['id'];
                // echo "Movie ID: " . $review['movie_id'];
                // echo "User ID: " . $review['user_id'] ;

                $url= 'https://image.tmdb.org/t/p/original'.$review['movie_poster_path'];
                $rating=$review['rating'];
                    $stars='';
                    for ($x = 0; $x <= $rating; $x++) {
                        $stars .= '★';
                    }
                echo '<div class="review">'
                .'<div class="content">'
                .'<h3>'.'Title:'.$review['movie_title'].'</h3>'
                .'<p>'.$review['review'].'<span>'.$review['created_at'].'</span>'.'</p>'
                .'<div class="stars">'.$stars.'</div>'
                .'</div>'
                .'<div class="poster">'.'<img src="'.$url.'">'.'</div>'
                .'</div>';

            }
        } else {
            echo '<div class="review">'. $response['message'].'</div>';
        }
?>
</section>
</body>
</html>