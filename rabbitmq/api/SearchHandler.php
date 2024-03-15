#!/usr/bin/php
<?php

class SearchHandler {
    private $token;
    
    public function __construct() {
        // Read API token from config.ini
        $config = parse_ini_file('config.ini');
        $this->token = $config['api_token'];
    }
    
    public function performSearch($query, $page, $adult) {
        $ch = curl_init();

        $url = 'https://api.themoviedb.org/3/search/movie?query=' . urlencode($query) . '&include_adult=' . $adult . '&language=en-US&page=' . $page;

        $headers = [
            'Authorization: Bearer ' . $this->token,
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
            $stmt = $conn->prepare("INSERT INTO movies (movie_id, title, overview, release_date, poster_path, backdrop_path, adult, vote_average) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), overview = VALUES(overview), release_date = VALUES(release_date), poster_path = VALUES(poster_path), backdrop_path = VALUES(backdrop_path), adult = VALUES(adult), vote_average = VALUES(vote_average)");
            $stmt->bind_param("ssssssid", $id, $title, $overview, $release_date, $poster_path, $backdrop_path, $adult, $vote_average);
            
            $movies = array();
            // Insert or update each movie record into the databases
            foreach ($data['results'] as $movie) {
                $id = $movie['id'];
                $title = $movie['title'];
                $overview = $movie['overview'];
                $release_date = !empty($movie['release_date']) ? date('Y-m-d', strtotime($movie['release_date'])) : null;
                $poster_path = $movie['poster_path'];
                $backdrop_path = $movie['backdrop_path'];
                $adult = $movie['adult'] ? 1 : 0;
                $vote_average = $movie['vote_average'];
                // Execute the SQL statement
                $stmt->execute();

                $movies[] = array(
                    'id' => $id,
                    'title' => $title,
                    'overview' => $overview,
                    'release_date' => $release_date,
                    'poster_path' => $poster_path,
                    'backdrop_path' => $backdrop_path,
                    'adult' => $adult,
                    'vote_average' => $vote_average
                );
            }

            echo "Records inserted or updated successfully";

            // Close statement and database connection
            $stmt->close();
            $conn->close();
            return array("status" => "success", "message" => "search performed successfully", "movies"=>$movies);
        }
    }
}

?>
