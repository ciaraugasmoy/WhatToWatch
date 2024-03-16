<?php
class ThreadHandler
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

    public function postThread($username, $title, $body, $movie_id)
    {
        $username = $this->mysqli->real_escape_string($username);
        $title = $this->mysqli->real_escape_string($title);
        $body = $this->mysqli->real_escape_string($body);
        $movie_id = ($movie_id === '') ? 'NULL' : $this->mysqli->real_escape_string($movie_id); // Convert empty string to NULL
    
        // Retrieve user ID
        $userQuery = "SELECT id FROM users WHERE username = '$username'";
        $userResult = $this->mysqli->query($userQuery);
        if (!$userResult) {
            return ['status' => 'error', 'message' => 'Failed to retrieve user data'];
        }
        $userData = $userResult->fetch_assoc();
        $user_id = $userData['id'];
    
        // Insert thread data
        $insertQuery = "INSERT INTO threads (user_id, movie_id, title, body) VALUES ('$user_id', $movie_id, '$title', '$body')";
        if ($this->mysqli->query($insertQuery) === TRUE) {
            return ['status' => 'success', 'message' => 'Thread posted'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to post thread: ' . $this->mysqli->error];
        }
    }

    public function getRecentThreads($offset, $limit, $query)
    {
        try {
            $queryCondition = "";
            if (!empty($query)) {
                // If a non-empty search query is provided, add a condition to search the fulltext
                $escapedQuery = $this->mysqli->real_escape_string($query);
                $queryCondition = " AND MATCH (title, body) AGAINST ('$escapedQuery')";
            }
    
            // Construct the SQL query to get recent threads with optional search condition
            $sql = "SELECT * FROM threads WHERE 1=1 $queryCondition ORDER BY posted_date DESC LIMIT ?, ?";
            
            // Prepare the statement
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new Exception('Failed to prepare statement: ' . $this->mysqli->error);
            }
    
            // Bind parameters and execute the statement
            $stmt->bind_param('ii', $offset, $limit);
            if (!$stmt->execute()) {
                throw new Exception('Failed to execute statement: ' . $stmt->error);
            }
    
            // Get the result
            $result = $stmt->get_result();
            if (!$result) {
                throw new Exception('Failed to get result set: ' . $this->mysqli->error);
            }
    
            // Fetch the rows
            $threads = $result->fetch_all(MYSQLI_ASSOC);
    
            // Close statement
            $stmt->close();
    
            // Check if no rows returned
            if (count($threads) === 0) {
                return ['status' => 'error', 'message' => 'No results'];
            }
    
            return ['status' => 'success', 'message' => 'Recent threads fetched', 'threads' => $threads];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    public function getComments($thread_id)
    {
        try {
            // Prepare SQL query to retrieve comments for the given thread_id
            $sql = "SELECT * FROM comments WHERE thread_id = ? ORDER BY posted_date DESC";
            
            // Prepare the statement
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new Exception('Failed to prepare statement: ' . $this->mysqli->error);
            }
    
            // Bind the thread_id parameter and execute the statement
            $stmt->bind_param('i', $thread_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to execute statement: ' . $stmt->error);
            }
    
            // Get the result
            $result = $stmt->get_result();
            if (!$result) {
                throw new Exception('Failed to get result set: ' . $this->mysqli->error);
            }
    
            // Fetch the comments
            $comments = $result->fetch_all(MYSQLI_ASSOC);
    
            // Close statement
            $stmt->close();
    
            // Check if no comments returned
            if (count($comments) === 0) {
                return ['status' => 'error', 'message' => 'No comments yet. Be the first'];
            }
    
            return ['status' => 'success', 'message' => 'Comments fetched successfully', 'comments' => $comments];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    public function postComment($username, $thread_id, $body)
    {
        try {
            // Escape input parameters to prevent SQL injection
            $username = $this->mysqli->real_escape_string($username);
            $thread_id = $this->mysqli->real_escape_string($thread_id);
            $body = $this->mysqli->real_escape_string($body);
            
            // Retrieve user_id based on username
            $userQuery = "SELECT id FROM users WHERE username = ?";
            $stmtUser = $this->mysqli->prepare($userQuery);
            if (!$stmtUser) {
                throw new Exception('Failed to prepare user statement: ' . $this->mysqli->error);
            }
            $stmtUser->bind_param('s', $username);
            if (!$stmtUser->execute()) {
                throw new Exception('Failed to execute user statement: ' . $stmtUser->error);
            }
            $userResult = $stmtUser->get_result();
            if (!$userResult || $userResult->num_rows === 0) {
                throw new Exception('User not found');
            }
            $userData = $userResult->fetch_assoc();
            $user_id = $userData['id'];
            $stmtUser->close();
            
            // Prepare SQL query to insert the comment
            $sql = "INSERT INTO comments (user_id, thread_id, body) VALUES (?, ?, ?)";
            
            // Prepare the statement
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new Exception('Failed to prepare statement: ' . $this->mysqli->error);
            }
            
            // Bind parameters and execute the statement
            $stmt->bind_param('iis', $user_id, $thread_id, $body);
            if (!$stmt->execute()) {
                throw new Exception('Failed to execute statement: ' . $stmt->error);
            }
            
            // Close statement
            $stmt->close();
            
            return ['status' => 'success', 'message' => 'Comment posted successfully'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


}    


?>