<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class UserHandler
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
                $jwtTokens = $this->doGenerateTokens($username);
                echo "Login successful for username: $username\n";
                $this->mysqli->close();
                return array("status" => "success", "message" => "Login successful", "tokens" => $jwtTokens);
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
        $email = $this->mysqli->real_escape_string($email); 
        $dob = $this->mysqli->real_escape_string($dob); 

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
    public function doValidate($username, $tokens)
    {
        try {
            $accessToken = $tokens['access_token'];
            $secretKey = $this->getSecretKey($username);
            // Decode the access token
            $decodedAccessToken = JWT::decode($accessToken, new Key($secretKey, 'HS256'));

            // Check if the access token is expired
            $currentTimestamp = time();
            if ($decodedAccessToken->exp < $currentTimestamp) {
                return array("status" => "error", "message" => "Access token has expired");
            }

            return array("status" => "success", "message" => "Token validation successful");
        } catch (Exception $e) {
            // Token decoding failed, or other exception occurred
            return array("status" => "error", "message" => "Token validation failed");
        }
    }

    private function doGenerateTokens($username)
    {
        $existingSecretKey = $this->getSecretKey($username);
        if (empty($existingSecretKey)) {
            $newSecretKey = $this->generateSecretKey($username);
        } else {
            $newSecretKey = $existingSecretKey;
        }
        $tokens = $this->generateTokens($username);
        return $tokens;
    }
    private function generateSecretKey($username)
    {
        // Check if the user already has a secret key
        $checkQuery = "SELECT private_key FROM private_keys WHERE user_id = (SELECT id FROM users WHERE username = '$username')";
        $result = $this->mysqli->query($checkQuery);
    
        if ($result->num_rows > 0) {
            // User already has a secret key, retrieve and return it
            $row = $result->fetch_assoc();
            $existingSecretKey = $row['private_key'];
            return $existingSecretKey;
        }
    
        // Generate a new secret key
        $newSecretKey = bin2hex(random_bytes(32));
        $username = $this->mysqli->real_escape_string($username);
        $newSecretKey = $this->mysqli->real_escape_string($newSecretKey);
    
        // Insert the new secret key into the database
        $insertQuery = "INSERT INTO private_keys (user_id, private_key) VALUES ((SELECT id FROM users WHERE username = '$username'), '$newSecretKey')";
        $this->mysqli->query($insertQuery);
        return $newSecretKey;
    }

    private function getSecretKey($username)
    {
        $username = $this->mysqli->real_escape_string($username);
        $query = "SELECT private_key FROM private_keys WHERE user_id = (SELECT id FROM users WHERE username = '$username')";
        $result = $this->mysqli->query($query);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo 'the secret key returned is'.$row['private_key'].PHP_EOL;
            return $row['private_key'];
        }
        return null;
    }

    private function generateTokens($username) {
        $secretKey = $this->getSecretKey($username);
        // payload of the access token
        $issuedAt = time();
        $accessTokenExpiration = $issuedAt + 3600; // Access token validity (1 hour)
        $refreshTokenExpiration = $issuedAt + (7 * 24 * 3600); // Refresh token validity (7 days)
    
        $accessTokenPayload = [
            //"iss" => "https://what2watch.com",
            "iat" => $issuedAt,
            "exp" => $accessTokenExpiration,
            "username" => $username
        ];
        $accessToken = JWT::encode($accessTokenPayload, $secretKey, 'HS256');
        
        $refreshTokenPayload = [
           // "iss" => "https://what2watch.com",
            "iat" => $issuedAt,
            "exp" => $refreshTokenExpiration,
            "username" => $username
        ];
        $refreshToken = JWT::encode($refreshTokenPayload, $secretKey, 'HS256');
        return [
            "access_token" => $accessToken,
            "refresh_token" => $refreshToken,
            "access_token_expiration" => $accessTokenExpiration
        ];
    }
}
