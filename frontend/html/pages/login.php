
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <script src="../javascript/template.js"></script>
    <title>Login Form</title>
</head>
<body>
    <h2>Login</h2>
    <p id="error-message" style="color: red;"></p>
    <form id="login-form">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>
    <p>Don't have an account? <a href="signup.php">Sign Up Here</a></p>
    <script>
        document.getElementById('login-form').addEventListener('submit', function (event) {
            event.preventDefault();

            // Get form data
            const formData = new FormData(event.target);

            // Perform a fetch request to login.php
            fetch('login.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Redirect to the success page
                    window.location.href = data.redirect;
                } else {
                    // Display error message
                    document.getElementById('error-message').textContent = data.message;
                }
            })
            .catch(error => console.error('Error:', error));
        });
     
    </script>
</body>
</html>
