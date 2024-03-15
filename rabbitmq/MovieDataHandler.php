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

    public function getUserReview($username, $movie_id)
    {
        $username = $this->mysqli->real_escape_string($username);
        $movie_id = $this->mysqli->real_escape_string($movie_id);
    
        $userQuery = "SELECT id FROM users WHERE username = '$username'";
        $userResult = $this->mysqli->query($userQuery);
        $userData = $userResult->fetch_assoc();
        $user_id = $userData['id'];
    
        $query = "SELECT * FROM movie_reviews WHERE user_id = $user_id AND movie_id = $movie_id";
        $result = $this->mysqli->query($query);
    
        if (!$result) {
            return ['status' => 'error', 'message' => $this->mysqli->error];
        }
        if ($result->num_rows == 0) {
            return ['status' => 'success', 'message' => 'Movie not reviewed by user'];
        }
        $reviewData = $result->fetch_assoc();
        return ['status' => 'success', 'user_review_data' => $reviewData];
    }
    public function postUserReview($username, $movie_id, $rating, $review)
    {
        $username = $this->mysqli->real_escape_string($username);
        $movie_id = $this->mysqli->real_escape_string($movie_id);
        $rating = $this->mysqli->real_escape_string($rating);
        $review = $this->mysqli->real_escape_string($review);
    
        $userQuery = "SELECT id FROM users WHERE username = '$username'";
        $userResult = $this->mysqli->query($userQuery);
        $userData = $userResult->fetch_assoc();
        $user_id = $userData['id'];
    
        try {
            $movieQuery = "SELECT id FROM movies WHERE movie_id = $movie_id";
            $movieResult = $this->mysqli->query($movieQuery);
            
        } catch (mysqli_sql_exception $exception) {
            return ['status' => 'error', 'message' => 'Failed to retrieve movie data: ' . $exception->getMessage()];
        }
        $insertQuery = "INSERT INTO movie_reviews (movie_id, user_id, rating, review) VALUES ($movie_id, $user_id, $rating, '$review')";    
        try {
            $insertResult = $this->mysqli->query($insertQuery);
            return ['status' => 'success', 'message' => 'User review posted'];
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() == 1062) { // MySQL error code for duplicate entry
                return ['status' => 'error', 'message' => 'User has already reviewed this movie'];
            } else {
                return ['status' => 'error', 'message' => 'Failed to post user review: ' . $exception->getMessage()];
            }
        }
    }

	public function getForumDiscussion($username, $movie_id, $message_id, $parent_message_id)
	{
		$username = $this->mysqli->real_escape_string($username);
		$movie_id = $this->mysqli->real_escape_string($movie_id);
		$message_id = $this->mysqli->real_escape_string($message_id);
		$parent_message_id = $this->mysqli->real_escape_string($parent_message_id);
		
		$userQuery = "SELECT id FROM users WHERE username = '$username'";
		$userResult = $this->mysqli->query($userQuery);
		$userData = $userResult->fetch_assoc();
		$user_id = $userData['id'];

		$query = "SELECT * FROM discussion_posts WHERE user_id = $user_id AND movie_id = $movie_id AND message_id = $message_id";
		$result = $this->mysqli->query($query);

		if(!$result) {
			return ['status' => 'error', 'message' => $this->mysqli->error];
		}
		if($result->num_rows ==0) {
			return ['status' => 'success', 'message' => 'No discussion messages by user'];
		}
		$reviewData = $result->fetch_assoc();
		return ['status' => 'success', 'user_discussion_data' => $reviewData];
	}

	public function postForumDiscussion($username, $movie_id, $message_id, $text, $parent_message_id)
	{
		$username = $this->mysqli->real_escape_string($username);
		$movie_id = $this->mysqli->real_escape_string($movie_id);
 		$message_id = $this->mysqli->real_escape_string($message_id);
		$text = $this->mysqli->real_escape_string($text);
		$parent_message_id = $this->mysqli->real_escape_string($parent_message_id);
		
		$userQuery = "SELECT id FROM users WHERE username = '$username'";
		$userResult = $this->mysqli->query($userQuery);
      $userData = $userResult->fetch_assoc();
      $user_id = $userData['id'];

		try {
			$movieQuery = "SELECT id FROM movies WHERE movie_id = $movie_id";
			$movieResult = $this->mysqli->query($movieQuery);

		} catch (mysqli_sql_exception $exception) {
			return ['status' => 'error', 'message' => 'Failed to retrieve movie data: ' . $exception->getMessage()];
		}
		$insertQuery = "INSERT INTO discussion_posts (message_id, movie_id, user_id, parent_message_id, text) VALUES ($message_id, $movie_id, $user_id, $parent_message_id, $text)";
		
		try {
			$insertResult = $this->mysqli->query($insertQuery);
			return ['status' => 'success', 'message' => 'Discussion message posted'];	
		} catch {
			return ['status' => 'error', 'message' => 'Failed to post message: ' . $exception->getMessage()];
		}
	}
}

?>
