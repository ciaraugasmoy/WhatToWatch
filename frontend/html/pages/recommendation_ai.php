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
#search-form{
  display: flex;
  max-width:100%;
  gap:10px;
  align-content: center;
  margin-bottom:10px;
  justify-self: center;
}
#search-form input[type=text]{
  border-radius: 20px;
  border: 2px #0075DE solid;
  padding: 8px;
  height: min-content;
}
#search-form input[type=submit]{
  border-radius: 20px;
  border: 2px #0075DE solid;
  background-color:#0075DE;
  content:'add friend';
  border-radius: 20px;
  justify-self: right;
  padding: 8px;
  height: min-content;
}
</style>
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
.movie a{
    text-decoration: none;
    color: white;
}
.movie a:hover{
    text-decoration: none;
    color: #0df;
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
</style>
<h1>Recommendations</h1>
<form id='search-form' action="recommendation_ai.php" method="GET">
    <input type="text" id="message" name="message">
    <button type="submit">Submit</button>
</form>


<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
$client = new RPCClient();

$page = isset($_GET['page']) ? $_GET['page'] : '1';

$message = isset($_GET['message']) ? $_GET['message'] : null;
if ($message !== null) {
    echo '<p>' . htmlspecialchars($message) . '</p>';

    // Make request only if message is set
    $request=array();
    $request['type'] = "ai_recommendation";
    $request['message'] = $message;
    $request['page'] = $page;
    $response = $client->call($request);

    echo "<section id='results'>";
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
    echo "</section>";
    if (!isset($_GET['page'])){
        echo "<style scoped>.formgroup{display:none;}</style>";
    }
}
?>


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