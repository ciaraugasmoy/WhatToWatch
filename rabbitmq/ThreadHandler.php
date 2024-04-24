<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
            // Retrieve the ID of the inserted thread
            $thread_id = $this->mysqli->insert_id;
            return ['status' => 'success', 'message' => 'Thread posted', 'thread_id' => $thread_id];
        } else {
            return ['status' => 'error', 'message' => 'Failed to post thread: ' . $this->mysqli->error];
        }
    }
    
    public function getThread($thread_id)
    {
        try {
            // Construct the SQL query to get thread information with username
            $sql = "SELECT threads.*, users.username 
                    FROM threads 
                    LEFT JOIN users ON threads.user_id = users.id
                    WHERE threads.id = ?";
        
            // Prepare the statement
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new Exception('Failed to prepare statement: ' . $this->mysqli->error);
            }
        
            // Bind parameter and execute the statement
            $stmt->bind_param('i', $thread_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to execute statement: ' . $stmt->error);
            }
        
            // Get the result
            $result = $stmt->get_result();
            if (!$result) {
                throw new Exception('Failed to get result set: ' . $this->mysqli->error);
            }
        
            // Fetch the row
            $thread = $result->fetch_assoc();
        
            // Close statement
            $stmt->close();
        
            // Check if no row returned
            if (!$thread) {
                return ['status' => 'error', 'message' => 'Thread not found'];
            }
        
            return ['status' => 'success', 'thread' => $thread];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
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
            $sql = "SELECT threads.*, users.username 
                    FROM threads 
                    LEFT JOIN users ON threads.user_id = users.id
                    WHERE 1=1 $queryCondition 
                    ORDER BY posted_date DESC LIMIT ?, ?";
    
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
            // Prepare SQL query to retrieve comments for the given thread_id along with usernames
            $sql = "SELECT comments.*, users.username 
                    FROM comments 
                    JOIN users ON comments.user_id = users.id
                    WHERE thread_id = ? 
                    ORDER BY posted_date DESC";
            
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
            $this->mailSubscribers($thread_id);
            return ['status' => 'success', 'message' => 'Comment posted successfully'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    private function mailSubscribers($thread_id)
    {
    // Retrieve subscribers for the given thread_id
    $subscribersQuery = "SELECT u.email, t.title 
                         FROM subscriptions s
                         JOIN users u ON s.user_id = u.id
                         JOIN threads t ON s.thread_id = t.id
                         WHERE s.thread_id = ?";
    
    $stmtSubscribers = $this->mysqli->prepare($subscribersQuery);
    if (!$stmtSubscribers) {
        throw new Exception('Failed to prepare subscribers statement: ' . $this->mysqli->error);
    }
    
    $stmtSubscribers->bind_param('i', $thread_id);
    if (!$stmtSubscribers->execute()) {
        throw new Exception('Failed to execute subscribers statement: ' . $stmtSubscribers->error);
    }
    
    $subscribersResult = $stmtSubscribers->get_result();
    if (!$subscribersResult || $subscribersResult->num_rows === 0) {
        $stmtSubscribers->close();
        return; // No subscribers found for this thread
    }
    
    // Iterate through subscribers and send email notifications
    while ($subscriber = $subscribersResult->fetch_assoc()) {
        $email = $subscriber['email'];
        $postTitle = $subscriber['title'];
        $this->mailSubscriber($email, $postTitle);
    }
    
    $stmtSubscribers->close();
}

private function mailSubscriber($email, $postTitle)
{
    // Read SMTP settings from email_credentials.ini
    $config = parse_ini_file('email_credentials.ini');

    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['smtp_secure'];
        $mail->Port = $config['port'];

        // Set sender and recipient
        $mail->setFrom($config['username'], 'What2Watch.com'); // Sender's email and name
        $mail->addAddress($email); // Recipient's email
        
        // Email content
        $mail->isHTML(true); // Set email format to plain text
        $mail->Subject = 'New Comments on ' . $postTitle; // Subject line
        $mail->Body = 'Hello, A new comment has been posted on the thread: ' . $postTitle . '. Login at www.what2watch.com to see what they said :)';

        // Send the email
        $mail->send();
        echo "Email sent successfully";
    } catch (Exception $e) {
        echo "Failed to send email. Error: {$mail->ErrorInfo}";
    }
}

//upvote downvote handler
    public function getVote($username, $thread_id)
    {
        try {
            // Construct the SQL query to get the vote for the given user and thread
            $sql = "SELECT vote_type FROM thread_votes 
                    WHERE user_id = (SELECT id FROM users WHERE username = ?)
                    AND thread_id = ?";

            // Prepare the statement
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new Exception('Failed to prepare statement: ' . $this->mysqli->error);
            }

            // Bind parameters and execute the statement
            $stmt->bind_param('si', $username, $thread_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to execute statement: ' . $stmt->error);
            }

            // Get the result
            $result = $stmt->get_result();
            if (!$result) {
                throw new Exception('Failed to get result set: ' . $this->mysqli->error);
            }

            // Fetch the vote type
            $vote = $result->fetch_assoc();

            // Close statement
            $stmt->close();

            // Determine the vote status
            if (!$vote) {
                return ['status' => 'success', 'vote' => 'unset']; // No vote found
            } else {
                return ['status' => 'success', 'vote' => $vote['vote_type']]; // Return 'upvote' or 'downvote'
            }

        } catch (Exception $e) {
            // Handle exceptions
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    public function setVote($username, $thread_id, $vote)
    {
        try {
            // Validate the vote value
            if (!in_array($vote, ['upvote', 'downvote', 'unset'])) {
                throw new Exception('Invalid vote type');
            }
    
            // Begin transaction for atomic operation
            $this->mysqli->begin_transaction();
    
            if ($vote === 'unset') {
                // Delete existing vote if $vote is 'unset'
                $deleteSql = "DELETE FROM thread_votes 
                              WHERE user_id = (SELECT id FROM users WHERE username = ?)
                              AND thread_id = ?";
                $deleteStmt = $this->mysqli->prepare($deleteSql);
                if (!$deleteStmt) {
                    throw new Exception('Failed to prepare delete statement: ' . $this->mysqli->error);
                }
    
                $deleteStmt->bind_param('si', $username, $thread_id);
                if (!$deleteStmt->execute()) {
                    throw new Exception('Failed to delete vote: ' . $deleteStmt->error);
                }
    
                $deleteStmt->close();
            } else {
                // Insert or update vote
                $insertUpdateSql = "INSERT INTO thread_votes (thread_id, user_id, vote_type) 
                                    VALUES (?, (SELECT id FROM users WHERE username = ?), ?)
                                    ON DUPLICATE KEY UPDATE vote_type = VALUES(vote_type)";
    
                $insertUpdateStmt = $this->mysqli->prepare($insertUpdateSql);
                if (!$insertUpdateStmt) {
                    throw new Exception('Failed to prepare insert/update statement: ' . $this->mysqli->error);
                }
    
                $insertUpdateStmt->bind_param('iss', $thread_id, $username, $vote);
                if (!$insertUpdateStmt->execute()) {
                    throw new Exception('Failed to set vote: ' . $insertUpdateStmt->error);
                }
    
                $insertUpdateStmt->close();
            }
    
            // Commit transaction
            $this->mysqli->commit();
    
            return ['status' => 'success'];
    
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->mysqli->rollback();
    
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
 // subscription services
 public function subscribe($username, $thread_id)
{
    try {
        // Begin transaction for atomic operation
        $this->mysqli->begin_transaction();

        // Check if the user is already subscribed to the thread
        $checkSubscriptionSql = "SELECT COUNT(*) as count FROM subscriptions 
                                 WHERE user_id = (SELECT id FROM users WHERE username = ?)
                                 AND thread_id = ?";
        $checkSubscriptionStmt = $this->mysqli->prepare($checkSubscriptionSql);
        if (!$checkSubscriptionStmt) {
            throw new Exception('Failed to prepare select statement: ' . $this->mysqli->error);
        }

        $checkSubscriptionStmt->bind_param('si', $username, $thread_id);
        if (!$checkSubscriptionStmt->execute()) {
            throw new Exception('Failed to check existing subscription: ' . $checkSubscriptionStmt->error);
        }

        $subscriptionResult = $checkSubscriptionStmt->get_result()->fetch_assoc();
        $checkSubscriptionStmt->close();

        if ($subscriptionResult['count'] > 0) {
            throw new Exception('User is already subscribed to this thread');
        }

        // Insert new subscription
        $insertSubscriptionSql = "INSERT INTO subscriptions (user_id, thread_id) 
                                  VALUES ((SELECT id FROM users WHERE username = ?), ?)";
        $insertSubscriptionStmt = $this->mysqli->prepare($insertSubscriptionSql);
        if (!$insertSubscriptionStmt) {
            throw new Exception('Failed to prepare insert statement: ' . $this->mysqli->error);
        }

        $insertSubscriptionStmt->bind_param('si', $username, $thread_id);
        if (!$insertSubscriptionStmt->execute()) {
            throw new Exception('Failed to subscribe user to thread: ' . $insertSubscriptionStmt->error);
        }

        $insertSubscriptionStmt->close();

        // Commit transaction
        $this->mysqli->commit();

        return ['status' => 'success'];

    } catch (Exception $e) {
        // Rollback transaction on error
        $this->mysqli->rollback();

        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

public function unsubscribe($username, $thread_id)
{
    try {
        // Begin transaction for atomic operation
        $this->mysqli->begin_transaction();

        // Check if the user is subscribed to the thread
        $checkSubscriptionSql = "SELECT id FROM subscriptions 
                                 WHERE user_id = (SELECT id FROM users WHERE username = ?)
                                 AND thread_id = ?";
        $checkSubscriptionStmt = $this->mysqli->prepare($checkSubscriptionSql);
        if (!$checkSubscriptionStmt) {
            throw new Exception('Failed to prepare select statement: ' . $this->mysqli->error);
        }

        $checkSubscriptionStmt->bind_param('si', $username, $thread_id);
        if (!$checkSubscriptionStmt->execute()) {
            throw new Exception('Failed to check existing subscription: ' . $checkSubscriptionStmt->error);
        }

        $subscriptionResult = $checkSubscriptionStmt->get_result()->fetch_assoc();
        $checkSubscriptionStmt->close();

        if (!$subscriptionResult) {
            throw new Exception('User is not subscribed to this thread');
        }

        $subscriptionId = $subscriptionResult['id'];

        // Delete the subscription record
        $deleteSubscriptionSql = "DELETE FROM subscriptions WHERE id = ?";
        $deleteSubscriptionStmt = $this->mysqli->prepare($deleteSubscriptionSql);
        if (!$deleteSubscriptionStmt) {
            throw new Exception('Failed to prepare delete statement: ' . $this->mysqli->error);
        }

        $deleteSubscriptionStmt->bind_param('i', $subscriptionId);
        if (!$deleteSubscriptionStmt->execute()) {
            throw new Exception('Failed to unsubscribe user from thread: ' . $deleteSubscriptionStmt->error);
        }

        $deleteSubscriptionStmt->close();

        // Commit transaction
        $this->mysqli->commit();

        return ['status' => 'success'];

    } catch (Exception $e) {
        // Rollback transaction on error
        $this->mysqli->rollback();

        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

    
}    


?>