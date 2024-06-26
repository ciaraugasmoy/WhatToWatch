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
        try {
            $movie_id = $this->mysqli->real_escape_string($movie_id);
            $query = "SELECT * FROM movies WHERE movie_id = $movie_id";
            $result = $this->mysqli->query($query);
    
            if (!$result) {
                throw new Exception("Error executing query: " . $this->mysqli->error);
            }
    
            $movieData = $result->fetch_assoc();
            $this->mysqli->close();
    
            return ['status' => 'success', 'message' => 'Movie data found', 'movie' => $movieData];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    public function getMovieDetailsPersonal($movie_id, $username)
    {
        try {
            $movie_id = $this->mysqli->real_escape_string($movie_id);
            $username = $this->mysqli->real_escape_string($username);

            // Query to check if movie_id exists in user's watchlist
            $watchlistQuery = "
                SELECT COUNT(*) AS inWatchlist
                FROM users u
                JOIN watchlist w ON u.id = w.user_id
                WHERE u.username = '$username' AND w.movie_id = $movie_id
            ";

            // Query to fetch movie details
            $movieQuery = "
                SELECT m.*, IFNULL(w.user_id, 0) AS inWatchlist
                FROM movies m
                LEFT JOIN (
                    SELECT user_id, movie_id
                    FROM users u
                    JOIN watchlist w ON u.id = w.user_id
                    WHERE u.username = '$username'
                ) w ON m.movie_id = w.movie_id
                WHERE m.movie_id = $movie_id
            ";

            $watchlistResult = $this->mysqli->query($watchlistQuery);
            $movieResult = $this->mysqli->query($movieQuery);

            if (!$watchlistResult || !$movieResult) {
                throw new Exception("Error executing query: " . $this->mysqli->error);
            }

            $inWatchlistData = $watchlistResult->fetch_assoc();
            $movieData = $movieResult->fetch_assoc();

            // Determine inWatchlist value based on the result of the watchlist query
            $inWatchlist = ($inWatchlistData['inWatchlist'] > 0) ? true : false;

            $this->mysqli->close();

            // Add inWatchlist status to the movie data
            $movieData['inWatchlist'] = $inWatchlist;

            return ['status' => 'success', 'movie' => $movieData];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    

    public function getUserReview($username, $movie_id)
    {
        try {
            // Sanitize inputs and prepare SQL statements
            $username = $this->mysqli->real_escape_string($username);
            $movie_id = $this->mysqli->real_escape_string($movie_id);
    
            // Retrieve user ID based on username
            $userQuery = "SELECT id FROM users WHERE username = ?";
            $stmt = $this->mysqli->prepare($userQuery);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $userResult = $stmt->get_result();
    
            if (!$userResult) {
                throw new Exception('Error finding user');
            }
    
            $userData = $userResult->fetch_assoc();
    
            if (!$userData) {
                throw new Exception('User not found');
            }
    
            $user_id = $userData['id'];
    
            // Retrieve review data based on user ID and movie ID
            $query = "SELECT * FROM movie_reviews WHERE user_id = ? AND movie_id = ?";
            $stmt = $this->mysqli->prepare($query);
            $stmt->bind_param("ii", $user_id, $movie_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if (!$result) {
                throw new Exception('Error fetching review data: ' . $this->mysqli->error);
            }
    
            if ($result->num_rows == 0) {
                throw new Exception('Movie not reviewed by user');
            }

            $reviewData = $result->fetch_assoc();
    
            return ['status' => 'success', 'user_review_data' => $reviewData];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
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
            $limit = $this->mysqli->real_escape_string($limit);
            $offset = $this->mysqli->real_escape_string($offset);


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