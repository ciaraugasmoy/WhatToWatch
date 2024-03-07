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
                $watch_provider_info = $result->fetch_assoc();
                return array("status" => "success", "message" => "user watch providers", $watch_provider_info);
            }
            else{
                return array("status" => "success", "message" => "user doesnt have wp");
            }
        }
        catch (Exception $e) {
            return array("status" => "error", "message" => "query failed: ".var_dump($e));
        }  

        $this->mysqli->close();
    }

}