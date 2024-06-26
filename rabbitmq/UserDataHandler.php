<?php

class UserDataHandler
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
    
    public function getWatchProviders($username)
    {
        $username = $this->mysqli->real_escape_string($username);
        $query = "    
                    SELECT wp.provider_id, wp.provider_name, wp.logo_path, wp.display_priority
                    FROM users u
                    JOIN user_watch_providers uwp ON u.id = uwp.user_id
                    JOIN watch_providers wp ON uwp.provider_id = wp.provider_id
                    WHERE u.username = '$username';
            ";
        try{
            $result = $this->mysqli->query($query);
            if ($result->num_rows > 0) {
                $watch_provider_info = array();
                while ($row = $result->fetch_assoc()) {
                    $watch_provider_info[] = $row;
                }
                $this->mysqli->close();
                return array("status" => "success", "message" => "user watch providers", "watch_provider_info" => $watch_provider_info);
            }
            else{
                $this->mysqli->close();
                return array("status" => "error", "message" => "user doesnt have wp");
            }
        }
        catch (Exception $e) {
            $this->mysqli->close();
            return array("status" => "error", "message" => "query failed: ".var_dump($e));
        }  

        $this->mysqli->close();
    }

    public function setWatchProviders($username,$watch_provider_id)
    {
        $username = $this->mysqli->real_escape_string($username);
        $query = "    
                INSERT INTO user_watch_providers(user_id, provider_id) VALUES ((SELECT id FROM users WHERE username = '$username'), '$watch_provider_id')
            ";
        try{
            $this->mysqli->query($query);
            $this->mysqli->close();
            return array("status" => "success", "message" => "watch provider id".$watch_provider_id."inserted");
        }
        catch (Exception $e) {
            $this->mysqli->close();
            if ($e->getCode() === 1062) { // MySQL error code for duplicate entry
                return array("status" => "error", "message" => "Duplicate entry: Watch provider id " . $watch_provider_id);
            } else {
                return array("status" => "error", "message" => "Query failed: " . $e->getMessage());
            }
        }  
    }

    public function unsetWatchProviders($username, $provider_id)
    {
        $username = $this->mysqli->real_escape_string($username);
        $query = "    
                DELETE FROM user_watch_providers WHERE user_id = (SELECT id FROM users WHERE username = '$username') AND provider_id = '$provider_id'
            ";
        try {
            $result = $this->mysqli->query($query);
    
            if ($result === false) {
                throw new Exception("Query failed: " . $this->mysqli->error);
            }
    
            $rowsAffected = $this->mysqli->affected_rows;
    
            $this->mysqli->close();
    
            if ($rowsAffected > 0) {
                return array("status" => "success", "message" => "Watch provider id " . $provider_id . " deleted");
            } else {
                return array("status" => "error", "message" => "Record not found for Watch provider id " . $provider_id);
            }
        } catch (Exception $e) {
            $this->mysqli->close();
            return array("status" => "error", "message" => $e->getMessage());
        }
    }
    
    public function getCuratedWatchProviders($username){
        $query = "
        SELECT cwp.*
        FROM curated_watch_providers cwp
        WHERE NOT EXISTS (
        SELECT 1
        FROM user_watch_providers uwp
        WHERE uwp.provider_id = cwp.provider_id
        AND uwp.user_id = (SELECT id FROM users WHERE username = '$username')
        );
        ";
        try{
            $result = $this->mysqli->query($query);
            if ($result->num_rows > 0) {
                $watch_provider_info = array();
                while ($row = $result->fetch_assoc()) {
                    $watch_provider_info[] = $row;
                }
                $this->mysqli->close();
                return array("status" => "success", "message" => "curated watch providers", "watch_provider_info" => $watch_provider_info);
            }
        }
        catch (Exception $e) {
        $this->mysqli->close();
        return array("status" => "error", "message" => "query failed: ".var_dump($e));
        }  
    }

// FRIEND LIST MANIPULATION
    public function getFriendList($username)
    {
        $username = $this->mysqli->real_escape_string($username);
        $query = "
        SELECT u.id AS friend_id, u.username AS friend_name, 
            CASE
                WHEN f.status = 'accepted' THEN 'accepted'
                WHEN f.receiver_id = (SELECT id FROM users WHERE username = '$username') THEN 'pending'
                WHEN f.sender_id = (SELECT id FROM users WHERE username = '$username') THEN 'requested'
            END AS status
        FROM users u
        INNER JOIN friends f ON (u.id = f.sender_id OR u.id = f.receiver_id)
        WHERE (f.sender_id = (SELECT id FROM users WHERE username = '$username')
            OR f.receiver_id = (SELECT id FROM users WHERE username = '$username'))
            AND u.id != (SELECT id FROM users WHERE username = '$username')
        ORDER BY 
            CASE 
                WHEN status = 'pending' THEN 0 -- Set pending status as first priority
                WHEN status = 'requested' THEN 1
                WHEN status = 'accepted' THEN 2
            END,
            status,
            friend_name; -- Then sort alphabetically by friend's name
        ";
        
        

        try {
            $result = $this->mysqli->query($query);

            if ($result->num_rows > 0) {
                $friend_list = array();

                while ($row = $result->fetch_assoc()) {
                    $friend_list[] = $row;
                }

                $this->mysqli->close();
                return array("status" => "success", "message" => "Friend list fetched successfully", "friend_list" => $friend_list);
            } else {
                $this->mysqli->close();
                return array("status" => "error", "message" => "No friends found for the user");
            }
        } catch (Exception $e) {
            $this->mysqli->close();
            return array("status" => "error", "message" => "Query failed: " . $e->getMessage());
        }
    }

    public function sendFriendRequest($senderUsername, $receiverUsername)
    {
        $senderUsername = $this->mysqli->real_escape_string($senderUsername);
        $receiverUsername = $this->mysqli->real_escape_string($receiverUsername);
    
        // Get sender and receiver IDs
        $senderIdQuery = "SELECT id FROM users WHERE username = '$senderUsername'";
        $receiverIdQuery = "SELECT id FROM users WHERE username = '$receiverUsername'";
    
        try {
            $senderResult = $this->mysqli->query($senderIdQuery);
            $receiverResult = $this->mysqli->query($receiverIdQuery);
    
            if ($senderResult->num_rows == 1 && $receiverResult->num_rows == 1) {
                $senderId = $senderResult->fetch_assoc()['id'];
                $receiverId = $receiverResult->fetch_assoc()['id'];
    
                // Check if sender and receiver are the same user
                if ($senderId != $receiverId) {
                    // Check if the friendship already exists
                    $existingFriendshipQuery = "
                        SELECT id FROM friends
                        WHERE (sender_id = $senderId AND receiver_id = $receiverId)
                        OR (sender_id = $receiverId AND receiver_id = $senderId)
                    ";
    
                    $existingFriendshipResult = $this->mysqli->query($existingFriendshipQuery);
    
                    if ($existingFriendshipResult->num_rows == 0) {
                        // Create friend request
                        $insertQuery = "
                            INSERT INTO friends (sender_id, receiver_id, status)
                            VALUES ($senderId, $receiverId, 'pending')
                        ";
    
                        $this->mysqli->query($insertQuery);
                        $this->mysqli->close();
    
                        return array("status" => "success", "message" => "Friend request sent successfully");
                    } else {
                        $this->mysqli->close();
                        return array("status" => "error", "message" => "Friend request already exists");
                    }
                } else {
                    $this->mysqli->close();
                    return array("status" => "error", "message" => "Cannot send friend request to yourself");
                }
            } else {
                $this->mysqli->close();
                return array("status" => "error", "message" => "Invalid sender or receiver username");
            }
        } catch (Exception $e) {
            $this->mysqli->close();
            return array("status" => "error", "message" => "Query failed: " . $e->getMessage());
        }
    }

    public function acceptFriendRequest($receiverUsername, $senderUsername)
    {
        $receiverUsername = $this->mysqli->real_escape_string($receiverUsername);
        $senderUsername = $this->mysqli->real_escape_string($senderUsername);

        // Get sender and receiver IDs
        $receiverIdQuery = "SELECT id FROM users WHERE username = '$receiverUsername'";
        $senderIdQuery = "SELECT id FROM users WHERE username = '$senderUsername'";

        try {
            $receiverResult = $this->mysqli->query($receiverIdQuery);
            $senderResult = $this->mysqli->query($senderIdQuery);

            if ($receiverResult->num_rows == 1 && $senderResult->num_rows == 1) {
                $receiverId = $receiverResult->fetch_assoc()['id'];
                $senderId = $senderResult->fetch_assoc()['id'];

                // Update the friendship status to 'accepted'
                $updateQuery = "
                    UPDATE friends
                    SET status = 'accepted'
                    WHERE (sender_id = $senderId AND receiver_id = $receiverId)
                    AND status = 'pending';
                ";

                $this->mysqli->query($updateQuery);

                $this->mysqli->close();
                return array("status" => "success", "message" => "Friend request accepted successfully");
            } else {
                $this->mysqli->close();
                return array("status" => "error", "message" => "Invalid sender or receiver username");
            }
        } catch (Exception $e) {
            $this->mysqli->close();
            return array("status" => "error", "message" => "Query failed: " . $e->getMessage());
        }
    }
    public function deleteFriend($user1, $user2)
    {
        $user1 = $this->mysqli->real_escape_string($user1);
        $user2 = $this->mysqli->real_escape_string($user2);

        // Get user IDs
        $user1IdQuery = "SELECT id FROM users WHERE username = '$user1'";
        $user2IdQuery = "SELECT id FROM users WHERE username = '$user2'";

        try {
            $user1Result = $this->mysqli->query($user1IdQuery);
            $user2Result = $this->mysqli->query($user2IdQuery);

            if ($user1Result->num_rows == 1 && $user2Result->num_rows == 1) {
                $user1Id = $user1Result->fetch_assoc()['id'];
                $user2Id = $user2Result->fetch_assoc()['id'];

                // Delete the friendship record
                $deleteQuery = "
                    DELETE FROM friends
                    WHERE (sender_id = $user1Id AND receiver_id = $user2Id)
                    OR (sender_id = $user2Id AND receiver_id = $user1Id);
                ";

                $this->mysqli->query($deleteQuery);

                $this->mysqli->close();
                return array("status" => "success", "message" => "Friendship deleted successfully");
            } else {
                $this->mysqli->close();
                return array("status" => "error", "message" => "Invalid user1 or user2 username");
            }
        } catch (Exception $e) {
            $this->mysqli->close();
            return array("status" => "error", "message" => "Query failed: " . $e->getMessage());
        }
    }
//WATCHLIST FEATURE
    public function getWatchlist($username)
    {
        $username = $this->mysqli->real_escape_string($username);
        $query = "
            SELECT m.movie_id, m.title, m.overview, m.release_date, m.poster_path, m.backdrop_path, m.adult
            FROM users u
            JOIN watchlist w ON u.id = w.user_id
            JOIN movies m ON w.movie_id = m.movie_id
            WHERE u.username = '$username';
        ";

        try {
            $result = $this->mysqli->query($query);

            if ($result === false) {
                throw new Exception("Query failed: " . $this->mysqli->error);
            }

            $movie_data = array();
            while ($row = $result->fetch_assoc()) {
                $movie_data[] = $row;
            }
            $this->mysqli->close();

            return array("status" => "success", "movies" => $movie_data);
        } catch (Exception $e) {
            $this->mysqli->close();
            return array("status" => "error", "message" => $e->getMessage());
        }
    }

    public function addToWatchlist($username, $movie_id)
    {
        try {
            $username = $this->mysqli->real_escape_string($username);
            $movie_id = $movie_id; 
            $userQuery = "SELECT id FROM users WHERE username = '$username';";
            $movieQuery = "SELECT movie_id FROM movies WHERE movie_id = $movie_id;";

            $userResult = $this->mysqli->query($userQuery);
            $movieResult = $this->mysqli->query($movieQuery);

            if ($userResult->num_rows === 0) {
                $this->mysqli->close();
                return array("status" => "error", "message" => "User not found");
            }

            if ($movieResult->num_rows === 0) {
                $this->mysqli->close();
                return array("status" => "error", "message" => "Movie not found");
            }

            // Insert into watchlist
            $insertQuery = "
                INSERT INTO watchlist (user_id, movie_id)
                SELECT id, $movie_id
                FROM users
                WHERE username = '$username';
            ";

        
            $this->mysqli->query($insertQuery);

            if ($this->mysqli->affected_rows > 0) {
                $this->mysqli->close();
                return array("status" => "success", "message" => "Movie added to watchlist");
            } else {
                $this->mysqli->close();
                return array("status" => "error", "message" => "Failed to add movie to watchlist");
            }
        } catch (Exception $e) {
            $this->mysqli->close();
            return array("status" => "error", "message" => "Query failed: " . $e->getMessage());
        }
    }
    public function removeFromWatchlist($username, $movie_id)
    {
        try {
            $username = $this->mysqli->real_escape_string($username);
            $movie_id = $movie_id; 
            $userQuery = "SELECT id FROM users WHERE username = '$username';";
            $movieQuery = "SELECT movie_id FROM movies WHERE movie_id = $movie_id;";
    
            $userResult = $this->mysqli->query($userQuery);
            $movieResult = $this->mysqli->query($movieQuery);
    
            if ($userResult->num_rows === 0) {
                $this->mysqli->close();
                return array("status" => "error", "message" => "User not found");
            }
    
            if ($movieResult->num_rows === 0) {
                $this->mysqli->close();
                return array("status" => "error", "message" => "Movie not found");
            }
    
            // Remove from watchlist
            $deleteQuery = "
                DELETE FROM watchlist
                WHERE user_id = (
                    SELECT id
                    FROM users
                    WHERE username = '$username'
                )
                AND movie_id = $movie_id;
            ";
    
            $this->mysqli->query($deleteQuery);
    
            if ($this->mysqli->affected_rows > 0) {
                $this->mysqli->close();
                return array("status" => "success", "message" => "Movie removed from watchlist");
            } else {
                $this->mysqli->close();
                return array("status" => "error", "message" => "Failed to remove movie from watchlist");
            }
        } catch (Exception $e) {
            $this->mysqli->close();
            return array("status" => "error", "message" => "Query failed: " . $e->getMessage());
        }
    }
    

}