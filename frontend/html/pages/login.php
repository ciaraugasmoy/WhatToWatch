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
    <?php include '../partials/loginform.php';?>
    <p>Don't have an account? <a href="signup.php">Sign Up Here</a></p>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var loginForm = document.getElementById('signup-form');
            loginForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                // Get form data
                const formData = new FormData(event.target);

                // Perform a fetch request to login.php
                fetch('../requests/login.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Redirect to the success page
                        window.location.href = data.redirect;
                    } else {
                        // Display error message
                        console.log(data.message);
                    }
                })
            });
        });
    </script>
</body>
</html>
