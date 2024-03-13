<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	require_once('vendor/autoload.php');
	$client = new \GuzzleHttp\Client();
	$env = parse_ini_file('.env');
	$api_key = $env["API_KEY"];

   $api_url = "https://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=false&language=en-US&page=1&sort_by=popularity.desc";

   $response = file_get_contents($api_url);

   header('Content-Type: application/json');
   echo $response;
} else {
	header('Content-Type: application/json');
   echo json_encode(['error' => 'Invalid request']);
}
?>
