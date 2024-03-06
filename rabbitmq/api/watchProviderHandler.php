#!/usr/bin/php
<?php

// Read API token from config.ini
$config = parse_ini_file('config.ini');
$token = $config['api_token'];

$ch = curl_init();

$url = 'https://api.themoviedb.org/3/watch/providers/movie?language=en-US&watch_region=US';

$headers = [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
];

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
    exit;
}

curl_close($ch);

// Decode the JSON response from api
$data = json_decode($result, true);

// Check if "results" key exists
if (isset($data['results']) && is_array($data['results'])) {
    // Read database credentials from credentials.ini
    $credentials = parse_ini_file('credentials.ini', true)['database'];
    echo "attempting to connect to db" . PHP_EOL;
    $conn= new mysqli(
        $credentials['host'],
        $credentials['username'],
        $credentials['password'],
        $credentials['database']
    );
    if ($conn->connect_error) {
        echo "failed to connect" . PHP_EOL;
        die("Connection failed: " . $this->mysqli->connect_error);
    }


    // Loop through the results and insert into the database
    foreach ($data['results'] as $provider) {
        $providerId = $provider['provider_id'];
        $providerName = $provider['provider_name'];
        $logoPath = $provider['logo_path'];
        $displayPriority = $provider['display_priority'];

        // Insert data into the database
        $sql = "INSERT INTO watch_providers(provider_id, provider_name, logo_path, display_priority) 
                VALUES ('$providerId', '$providerName', '$logoPath', '$displayPriority')";

        if ($conn->query($sql) === TRUE) {
            echo "Record inserted successfully.\n";
        } else {
            echo "Error inserting record: " . $conn->error . "\n";
        }
    }
    

    // Close the database connection
    $conn->close();
} else {
    echo "Invalid or missing data in the API response.\n";
}
?>
