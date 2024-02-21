<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("rabbitMQDB.ini", "testServer");

// Retrieve tokens from cookies
$access_token = isset($_COOKIE['access_token']) ? $_COOKIE['access_token'] : '';
$refresh_token = isset($_COOKIE['refresh_token']) ? $_COOKIE['refresh_token'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ismytokenvalid'])) {
    $request = array();
    $request['type'] = "validate";
    $request['tokens']['access_token'] = $access_token;
    $request['tokens']['refresh_token'] = $refresh_token;

    $response = $client->send_request($request);

    if (isset($response['status']) && $response['status'] == 'success') {
        $validationMessage = "Token is valid!";
    } else {
        $validationMessage = "Token is not valid. Please log in again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Token Validation</title>
</head>
<body>

    <h2>Token Validation</h2>

    <?php
    if (isset($validationMessage)) {
        echo '<p style="color: ' . ($response['status'] == 'success' ? 'green' : 'red') . ';">' . $validationMessage . '</p>';
    }
    ?>

    <form action="" method="post">
        <input type="submit" name="ismytokenvalid" value="Check Token Validity">
    </form>

</body>
</html>
