<?php
echo "attempting to connect to db" . PHP_EOL;
$mysqli = new mysqli("localhost", "what2watchadmin", "what2watchpassword", "what2watch");
    // Check connection
if ($mysqli->connect_error) {
    echo "failed to connect" . PHP_EOL;
    die("Connection failed: " . $mysqli->connect_error);
  }
?>
