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
            return ['status' => 'error', 'message' => 'Movie not reviewed by user'];
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
    public function getRecentReviews($username, $movie_id, $limit, $offset)
    {
        try {
            $username = $this->mysqli->real_escape_string($username);
            $movie_id = $this->mysqli->real_escape_string($movie_id);
            $limit = (int)$limit; // Cast to integer for security
            $offset = (int)$offset; // Cast to integer for security

            // Subquery to get user_id for the given username
            $userQuery = "SELECT id FROM users WHERE username = '$username'";
            $userResult = $this->mysqli->query($userQuery);

            if (!$userResult || $userResult->num_rows === 0) {
                throw new Exception("User '$username' not found");
            }

            $userData = $userResult->fetch_assoc();
            $user_id = $userData['id'];

            // Query to fetch recent reviews excluding the given user's review if they have one
            $query = "SELECT movie_reviews.*, users.username 
                    FROM movie_reviews 
                    INNER JOIN users ON movie_reviews.user_id = users.id
                    WHERE movie_reviews.movie_id = $movie_id 
                    AND movie_reviews.user_id != $user_id
                    ORDER BY movie_reviews.created_at DESC
                    LIMIT $limit OFFSET $offset";

            $result = $this->mysqli->query($query);

            if (!$result) {
                throw new Exception($this->mysqli->error);
            }

            $reviews = [];
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }

            if (empty($reviews)) {
                return ['status' => 'error', 'message' => 'No reviews'];
            }

            return ['status' => 'success', 'reviews' => $reviews];
        }catch (Exception $e) {
            return ['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }
    public function getFriendReviews($username, $friend_name)
    {
        try {
            $username = $this->mysqli->real_escape_string($username);
            $friend_name = $this->mysqli->real_escape_string($friend_name);
    
            // Check if the user is querying their own reviews
            $userQuery = "SELECT id FROM users WHERE username = '$username'";
            $userResult = $this->mysqli->query($userQuery);
            $userData = $userResult->fetch_assoc();
            $user_id = $userData['id'];
    
            // Retrieve the friend's id based on the username
            $friendQuery = "SELECT id FROM users WHERE username = '$friend_name'";
            $friendResult = $this->mysqli->query($friendQuery);
            $friendData = $friendResult->fetch_assoc();
            $friend_id = $friendData['id'];
    
            // Check if the user and friend are the same
            if ($user_id == $friend_id) {
                // Fetch review data for the user
                $reviewQuery = "SELECT movie_reviews.*, movies.title AS movie_title, movies.poster_path AS movie_poster_path
                                FROM movie_reviews 
                                INNER JOIN movies ON movie_reviews.movie_id = movies.movie_id
                                WHERE movie_reviews.user_id = $user_id";
    
                $reviewResult = $this->mysqli->query($reviewQuery);
    
                $reviews = [];
                while ($row = $reviewResult->fetch_assoc()) {
                    $reviews[] = $row;
                }
    
                if (empty($reviews)) {
                    return ['status' => 'error', 'message' => 'No reviews found'];
                }
    
                return ['status' => 'success', 'reviews' => $reviews];
            }
    
            // Check if the friendship status is accepted
            $friendshipQuery = "SELECT status FROM friends 
                                WHERE ((sender_id = $user_id AND receiver_id = $friend_id) 
                                OR 
                                (sender_id = $friend_id AND receiver_id = $user_id)) 
                                AND status = 'accepted'";
    
            $friendshipResult = $this->mysqli->query($friendshipQuery);
    
            if ($friendshipResult->num_rows === 0) {
                return ['status' => 'error', 'message' => "You're not friends yet"];
            }
    
            // Fetch review data along with movie details
            $reviewQuery = "SELECT movie_reviews.*, movies.title AS movie_title, movies.poster_path AS movie_poster_path
                            FROM movie_reviews 
                            INNER JOIN movies ON movie_reviews.movie_id = movies.movie_id
                            WHERE movie_reviews.user_id = $friend_id";
    
            $reviewResult = $this->mysqli->query($reviewQuery);
    
            $reviews = [];
            while ($row = $reviewResult->fetch_assoc()) {
                $reviews[] = $row;
            }
    
            if (empty($reviews)) {
                return ['status' => 'error', 'message' => 'No reviews by your friend'];
            }
    
            return ['status' => 'success', 'reviews' => $reviews];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }
    

}    


?>