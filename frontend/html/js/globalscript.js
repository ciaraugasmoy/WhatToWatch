function logoutHandler(event) {
    event.preventDefault(); // Prevent the default behavior of the link

    // Perform the logout functionality
    fetch('../requests/logout.php', {
        method: 'GET',
        credentials: 'same-origin' // Include cookies in the request
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // If the logout was successful, redirect to index.html
            window.location.href = '../index.html';
        } else {
            // Handle unsuccessful logout
            console.error('Logout failed.');
        }
    });
}


//VALIDATION FUNCTION
document.addEventListener('DOMContentLoaded', function () {
    // Function to handle token validation
    function validateToken() {
        fetch('../requests/validate_token.php', {
            method: 'POST',
        })
        .then(response => response.json())
        .then(data => {
            // Redirect to the index page if success is not true
            if (!data.success) {
                window.location.href = '../index.html';
            }
        })
    }
    // Call the function on page load
    validateToken();
});