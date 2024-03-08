document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('logoutButton').addEventListener('click', function () {
        // Use the Fetch API to send a POST request to the server
        fetch('../requests/logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'logout=true', // Sending the logout parameter
        })
        .then(response => response.json())
        .then(data => {
            // Redirect to index page on successful logout
            if (data.success) {
                window.location.href = '../index.html';
            }
        })
        .catch(error => console.error('Error:', error));
    });
});