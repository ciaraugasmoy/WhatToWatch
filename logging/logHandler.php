<?php
$logFiles = [
    '/var/log/apache2/error.log',
    '/var/log/mysql/error.log',
    '/var/log/rabbitmq/rabbitmq.log'
];

$outputFile = './logs/what2watch.log';
$storedPositionFile = './logs/position.txt'; // Store the last known position

// Read the stored position (if available)
$storedPosition = file_exists($storedPositionFile) ? intval(file_get_contents($storedPositionFile)) : 0;

$output = fopen($outputFile, 'a');

foreach ($logFiles as $logFile) {
    if (file_exists($logFile)) {
        $handle = fopen($logFile, 'r');
        if ($handle) {
            $lineNumber = 0;
            while (($line = fgets($handle)) !== false) {
                $lineNumber++;
                if ($lineNumber > $storedPosition) {
                    fwrite($output, $line);
                }
            }
            fclose($handle);
        }
    }
}

// Update the stored position
file_put_contents($storedPositionFile, $lineNumber);

fclose($output);
?>
