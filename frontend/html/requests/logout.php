<?php
    // Destroy cookies
    setcookie('access_token', '', time() - 3600, '/');
    setcookie('refresh_token', '', time() - 3600, '/');
    setcookie('username', '', time() - 3600, '/');

    // Send a JSON response to indicate successful logout
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
?>
