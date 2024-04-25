<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <script src="../js/template.js"></script>
    <title>Watchlist</title>
</head>
<body>
<style scoped>

h1{
    text-align: center;
    padding: 10px;
}
#results{
    display: grid;
    gap:10px;
    overflow-x: scroll;
    justify-content: center;
}
@media (min-width:961px){
    #results{
        justify-self: center;
        grid-template-columns: 1fr 1fr 1fr;
    }
} 
#results .movie{
    display: grid;
    border: 2px #00000000 solid;
    grid-template-rows: 40px auto auto;
    width:300px;
    border-radius: 20px;
    padding: 5px;
    background-color: black;
    color:white;
    transition : 2s;
}
#results .movie:hover{
    border: 2px cadetblue solid;
    transition: 2s;
}
.movieimg{
    max-width: 100%;
    border-radius: 20px;
}
.movie h4{
    text-align: center;
    padding: 10px;
}
.formgroup{
   justify-self: center;
    padding: 30px;
}
form{
    display: inline-block;
}
form button{
    border:none;
    padding:10px;
    border-radius:5px;
    color: white;
    background:#000;
}
form button:hover{
    background:darkred;
}
.movie a{
    text-decoration: none;
    color: white;
}
.movie a:hover{
    text-decoration: none;
    color: #0df;
}

button{
    height:40px;
    padding: 10px;
    background-color: #01404a90;
    color:aquamarine;
    border: none;
    transition:300ms;
    border-radius:30px;
    justify-self:center;
    align-self:end;
    margin:5px;
}
    button:hover{
    background-color: #01404a;
    transition:300ms;
}

</style>
<h1>Your Watchlist</h1>
<section id='results'>
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
$client = new RPCClient();

$query = isset($_GET['query']) ? $_GET['query'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : '';
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

$request=array();
$request['type'] =  "get_watchlist";
$request['page'] = $page;
$request['username'] = $username;
$response = $client->call($request);

foreach ($response['movies'] as $movie){
    $title = $movie['title'];
    $overview = $movie['overview'];
    $src= 'https://image.tmdb.org/t/p/original/'.$movie['poster_path'];
    $id = $movie['movie_id'];
    $url= 'movie_profile.php?id='.$id;

    echo 
    '<div class ="movie">'
    .'<h4>'.'<a href="'.$url.'">'.$title.'</a>'.'</h4>'
    .'<div>'
    .'<img class="movieimg" src="'.$src.'">'
    .'<p>'.$overview.'</p>'
    .'</div>'
    .'<button class="watchlistbtn" data-status="add_to_watchlist" data-movie-id="'.$id.'">Remove from Watchlist</button>'
    .'</div>';
}
?>

</section>
<script>
const buttons = document.querySelectorAll('.watchlistbtn');


buttons.forEach(button => {
    button.addEventListener('click', function() {
        const movieId = this.getAttribute('data-movie-id');
        const watchlistStatus = this.getAttribute('data-status');

        let newStatus;
        if (watchlistStatus === 'add_to_watchlist') {
            newStatus = 'remove_from_watchlist';
        } else if (watchlistStatus === 'remove_from_watchlist') {
            newStatus = 'add_to_watchlist';
        }

        const url = `../requests/toggle_watchlist.php?movie_id=${encodeURIComponent(movieId)}&watchlist_status=${encodeURIComponent(newStatus)}`;

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Handle the response data here if needed
                console.log(data);
                // Update the button's data-status attribute based on the new status
                this.setAttribute('data-status', newStatus);
                // Optionally update the button text or styling based on the new status
                this.textContent = newStatus === 'add_to_watchlist' ? 'Remove from Watchlist': 'Add to Watchlist' ;
            })
            .catch(error => {
                console.log('Fetch error');
            });
    });
});

</script>
</div>
</body>
</html>
