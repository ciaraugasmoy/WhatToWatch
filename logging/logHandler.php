<?php

$logFiles = [
    '/var/log/apache2/error.log',
    '/var/log/mysql/error.log',
    '/var/log/rabbitmq/rabbitmq.log',
];

$outputFile = './logs/WhatToWatch.log';
$output = fopen($outputFile, 'a');

foreach ($logFiles as $logFile) {
    // Check if the log file exists
    if (file_exists($logFile)) {
        $handle = fopen($logFile, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                fwrite($output, $line);
            }
            fclose($handle);
        }
    } 
    else {
        touch($outputFile);
    }
}
fclose($output);
?>
