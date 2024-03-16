
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

// Check if tokens are not set
// if (empty($access_token) || empty($refresh_token) || empty($username)) {
//     echo json_encode(['status' => false]);
// } else 
// $validaterequest = [
//     'type' => 'validate',
//     'tokens' => [
//         'access_token' => $access_token,
//         'refresh_token' => $refresh_token,
//     ],
//     'username' => $username,
// ];

$request =[
    'type' => 'get_movie_details',
    'movie_id'   => $movie_id,
];
$response = $client->call($request);

?>
<?php

if(isset($_POST["submit"])){
  $user_id = $_POST["user_id"];
  $message = $_POST["message"];
  $parent_message_id = $_POST["parent_message_id"];

  $query = "INSERT INTO discussion_posts VALUES('', '$user_id', '$message', '$parent_message_id')";
  mysqli_query($conn, $query);
}
?>
<html>
 <body>
    <div class="container">
      <?php
      $datas = mysqli_query($conn, "SELECT * FROM discussion_posts WHERE parent_message_id = 0"); 
      foreach($datas as $data) {
        require 'comment.php';
      }
      ?>
      <form action = "" method = "post">
        <h3 id = "title">Leave a Comment</h3>
        <input type="hidden" name="parent_message_id" id="parent_message_id">
        <textarea name="message" placeholder="Your comment"></textarea>
        <button class = "submit" type="submit" name="submit">Submit</button>
      </form>
    </div>

    <script>
      function reply(message_id, user_id){
        title = document.getElementById('title');
        title.innerHTML = "Reply to " + user_id;
        document.getElementById('parent_message_id').value = message_id;
      }
    </script>
  </body>
</html>
