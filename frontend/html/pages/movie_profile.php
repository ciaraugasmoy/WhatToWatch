
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
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
$client = new RPCClient();

$movie_id = isset($_GET['id']) ? $_GET['id'] : '';

$access_token = isset($_COOKIE['access_token']) ? $_COOKIE['access_token'] : '';
$refresh_token = isset($_COOKIE['refresh_token']) ? $_COOKIE['refresh_token'] : '';
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
$request =[
    'type' => 'get_movie_details',
    'movie_id'   => $movie_id,
];
$response = $client->call($request);
$movie = $response['movie'];
    $backdrop_url= 'https://image.tmdb.org/t/p/original/'.$movie['backdrop_path'];
    $poster_url= 'https://image.tmdb.org/t/p/original/'.$movie['poster_path'];
    echo
    '<section class="movieinfo">'
    .'<h2>'.$movie['title'].'</h2>'
    .'<p>'.$movie['release_date'].'</p>'
    .'<img class="backdrop" src="'.$backdrop_url.'">'
    .'<p>'.$movie['overview'].'</p>'
    .'<img class="poster" src="'.$poster_url.'">'
    .'<p>'.'remember the wp'.'</p>'
    .'</section>';

?>
</body>
</html>
