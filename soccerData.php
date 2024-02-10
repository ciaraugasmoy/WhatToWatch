<?php
$results = shell_exec('GET https://api.football-data.org/v4/teams/86/matches?status=SCHEDULED');
$arrayCode = json_decode($results);
var_dump($arrayCode);
?>
