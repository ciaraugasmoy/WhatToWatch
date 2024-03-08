<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/global.css">
    <script src="../javascript/template.js"></script>
    <title>Signup Form</title>
</head>
<body>
    <h2>Signup</h2>

    <p id="error-message" style="color: red;"></p>
    <form id="signup-form">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required><br><br>

        <input type="submit" value="signup">
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const signupForm = document.getElementById('signup-form');
            const errorMessage = document.getElementById('error-message');

            signupForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(signupForm);

                fetch('../requests/signup.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Redirect to the specified URL
                        window.location.href = data.redirect;
                    } else {
                        // Display an error message on the same page
                        errorMessage.innerText = data.error || 'Signup failed. Please try again.';
                    }
                })
                .catch(error => {
                    console.error('Error during signup:', error);
                });
            });
        });
    </script>
</body>
</html>
