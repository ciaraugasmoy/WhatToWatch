<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <script src="../js/template.js"></script>
    <title>Movie Results</title>
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
</style>
<h1>Movie Results</h1>
<?php include '../partials/search_movie.php'; ?>
<section id='results'>
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
$client = new RPCClient();

$query = isset($_GET['query']) ? $_GET['query'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : '';

$request=array();
$request['type'] = "discover_movie";
$request['query'] = $query;
$request['page'] = $page;
$request['adult_bool'] = 'false';
$response = $client->call($request);

foreach ($response['movies'] as $movie){
    $title = $movie['title'];
    $overview = $movie['overview'];
    $src= 'https://image.tmdb.org/t/p/original/'.$movie['poster_path'];
    $id = $movie['id'];
    $url= 'movie_profile.php?id='.$id;

    echo 
    '<div class ="movie">'
    .'<h4>'.'<a href="'.$url.'">'.$title.'</a>'.'</h4>'
    .'<div>'
    .'<img class="movieimg" src="'.$src.'">'
    .'<p>'.$overview.'</p>'
    .'</div>'
    .'</div>';
}
?>
</section>

<!-- Form to navigate to the previous or next page -->
<div class="formgroup">
<form action="movie_results.php" method="GET">
    <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
    <input type="hidden" name="page" value="<?php echo max(1, $page - 1); ?>"> <!-- Decrease page number -->
    <button type="submit" <?php echo ($page <= 1) ? 'disabled' : ''; ?>>Back</button>
</form>

<form action="movie_results.php" method="GET">
    <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
    <input type="hidden" name="page" value="<?php echo $page + 1; ?>"> <!-- Increase page number -->
    <button type="submit">Next</button>
</form>
</div>
</body>
</html>
