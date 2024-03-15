<?php
//ratings reviews etc
class MovieDataHandler
{
    private $mysqli; 
    public function __construct()
    {
        $credentials = parse_ini_file('credentials.ini', true)['database'];
        echo "attempting to connect to db" . PHP_EOL;
        $this->mysqli = new mysqli(
            $credentials['host'],
            $credentials['username'],
            $credentials['password'],
            $credentials['database']
        );
        if ($this->mysqli->connect_error) {
            echo "failed to connect" . PHP_EOL;
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }

    public function getMovieDetails($movie_id)
    {
        $movie_id = $this->mysqli->real_escape_string($movie_id);
        $query = "SELECT * FROM movies WHERE movie_id = $movie_id";
        $result = $this->mysqli->query($query);
        if (!$result) {
            echo "Error executing query: " . $this->mysqli->error;
            return ['status' => 'error', 'message' => 'Error executing query'];
        }
        $movieData = $result->fetch_assoc();
        $this->mysqli->close();
        return ['status' => 'success', 'message' => 'Movie data found', 'movie' => $movieData];
    }

}
?>