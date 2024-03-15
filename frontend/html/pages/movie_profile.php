
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

.backdrop{
    position: fixed;
    z-index: -1;
    width: 100vw;   
}
.movieinfo{
    margin:100px 10px 20px;
    border-radius: 20px;
    background: linear-gradient(#000000 5px,#000000DD);
    max-width:500px;
    padding: 20px;
    justify-self: center;
    justify-content: center;
    justify-items: center;
    gap:10px;
    display: grid;
}
.movieinfo .poster{
    width: 300px;
    border-radius: 20px;
}
.providers{
    width:300px;
    overflow-y: scroll;
    display: flex;
    gap: 10px;
    padding: 20px;
    align-items: center;
    transition: 300ms;
}
.providers:empty{ 
    background-color: #000;
    border-radius: 10px;
    height: 60px;
    transition: 300ms;
    overflow: hidden;
}
.providers:empty:after{
    content: 'no providers found';
}
.providers img{
    max-width: 60px;
    border-radius: 5px;
}
.providers img:hover{
    border: 1px solid cyan;
}
</style>
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
    '<img class="backdrop" src="'.$backdrop_url.'">'
    .'<section class="movieinfo">'
    .'<h2>'.$movie['title'].'</h2>'
    .'<p class="releasedate">'.$movie['release_date'].'</p>'
    .'<p>'.$movie['overview'].'</p>'
    .'<img class="poster" src="'.$poster_url.'">'
    .'<h3>'.'Streaming On'.'</h3>'
    .'<div class="providers">'.'</div>'
    .'</section>';

?>
</body>
</html>
