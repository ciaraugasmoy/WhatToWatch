#!/usr/bin/php
<?php

class SearchHandler {
    private $token;
    
    public function __construct() {
        // Read API token from config.ini
        $config = parse_ini_file('config.ini');
        $this->token = $config['api_token'];
        $this->credentials = parse_ini_file('credentials.ini', true)['database'];
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
            $stmt = $conn->prepare("INSERT INTO movies (movie_id, title, overview, release_date, poster_path, backdrop_path, adult) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), overview = VALUES(overview), release_date = VALUES(release_date), poster_path = VALUES(poster_path), backdrop_path = VALUES(backdrop_path), adult = VALUES(adult)");
            $stmt->bind_param("ssssssi", $id, $title, $overview, $release_date, $poster_path, $backdrop_path, $adult);
            
            $movies = array();
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

                $movies[] = array(
                    'id' => $id,
                    'title' => $title,
                    'overview' => $overview,
                    'release_date' => $release_date,
                    'poster_path' => $poster_path,
                    'backdrop_path' => $backdrop_path,
                    'adult' => $adult
                );
            }

            echo "Records inserted or updated successfully";

            // Close statement and database connection
            $stmt->close();
            return array("status" => "success", "message" => "search performed successfully", "movies"=>$movies);
        }
    }
        
    public function topRatedSearch($page) {
        $ch = curl_init();

        $url = 'https://api.themoviedb.org/3/movie/top_rated?language=en-US&page=1='.$page;

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
            $stmt = $conn->prepare("INSERT INTO movies (movie_id, title, overview, release_date, poster_path, backdrop_path, adult) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), overview = VALUES(overview), release_date = VALUES(release_date), poster_path = VALUES(poster_path), backdrop_path = VALUES(backdrop_path), adult = VALUES(adult)");
            $stmt->bind_param("ssssssi", $id, $title, $overview, $release_date, $poster_path, $backdrop_path, $adult);
            
            $movies = array();
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

                $movies[] = array(
                    'id' => $id,
                    'title' => $title,
                    'overview' => $overview,
                    'release_date' => $release_date,
                    'poster_path' => $poster_path,
                    'backdrop_path' => $backdrop_path,
                    'adult' => $adult
                );
            }

            echo "Records inserted or updated successfully";

            // Close statement and database connection
            $stmt->close();
            return array("status" => "success", "message" => "search performed successfully", "movies"=>$movies);
        }
    }

    public function getSimilar($page, $username) {
        // Retrieve the highest rated movie ID for the given user
        $highestRatedMovieId = $this->getHighestRatedMovieId($username);
    
        if ($highestRatedMovieId) {
            // Make a request to TMDB API to get similar movies
            $url = 'https://api.themoviedb.org/3/movie/' . $highestRatedMovieId . '/similar?language=en-US&page=' . $page;
    
            $ch = curl_init();
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
    
            if (isset($data['results']) && is_array($data['results'])) {
                // Process the similar movies data
                // This part is similar to how it's done in topRatedSearch function
                // You can reuse the code from topRatedSearch function to process the data
                // and handle it accordingly
    
                // Connect to the database
                
                $conn = new mysqli(
                    $this->credentials['host'],
                    $this->credentials['username'],
                    $this->credentials['password'],
                    $this->credentials['database']
                );
    
                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
    
                // Prepare and bind SQL statement
                $stmt = $conn->prepare("INSERT INTO movies (movie_id, title, overview, release_date, poster_path, backdrop_path, adult) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), overview = VALUES(overview), release_date = VALUES(release_date), poster_path = VALUES(poster_path), backdrop_path = VALUES(backdrop_path), adult = VALUES(adult)");
                $stmt->bind_param("ssssssi", $id, $title, $overview, $release_date, $poster_path, $backdrop_path, $adult);
    
                $movies = array();
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
    
                    $movies[] = array(
                        'id' => $id,
                        'title' => $title,
                        'overview' => $overview,
                        'release_date' => $release_date,
                        'poster_path' => $poster_path,
                        'backdrop_path' => $backdrop_path,
                        'adult' => $adult
                    );
                }
    
                echo "Records inserted or updated successfully";
    
                // Close statement and database connection
                $stmt->close();
                $conn->close();
    
                return array("status" => "success", "message" => "Similar movies fetched successfully", "movies" => $movies);
            }
        }
    
        return array("status" => "error", "message" => "Failed to fetch similar movies");
    }
    
    private function getHighestRatedMovieId($username) {
        // Connect to the database
        $conn = new mysqli(
            $this->credentials['host'],
            $this->credentials['username'],
            $this->credentials['password'],
            $this->credentials['database']
        );
    
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        // Prepare and execute SQL statement to get highest rated movie ID for the user
        $stmt = $conn->prepare("SELECT movie_id FROM movie_reviews WHERE user_id = (SELECT id FROM users WHERE username = ?) ORDER BY rating DESC LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($highestRatedMovieId);
        $stmt->fetch();
    
        // Close statement and database connection
        $stmt->close();
        $conn->close();
    
        return $highestRatedMovieId;
    }
    

}

?>
