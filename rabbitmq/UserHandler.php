<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class UserHandler
{
    private $mysqli; 

    public function __construct()
    {
        echo "attempting to connect to db" . PHP_EOL;
        $mysqli = new mysqli("localhost", "what2watchadmin", "what2watchpassword", "what2watch");
        if ($mysqli->connect_error) {
            echo "failed to connect" . PHP_EOL;
            die("Connection failed: " . $mysqli->connect_error);
        }
        $this->mysqli = $mysqli;
    }

    public function doLogin($username, $password)
    {
        // Sanitize input to prevent SQL injection
        $username = $this->mysqli->real_escape_string($username);

        // Perform login check
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $this->mysqli->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $storedHashedPassword = $row['password'];

            // Use password_verify to check if the entered password matches the stored hashed password
            if (password_verify($password, $storedHashedPassword)) {
                $this->mysqli->close();
                //$jwtTokens = $this->doGenerateTokens($username);
                echo "Login successful for username: $username\n";
                return array("status" => "success", "message" => "Login successful", "tokens" => "placeholder");
            }
        }

        $this->mysqli->close();
        echo "User not found or incorrect password" . PHP_EOL;
        echo "Login failed for username: $username\n";

        return array("status" => "error", "message" => "Login failed");
    }

    public function doSignup($username, $password, $email, $dob)
    {
        // Sanitize input to prevent SQL injection
        $username = $this->mysqli->real_escape_string($username);
        $password = $this->mysqli->real_escape_string($password);
        $email = $this->mysqli->real_escape_string($email); // New field
        $dob = $this->mysqli->real_escape_string($dob); // New field

        // Check if the username or email is already registered
        $checkQuery = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $checkResult = $this->mysqli->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            echo "Username or email already exists" . PHP_EOL;
            $this->mysqli->close();
            return array("status" => "error", "message" => "Username or email already exists");
        }

        // Perform signup
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $signupQuery = "INSERT INTO users (username, password, email, dob) VALUES ('$username', '$hashedPassword', '$email', '$dob')";
        $signupResult = $this->mysqli->query($signupQuery);

        if ($signupResult) {
            echo "User signed up successfully" . PHP_EOL;
            $this->mysqli->close();
            return array("status" => "success", "message" => "Signup successful");
        } else {
            echo "Signup failed" . PHP_EOL;
            $this->mysqli->close();
            return array("status" => "error", "message" => "Signup failed");
        }
    }

}
