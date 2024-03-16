#!/usr/bin/php
<?php
require_once 'SearchHandler.php';
$searchHandler = new SearchHandler();
$searchHandler->performSearch('inception', '1', 'false');
$searchHandler->performSearch('moana', '1', 'false');
$searchHandler->performSearch('barbie', '1', 'false');
$searchHandler->performSearch('alice', '1', 'false');
$searchHandler->performSearch('space movie', '1', 'false');
$searchHandler->performSearch('spider man', '1', 'false');
?>