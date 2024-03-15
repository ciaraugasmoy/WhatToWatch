#!/usr/bin/php
<?php

class SearchHandler {
    private $token;
    
    public function __construct() {
        // Read API token from config.ini
        $config = parse_ini_file('config.ini');
        $this->token = $config['api_token'];
    }
    
    public function performRatingSearch($sort_order) {
   	// Sort order can either be: ASC or DESC
      	
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
	
	if($sort_order=="ASC"){
		$sql = "SELECT * FROM movies ORDER BY vote_average asc;";
	}
	if($sort_order=="DESC"){
		$sql = "SELECT * FROM movies ORDER BY vote_average desc;";
	}
    
  	// Execute the SQL statement


       try{
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $sorted_result = array();
                while ($row = $result->fetch_assoc()) {
                    $sorted_result[] = $row;
                }
                $conn->close();
                return array("status" => "success", "message" => "sorted movie popularity", "sorted_result" => $sorted_result);
            }
            else{
                $conn->close();
                return array("status" => "error", "message" => "no movies to sort?");
            }
        }
        catch (Exception $e) {
            $conn->close();
            return array("status" => "error", "message" => "query failed: ".var_dump($e));
        }  

        $conn->close();
    }
}

?>
