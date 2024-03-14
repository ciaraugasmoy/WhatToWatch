#!/usr/bin/php
<?php
// Read API token from config.ini
$config = parse_ini_file('config.ini');
$token = $config['api_token'];
$ch = curl_init();

$user_query = 'space%20movie';
$user_page = '1';
$user_adult = 'false';
$url = 'https://api.themoviedb.org/3/search/movie?query=' . $user_query . '&include_adult=' . $user_adult . '&language=en-US&page=' . $user_page;

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

// Decode the JSON response from API
$data = json_decode($result, true);

// Check if "results" key exists
if (isset($data['results']) && is_array($data['results'])) {
    // Read database credentials from credentials.ini
    $credentials = parse_ini_file('credentials.ini', true)['database'];

    // Connect to the database
    $conn = new mysqli(
        $credentials['host'],
        $credentials['username'],
        $credentials['password'],
        $credentials['database']
    );

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO movies (movie_id, title, overview, release_date, poster_path, backdrop_path, adult) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), overview = VALUES(overview), release_date = VALUES(release_date), poster_path = VALUES(poster_path), backdrop_path = VALUES(backdrop_path), adult = VALUES(adult)");
    $stmt->bind_param("ssssssi", $id, $title, $overview, $release_date, $poster_path, $backdrop_path, $adult);

    // Insert or update each movie record into the database
    foreach ($data['results'] as $movie) {
        $id = $movie['id'];
        $title = $movie['title'];
        $overview = $movie['overview'];
        $release_date = !empty($movie['release_date']) ? date('Y-m-d', strtotime($movie['release_date'])) : null;
        $poster_path = $movie['poster_path'];
        $backdrop_path = $movie['backdrop_path'];
        $adult = $movie['adult'] ? 1 : 0;
        // Execute the SQL statement
        $stmt->execute();
    }

    echo "Records inserted or updated successfully";

    // Close statement and database connection
    $stmt->close();
    $conn->close();
}
?>
