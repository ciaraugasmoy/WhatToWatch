<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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
            $userId = $row['id'];
    
            // Use password_verify to check if the entered password matches the stored hashed password
            if (password_verify($password, $storedHashedPassword)) {
                // Check if the user has 2FA enabled
                $query2fa = "SELECT * FROM user_2fa WHERE user_id = $userId";
                $result2fa = $this->mysqli->query($query2fa);
    
                if ($result2fa->num_rows > 0) {
                    echo "User found in 2FA table. Requires 2FA verification" . PHP_EOL;
                    $this->generate2faKey($userId);

                    return array("status" => "2fa", "message" => "Two-factor authentication required");
                }
    
                $jwtTokens = $this->doGenerateTokens($username);
                echo "Login successful for username: $username\n";
                return array("status" => "success", "message" => "Login successful", "tokens" => $jwtTokens);
            }
        }

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

    public function generate2faKey($user_id)
    {
        // Generate a unique temporary key
        $tempKey = substr(bin2hex(random_bytes(16)), 0, 5); // Generate a random hexadecimal string (32 characters)
    
        // Call email2fa function with the generated key
        $this->email2fa($tempKey, $user_id);
    
        // Hash the key
        $tempKeyHash = password_hash($tempKey, PASSWORD_DEFAULT); // Hash 
    
        // Calculate expiration time (3 minutes from now)
        $expiration = date('Y-m-d H:i:s', strtotime('+3 minutes'));
    
        // Insert the temporary key into the database
        $query = "INSERT INTO temporary_keys (user_id, token, purpose, expiration) VALUES ($user_id, '$tempKeyHash', '2fa_setup', '$expiration')";
        if ($this->mysqli->query($query)) {
            $this->mysqli->close();
            return array("status" => "success", "message" => "2FA setup key generated successfully", "key" => $tempKey);
        } else {
            $this->mysqli->close();
            return array("status" => "error", "message" => "Failed to generate 2FA setup key");
        }
    }
    
    public function email2fa($key, $user_id)
    {
        // Read SMTP settings from email_credentials.ini
        $config = parse_ini_file('email_credentials.ini');
        $mail = new PHPMailer(true);
    
        try {
            // Query to fetch user's email
            $query = "SELECT email FROM users WHERE id = $user_id";
            $result = $this->mysqli->query($query);
    
            if (!$result) {
                throw new Exception("Failed to fetch user's email: " . $this->mysqli->error);
            }
    
            // Fetch user's email
            $row = $result->fetch_assoc();
            $email = $row['email'];
    
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
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = '2FA Setup Key for What2Watch.com'; // Subject line
            $mail->Body = 'Your 2FA setup key: <strong>' . $key . '</strong>. This key will expire in 3 minutes.';
    
            // Send the email
            $mail->send();
            echo "Email sent successfully";
        } catch (Exception $e) {
            echo "Failed to send email. Error: {$e->getMessage()}";
        }
    }
    

    public function read2fa($username,$key)
    {
        try{
            // Sanitize input to prevent SQL injection
            $username = $this->mysqli->real_escape_string($username);

            // Perform login check
            $query = "SELECT id FROM users WHERE username = '$username'";
            $result = $this->mysqli->query($query);
            $row = $result->fetch_assoc();
            $user_id = $row['id'];
            // Fetch the most recent 2FA key for the user from the database
            $query = "SELECT token, expiration FROM temporary_keys WHERE user_id = $user_id AND purpose = '2fa_setup' ORDER BY id DESC LIMIT 1";
            $result = $this->mysqli->query($query);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $dbKey = $row['token'];
                $expiration = strtotime($row['expiration']);
                
                // Check if the key matches and is not expired
                if ((password_verify($key, $dbKey)) && $expiration > time()) {
                    $jwtTokens = $this->doGenerateTokens($username);
                    echo "2fa Login successful for username: $username\n";
                    return array("status" => "success", "message" => "Login successful", "tokens" => $jwtTokens);
                } else {
                    return array("status" => "error", "message" => "2FA key does not match or is expired key".$key);
                }
            } else {
                return array("status" => "error", "message" => "No 2FA key found for the user");
            }
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

}
