#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../client/client_rpc.php'; // Include the RPCClient class

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$client = new RPCClient();

$request2 = array();
$request2['type'] = "get_movie_providers";
$request2['username'] = 'steve';
$request2['movie_id'] = '27205';
$response2 = $client->call($request2);
var_dump($response2).PHP_EOL;

$providers = $response2['providers'];
$user_providers_list;
$general_providers_list;
$url_path='https://image.tmdb.org/t/p/w500';
foreach ($providers as $provider){
    if ($provider['user_has']){
        echo 'user has the following'.PHP_EOL;
        echo $provider['provider_name'].PHP_EOL;
        $user_providers_list= $user_providers_list.'<img'
                                .' data-provider-pricing="'.$provider['pricing']
                                .'" data-provider-name="'.$provider['provider_name']
                                .'" src=">'.$url_path.$provider['logo_path']
                                .'">';
    }
    else{
        echo 'USER DOES NOT HAVE'.PHP_EOL;
        echo $provider['provider_id'].PHP_EOL;
        echo $provider['provider_name'].PHP_EOL;
        echo $provider['logo_path'].PHP_EOL;        
        echo $provider['pricing'].PHP_EOL;
        $general_providers_list= $user_providers_list.'<img'
        .' data-provider-pricing="'.$provider['pricing']
        .'" data-provider-name="'.$provider['provider_name']
        .'" src=">'.$url_path.$provider['logo_path']
        .'">';
    }
    
}
?>