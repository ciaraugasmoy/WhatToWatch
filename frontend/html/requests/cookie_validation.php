<?php
// Retrieve tokens from cookies
$access_token = isset($_COOKIE['access_token']) ? $_COOKIE['access_token'] : '';
$refresh_token = isset($_COOKIE['refresh_token']) ? $_COOKIE['refresh_token'] : '';
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

// Redirect to index if cookies are not set
if (empty($access_token) || empty($refresh_token) || empty($username)) {
    header("Location:".__DIR__. "../index.html");
    exit();
}
?>