#!/usr/bin/php

<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once 'UserHandler.php';
require_once 'UserDataHandler.php';
require_once 'api/SearchHandler.php';
require_once 'api/MovieWatchProvider.php';
require_once 'MovieDataHandler.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

	
$connection = new AMQPStreamConnection('localhost', 5672, 'mqadmin', 'mqadminpass','brokerHost'); #THE VALUES ARE RABBITMQ CREDs
$channel = $connection->channel();

$channel->queue_declare('testQueue', false, false, false, false);

function HANDLE_MESSAGE($request)
{
    echo "received request" . PHP_EOL;
    var_dump($request);

    if (!isset($request['type'])) {
        return array("status" => "error", "message" => "Unsupported message type");
    }
    switch ($request['type']) {
        case "login":
            $userHandler = new UserHandler();
            return $userHandler->doLogin($request['username'], $request['password']);
        case "signup":
            $userHandler = new UserHandler();
            return $userHandler->doSignup($request['username'], $request['password'], $request['email'], $request['dob']);
        case "validate":
            $userHandler = new UserHandler();
            return $userHandler->doValidate($request['username'], $request['tokens']);
        case "get_providers":
            $userDataHandler= new UserDataHandler();
            return $userDataHandler->getWatchProviders($request['username']);
        case "set_provider":
            $userDataHandler= new UserDataHandler();
            return $userDataHandler->setWatchProviders($request['username'], $request['watch_provider_id']);
        case "unset_provider":
            $userDataHandler= new UserDataHandler();
            return $userDataHandler->unsetWatchProviders($request['username'], $request['watch_provider_id']);
        case "get_curated_providers":
            $userDataHandler= new UserDataHandler();
            return $userDataHandler->getCuratedWatchProviders($request['username']);
        case "get_friend_list":
            $userDataHandler= new UserDataHandler();
            return $userDataHandler->getFriendList($request['username']);
        case "send_friend_request":
            $userDataHandler= new UserDataHandler();
            return $userDataHandler->sendFriendRequest($request['username'],$request['friend_username']);
        case "accept_friend_request":
            $userDataHandler= new UserDataHandler();
            return $userDataHandler->acceptFriendRequest($request['username'],$request['friend_username']);
        case "delete_friend":
            $userDataHandler= new UserDataHandler();
            return $userDataHandler->deleteFriend($request['username'],$request['friend_username']);
        case "discover_movie":
            $searchHandler = new SearchHandler();
            return $searchHandler->performSearch($request['query'], $request['page'], $request['adult_bool']);
        case "get_movie_details":
            $movieDataHandler = new MovieDataHandler();
            return $movieDataHandler->getMovieDetails($request['movie_id']);
        case "get_user_review":
            $movieDataHandler = new MovieDataHandler();
            return $movieDataHandler->getUserReview($request['username'],$request['movie_id']);
        case "post_user_review":
            $movieDataHandler = new MovieDataHandler();
            return $movieDataHandler->postUserReview($request['username'],$request['movie_id'],$request['rating'],$request['review']);
        case "get_movie_providers":
            $movieWatchProvider = new MovieWatchProvider();
            return $movieWatchProvider->getProviders($request['username'],$request['movie_id']);
        }

    return array("status" => "error", "message" => "Server received request and processed but no case");
}


echo " [x] Awaiting RPC requests\n";
$callback = function ($req) {
    $n = $req->getBody();
    $result = HANDLE_MESSAGE(json_decode($n, true)); // Decode JSON string to associative array

    $msg = new AMQPMessage(
        json_encode($result), // Encode the result array as JSON
        array('correlation_id' => $req->get('correlation_id'))
    );

    $req->getChannel()->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );
    $req->ack();
};

$channel->basic_qos(null, 1, false);
$channel->basic_consume('testQueue', '', false, false, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}

$channel->close();
$connection->close();
?>
