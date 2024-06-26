#!/usr/bin/php

<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once 'UserHandler.php';
require_once 'UserDataHandler.php';
require_once 'api/SearchHandler.php';
require_once 'api/MovieWatchProvider.php';
require_once 'MovieDataHandler.php';
require_once 'ThreadHandler.php';
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
        case "2fa":
            $userHandler = new UserHandler();
            return $userHandler->read2fa($request['username'], $request['code']);
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
        case "add_to_watchlist":
            $userDataHandler= new UserDataHandler();
            return $userDataHandler->addToWatchlist($request['username'], $request['movie_id']);
        case "remove_from_watchlist":
            $userDataHandler= new UserDataHandler();
            return $userDataHandler->removeFromWatchlist($request['username'], $request['movie_id']);
        case "get_watchlist":
            $userDataHandler= new UserDataHandler();
            return $userDataHandler->getWatchlist($request['username']);
        case "discover_movie":
            $searchHandler = new SearchHandler();
            return $searchHandler->performSearch($request['query'], $request['page'], $request['adult_bool']);
        case "get_top_rated":
            $searchHandler = new SearchHandler();
            return $searchHandler->topRatedSearch($request['page']);
        case "get_similar":
            $searchHandler = new SearchHandler();
            return $searchHandler->getSimilar($request['page'], $request['username']);
        case "get_movie_details":
            $movieDataHandler = new MovieDataHandler();
            return $movieDataHandler->getMovieDetails($request['movie_id']);
        case "get_movie_details_personal":
            $movieDataHandler = new MovieDataHandler();
            return $movieDataHandler->getMovieDetailsPersonal($request['movie_id'],$request['username']);
        case "get_user_review":
            $movieDataHandler = new MovieDataHandler();
            return $movieDataHandler->getUserReview($request['username'],$request['movie_id']);
        case "get_user_reviews": //ensures user is themself or is friended before return
            $movieDataHandler = new MovieDataHandler();
            return $movieDataHandler->getFriendReviews($request['username'],$request['friend_name']);
        case "post_user_review":
            $movieDataHandler = new MovieDataHandler();
            return $movieDataHandler->postUserReview($request['username'],$request['movie_id'],$request['rating'],$request['review']);
        case "get_recent_reviews":
            $movieDataHandler = new MovieDataHandler();
            return $movieDataHandler->getRecentReviews($request['username'], $request['movie_id'], $request['limit'], $request['offset']);
        case "get_movie_providers":
            $movieWatchProvider = new MovieWatchProvider();
            return $movieWatchProvider->getProviders($request['username'],$request['movie_id']);
        //Thread Handler
        case "post_comment":
            $commentHandler = new ThreadHandler();
            return $commentHandler->postComment($request['username'], $request['thread_id'], $request['body']);
        case "get_comments":
            $commentHandler = new ThreadHandler();
            return $commentHandler->getComments($request['thread_id']);
        case "get_recent_threads":
            $threadHandler = new ThreadHandler();
            return $threadHandler->getRecentThreads($request['offset'], $request['limit'], $request['query'],$request['sort']);
        case "post_thread":
            $threadHandler = new ThreadHandler();
            return $threadHandler->postThread($request['username'], $request['title'], $request['body'], $request['movie_id']);
        case "get_thread":
            $threadHandler = new ThreadHandler();
            return $threadHandler->getThread($request['thread_id']);
        case "get_vote":
            $threadHandler = new ThreadHandler();
            return $threadHandler->getVote($request['username'],$request['thread_id']);
        case "set_vote":
            $threadHandler = new ThreadHandler();
        return $threadHandler->setVote($request['username'],$request['thread_id'],$request['vote']);
        case "subscribe_status":
            $threadHandler = new ThreadHandler();
            return $threadHandler->subscribeStatus($request['username'],$request['thread_id']);
        case "subscribe":
            $threadHandler = new ThreadHandler();
            return $threadHandler->subscribe($request['username'],$request['thread_id']);
        case "unsubscribe":
            $threadHandler = new ThreadHandler();
            return $threadHandler->unsubscribe($request['username'],$request['thread_id']);
        //ai request
        case "ai_recommendation":
            $searchHandler = new SearchHandler();
            return $searchHandler->aiSearch($request['page'],$request['message']);
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
