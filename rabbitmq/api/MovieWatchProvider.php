#!/usr/bin/php
<?php

class MovieWatchProvider {
    private $apiToken;
    private $mysqli;

    public function __construct() {
        // Read API token from config.ini
        $config = parse_ini_file('config.ini');
        $this->apiToken = $config['api_token'];

        $credentials = parse_ini_file('credentials.ini', true)['database'];
        $this->mysqli = new mysqli(
            $credentials['host'],
            $credentials['username'],
            $credentials['password'],
            $credentials['database']
        );
        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }

    public function getProviders($username, $movieId) {
        $ch = curl_init();
        $url = 'https://api.themoviedb.org/3/movie/' . $movieId . '/watch/providers';
        $headers = [
            'Authorization: Bearer ' . $this->apiToken,
            'Accept: application/json',
        ];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
            exit;
        }
        curl_close($ch);
        $response = json_decode($result, true);

        if (isset($response['results'])) {
            $results = $response['results'];
            $providers=array();
            foreach ($results as $country => $countrydata) {
                if ($country === 'US') {
                    if (isset($countrydata['buy'])) {
                        foreach ($countrydata['buy'] as $provider) {
                            $providerId = $provider['provider_id'];
                            $providerName = $provider['provider_name'];
                            $logoPath = $provider['logo_path'];
                            $displayPriority = $provider['display_priority'];
                            $pricing='buy';
                            $userHas=$this->hasWatchProvider($username, $providerId);
                            $providers[] = array(
                                'provider_id' => $providerId,
                                'provider_name' => $providerName,
                                'logo_path' => $logoPath,
                                'display_priority' => $displayPriority,
                                'pricing' => $pricing,
                                'user_has' => $userHas,
                            );
                        }
                    }
                    if (isset($countrydata['flatrate'])) {
                        foreach ($countrydata['flatrate'] as $provider) {
                            $providerId = $provider['provider_id'];
                            $providerName = $provider['provider_name'];
                            $logoPath = $provider['logo_path'];
                            $displayPriority = $provider['display_priority'];
                            $pricing='flatrate';
                            $userHas=$this->hasWatchProvider($username, $providerId);
                            $providers[] = array(
                                'provider_id' => $providerId,
                                'provider_name' => $providerName,
                                'logo_path' => $logoPath,
                                'display_priority' => $displayPriority,
                                'pricing' => $pricing,
                                'user_has' => $userHas,
                            );
                        }
                    }
                    if (isset($countrydata['rent'])) {
                        foreach ($countrydata['rent'] as $provider) {
                            $providerId = $provider['provider_id'];
                            $providerName = $provider['provider_name'];
                            $logoPath = $provider['logo_path'];
                            $displayPriority = $provider['display_priority'];
                            $pricing='rent';
                            $userHas=$this->hasWatchProvider($username, $providerId);
                            $providers[] = array(
                                'provider_id' => $providerId,
                                'provider_name' => $providerName,
                                'logo_path' => $logoPath,
                                'display_priority' => $displayPriority,
                                'pricing' => $pricing,
                                'user_has' => $userHas,
                            );
                            
                        }
                    }
                    return array("status" => "success", "message" => "got wp for movie in US", "providers"=>$providers);
                }
            }
            return array("status" => "error", "message" => "no us results or err other");
        } else {
            return array("status" => "error", "message" => "no results set");
        }
    }
    private function hasWatchProvider($username, $providerId) {
        $username = $this->mysqli->real_escape_string($username);
        $providerId = intval($providerId);

        $query = "SELECT 1 FROM user_watch_providers WHERE user_id = (SELECT id FROM users WHERE username = '$username') AND provider_id = $providerId";
        $result = $this->mysqli->query($query);

        if ($result && $result->num_rows > 0) {
            // Provider exists for the user
            return true;
        } else {
            // Provider doesn't exist for the user
            return false;
        }
    }
    
}

?>
