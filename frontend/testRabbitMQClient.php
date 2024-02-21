#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("rabbitMQDB.ini","testServer");
if (isset($argv[1]))
{
  $type = $argv[1];
}
else
{
  $type = "login";
}
if (isset($argv[2]))
{
  $username=$argv[2];
}
else{
  $username="steve";
}
if (isset($argv[3]))
{
  $password=$argv[3];
}

$request = array();
$request['type'] = $type;
$request['username'] = $username;
$request['password'] = $password;
$request['message'] = "test megsgsg";

$response = $client->send_request($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

