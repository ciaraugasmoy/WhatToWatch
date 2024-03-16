
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
    margin:100px 0px 20px;
    border-radius: 20px;
    background: linear-gradient(#000000 5px,#000000DD);
    width:500px;
    max-width: 100vw;
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
    border-radius: 10px 10px 0 0;
}
.providers .imgbox{
    border-radius:10px;
    border: 1px solid #00000000;
    transition:900ms;
}
.providers .imgbox:hover{
    border: 1px solid cyan;
    transition:900ms;
}
.imglabel{
    background: green;
    margin-top:-4px;
    width: 100%;
    padding:2px;
    text-align: center;
    border-radius: 0 0 10px 10px;
    
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



$request2 = array();
$request2['type'] = "get_movie_providers";
$request2['username'] = $username;
$request2['movie_id'] = $movie_id;
$response2 = $client->call($request2);
$user_providers_list = '';
$general_providers_list = '';
if (isset($response2['providers']) && is_array($response2['providers'])) {
    $providers = $response2['providers'];
    $url_path = 'https://image.tmdb.org/t/p/w500';
    foreach ($providers as $provider) {
        if ($provider['user_has']) {
            $user_providers_list .= '<div class="imgbox"'
                                .' title="'.$provider['provider_name'].'" >'
                                .'<img src="'.$url_path.$provider['logo_path'].'">'
                                .'<div class="imglabel">'.$provider['pricing'].'</div>'
                                .'</div>';
        } else {
            $general_providers_list .= '<div class="imgbox"'
                                .' title="'.$provider['provider_name'].'" >'
                                .'<img src="'.$url_path.$provider['logo_path'].'">'
                                .'<div class="imglabel">'.$provider['pricing'].'</div>'
                                .'</div>';
        }
    }
}

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
    .'<h3>'.'Your Providers'.'</h3>'
    .'<div class="providers">'.$user_providers_list.'</div>'
    .'<h3>'.'Other Providers'.'</h3>'
    .'<div class="providers">'.$general_providers_list.'</div>'
    .'</section>';

    include '../partials/reviewform.php';
?>



</body>
</html>
